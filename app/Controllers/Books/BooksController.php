<?php

namespace App\Controllers\Books;

use App\Models\BookItemModel;
use App\Models\BookModel;
use App\Models\BookStockModel;
use App\Models\CategoryModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use App\Models\RackModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;

class BooksController extends ResourceController
{
    protected BookModel $bookModel;
    protected CategoryModel $categoryModel;
    protected RackModel $rackModel;
    protected BookStockModel $bookStockModel;
    protected LoanModel $loanModel;
    protected BookItemModel $bookItemModel;
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->bookModel = new BookModel;
        $this->categoryModel = new CategoryModel;
        $this->rackModel = new RackModel;
        $this->bookStockModel = new BookStockModel;
        $this->loanModel = new LoanModel;
        $this->bookItemModel = new BookItemModel;
        $this->memberModel = new MemberModel;

        helper('upload');
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $itemPerPage = 20;

        $title = $this->request->getGet('title');
        $author = $this->request->getGet('author');
        $categoryId = $this->request->getGet('category_id');
        $rackId = $this->request->getGet('rack_id');

        $query = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT');

        if (!empty($title)) {
            $query->like('books.title', $title, insensitiveSearch: true);
        }
        if (!empty($author)) {
            $query->like('books.author', $author, insensitiveSearch: true);
        }
        if (!empty($categoryId)) {
            $query->where('books.category_id', $categoryId);
        }
        if (!empty($rackId)) {
            $query->where('books.rack_id', $rackId);
        }

        // Keep fallback support for basic search if present
        if ($this->request->getGet('search')) {
            $keyword = $this->request->getGet('search');
            $query->groupStart()
                ->like('books.title', $keyword, insensitiveSearch: true)
                ->orLike('books.author', $keyword, insensitiveSearch: true)
                ->orLike('books.publisher', $keyword, insensitiveSearch: true)
                ->orLike('books.isbn', $keyword, insensitiveSearch: true)
                ->groupEnd();
        }

        $books = $query->paginate($itemPerPage, 'books');

        $categories = $this->categoryModel->findAll();
        $racks = $this->rackModel->findAll();

        $data = [
            'books'         => $books,
            'pager'         => $this->bookModel->pager,
            'currentPage'   => $this->request->getVar('page_books') ?? 1,
            'itemPerPage'   => $itemPerPage,
            'title'         => $title,
            'author'        => $author,
            'categoryId'    => $categoryId,
            'rackId'        => $rackId,
            'categories'    => $categories,
            'racks'         => $racks,
            'search'        => $this->request->getGet('search')
        ];

        return view('books/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($slug = null)
    {
        $book = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('slug', $slug)->first();

        if (empty($book)) {
            throw new PageNotFoundException('Book with slug \'' . $slug . '\' not found');
        }

        $loans = $this->loanModel->where([
            'book_id' => $book['id'],
            'return_date' => null
        ])->findAll();

        $loanCount = array_reduce(
            array_map(function ($loan) {
                return $loan['quantity'];
            }, $loans),
            function ($carry, $item) {
                return ($carry + $item);
            }
        );

        $bookStock = $book['quantity'] - $loanCount;

        $items = $this->bookItemModel
            ->select('book_items.*, racks.name as rack_name, racks.floor as rack_floor, members.first_name as donor_first_name, members.last_name as donor_last_name')
            ->join('racks', 'book_items.rack_id = racks.id', 'LEFT')
            ->join('members', 'book_items.donated_by_member_id = members.id', 'LEFT')
            ->where('book_id', $book['id'])
            ->findAll();

        if (empty($items) && $book['quantity'] > 0) {
            $this->bookItemModel->generateItemsForBook($book['id'], (int)$book['quantity']);
            $items = $this->bookItemModel
                ->select('book_items.*, racks.name as rack_name, racks.floor as rack_floor, members.first_name as donor_first_name, members.last_name as donor_last_name')
                ->join('racks', 'book_items.rack_id = racks.id', 'LEFT')
                ->join('members', 'book_items.donated_by_member_id = members.id', 'LEFT')
                ->where('book_id', $book['id'])
                ->findAll();
        }

        $racks = $this->rackModel->findAll();
        $allMembers = $this->memberModel->findAll();

        $reservationModel = new \App\Models\BookReservationModel();
        $activeReservations = $reservationModel->getActiveReservationsForBook($book['id']);

        foreach ($activeReservations as &$res) {
            $res['tier'] = \App\Models\MemberModel::getTierDetails([
                'donated_books_count' => $res['donated_books_count'] ?? 0,
                'manual_tier'         => $res['manual_tier'] ?? 'none',
            ]);
        }

        $activeLoansDetail = $this->loanModel
            ->select('loans.*, members.first_name, members.last_name, members.email, members.uid as member_uid, book_items.item_code')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->where('loans.book_id', $book['id'])
            ->where('loans.return_date', null)
            ->findAll();

        $conditionLogModel = new \App\Models\BookItemConditionLogModel();
        $itemLogs = [];
        foreach ($items as $it) {
            $itemLogs[$it['id']] = $conditionLogModel->getLogsByItem($it['id']);
        }

        $data = [
            'book'               => $book,
            'loanCount'          => $loanCount ?? 0,
            'bookStock'          => $bookStock,
            'items'              => $items,
            'itemLogs'           => $itemLogs,
            'racks'              => $racks,
            'allMembers'         => $allMembers,
            'activeReservations' => $activeReservations,
            'activeLoansDetail'  => $activeLoansDetail,
        ];

        return view('books/show', $data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        $categories = $this->categoryModel->findAll();
        $racks = $this->rackModel->findAll();
        $authorModel = new \App\Models\AuthorModel();
        $publisherModel = new \App\Models\PublisherModel();
        $authors = $authorModel->orderBy('name', 'ASC')->findAll();
        $publishers = $publisherModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'categories' => $categories,
            'racks'      => $racks,
            'authors'    => $authors,
            'publishers' => $publishers,
            'validation' => \Config\Services::validation(),
        ];

        return view('books/create', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        if (!$this->validate([
            'cover'        => 'permit_empty|is_image[cover]|mime_in[cover,image/jpg,image/jpeg,image/gif,image/png,image/webp]|max_size[cover,5120]',
            'title'        => 'required|string|max_length[127]',
            'author_id'    => 'required',
            'publisher_id' => 'required',
            'isbn'         => 'required|min_length[10]|max_length[18]',
            'year'         => 'required|numeric|min_length[4]|max_length[4]|less_than_equal_to[2100]',
            'rack'         => 'required|numeric',
            'category'     => 'required',
        ])) {
            $categories = $this->categoryModel->findAll();
            $racks = $this->rackModel->findAll();
            $authorModel = new \App\Models\AuthorModel();
            $publisherModel = new \App\Models\PublisherModel();
            $authors = $authorModel->orderBy('name', 'ASC')->findAll();
            $publishers = $publisherModel->orderBy('name', 'ASC')->findAll();

            $data = [
                'categories' => $categories,
                'racks'      => $racks,
                'authors'    => $authors,
                'publishers' => $publishers,
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ];

            return view('books/create', $data);
        }

        // Sanitize and clean ISBN input (keep only digits and X)
        $rawIsbn = trim((string)$this->request->getVar('isbn'));
        $isbnInput = preg_replace('/[^0-9X]/i', '', $rawIsbn);
        if (empty($isbnInput)) {
            $isbnInput = $rawIsbn;
        }

        // Cek duplikasi ISBN sebelum mendaftarkan buku baru
        $existingBook = $this->bookModel->where('isbn', $isbnInput)->first();
        if ($existingBook) {
            session()->setFlashdata([
                'msg'   => 'Gagal mendaftarkan buku: Buku dengan ISBN "' . esc($isbnInput) . '" sudah terdaftar di sistem ("' . esc($existingBook['title']) . '").',
                'error' => true
            ]);
            return redirect()->back()->withInput();
        }

        try {
            $authorRes = $this->resolveAuthorId($this->request->getVar('author_id'));
            $publisherRes = $this->resolvePublisherId($this->request->getVar('publisher_id'));
            $categoryRes = $this->resolveCategoryId($this->request->getVar('category'));

            $coverImage = $this->request->getFile('cover');
            $remoteCoverUrl = $this->request->getVar('cover_url');

            $coverImageFileName = null;
            if ($coverImage && $coverImage->isValid() && !$coverImage->hasMoved()) {
                $coverImageFileName = uploadBookCover($coverImage);
            } elseif (!empty($remoteCoverUrl)) {
                $coverImageFileName = downloadBookCoverFromUrl($remoteCoverUrl);
            }

            $slug = url_title($this->request->getVar('title') . ' ' . rand(0, 1000), '-', true);

            if (!$this->bookModel->save([
                'slug'         => $slug,
                'title'        => $this->request->getVar('title'),
                'author'       => $authorRes['name'],
                'author_id'    => $authorRes['id'],
                'publisher'    => $publisherRes['name'],
                'publisher_id' => $publisherRes['id'],
                'isbn'         => substr($isbnInput, 0, 13),
                'year'         => $this->request->getVar('year'),
                'rack_id'      => $this->request->getVar('rack'),
                'category_id'  => $categoryRes['id'],
                'synopsis'     => $this->request->getVar('synopsis'),
                'ddc'          => $this->request->getVar('ddc'),
                'call_number'  => $this->request->getVar('call_number'),
                'book_cover'   => $coverImageFileName ?? null,
            ])) {
                session()->setFlashdata(['msg' => 'Gagal mendaftarkan buku ke database.', 'error' => true]);
                return redirect()->back()->withInput();
            }

            $newBookId = $this->bookModel->insertID();
            $this->bookStockModel->save([
                'book_id'  => $newBookId,
                'quantity' => 0
            ]);

            $createdBook = $this->bookModel->find($newBookId);

            session()->setFlashdata(['msg' => 'Buku berhasil didaftarkan. Silakan tambahkan kartu salinan fisik buku.']);
            return redirect()->to('admin/books/' . ($createdBook['slug'] ?? ''));
        } catch (\Throwable $e) {
            log_message('error', 'Create Book Exception: ' . $e->getMessage());
            session()->setFlashdata(['msg' => 'Terjadi kesalahan sistem saat menyimpan buku baru: ' . $e->getMessage(), 'error' => true]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($slug = null)
    {
        $book = $this->bookModel
            ->select('books.*, book_stock.quantity')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->where('books.slug', $slug)->first();

        if (empty($book)) {
            $book = $this->bookModel
                ->select('books.*, book_stock.quantity')
                ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
                ->where('books.id', $slug)->first();
        }

        if (empty($book)) {
            throw new PageNotFoundException('Book with slug \'' . $slug . '\' not found');
        }

        $categories = $this->categoryModel->findAll();
        $racks = $this->rackModel->findAll();
        $authorModel = new \App\Models\AuthorModel();
        $publisherModel = new \App\Models\PublisherModel();
        $authors = $authorModel->orderBy('name', 'ASC')->findAll();
        $publishers = $publisherModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'book'       => $book,
            'categories' => $categories,
            'racks'      => $racks,
            'authors'    => $authors,
            'publishers' => $publishers,
            'validation' => \Config\Services::validation(),
        ];

        return view('books/edit', $data);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($slug = null)
    {
        $book = $this->bookModel->where('slug', $slug)->first();

        if (empty($book)) {
            $book = $this->bookModel->find($slug);
        }

        if (empty($book)) {
            throw new PageNotFoundException('Book with slug \'' . $slug . '\' not found');
        }

        $rules = [
            'cover'        => 'permit_empty|is_image[cover]|mime_in[cover,image/jpg,image/jpeg,image/gif,image/png,image/webp]|max_size[cover,5120]',
            'title'        => 'required|string|max_length[127]',
            'author_id'    => 'required',
            'publisher_id' => 'required',
            'isbn'         => 'required|min_length[10]|max_length[18]',
            'year'         => 'required|numeric|min_length[4]|max_length[4]|less_than_equal_to[2100]',
            'rack'         => 'required|numeric',
            'category'     => 'required',
        ];

        if (!$this->validate($rules)) {
            $categories = $this->categoryModel->findAll();
            $racks = $this->rackModel->findAll();
            $authorModel = new \App\Models\AuthorModel();
            $publisherModel = new \App\Models\PublisherModel();
            $authors = $authorModel->orderBy('name', 'ASC')->findAll();
            $publishers = $publisherModel->orderBy('name', 'ASC')->findAll();

            $data = [
                'book'       => $book,
                'categories' => $categories,
                'racks'      => $racks,
                'authors'    => $authors,
                'publishers' => $publishers,
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getPost(),
            ];

            return view('books/edit', $data);
        }

        // Sanitize and clean ISBN input (keep only digits and X) to prevent MySQL VARCHAR(13) overflow
        $rawIsbn = trim((string)$this->request->getPost('isbn'));
        $isbnInput = preg_replace('/[^0-9X]/i', '', $rawIsbn);
        if (empty($isbnInput)) {
            $isbnInput = $rawIsbn;
        }

        // Cek duplikasi ISBN saat mengedit buku (abaikan ID buku sendiri)
        $existingBook = $this->bookModel
            ->where('isbn', $isbnInput)
            ->where('id !=', $book['id'])
            ->first();
        if ($existingBook) {
            session()->setFlashdata([
                'msg'   => 'Gagal memperbarui buku: Nomor ISBN "' . esc($isbnInput) . '" sudah digunakan oleh buku lain ("' . esc($existingBook['title']) . '").',
                'error' => true
            ]);
            return redirect()->back()->withInput();
        }

        try {
            $authorRes = $this->resolveAuthorId($this->request->getPost('author_id'));
            $publisherRes = $this->resolvePublisherId($this->request->getPost('publisher_id'));
            $categoryRes = $this->resolveCategoryId($this->request->getPost('category'));

            $coverImage = $this->request->getFile('cover');
            $remoteCoverUrl = $this->request->getPost('cover_url');

            if ($coverImage && $coverImage->isValid() && !$coverImage->hasMoved()) {
                $coverImageFileName = updateBookCover($coverImage, $book['book_cover']);
            } elseif (!empty($remoteCoverUrl)) {
                $downloadedName = downloadBookCoverFromUrl($remoteCoverUrl);
                if ($downloadedName) {
                    deleteBookCover($book['book_cover']);
                    $coverImageFileName = $downloadedName;
                } else {
                    $coverImageFileName = $book['book_cover'];
                }
            } else {
                $coverImageFileName = $book['book_cover'];
            }

            $newTitle = trim((string)$this->request->getPost('title'));
            $slug = ($newTitle !== $book['title'])
                ? url_title($newTitle . ' ' . rand(10, 999), '-', true)
                : $book['slug'];

            $saveData = [
                'id'           => $book['id'],
                'slug'         => $slug,
                'title'        => $newTitle,
                'author'       => $authorRes['name'],
                'author_id'    => $authorRes['id'],
                'publisher'    => $publisherRes['name'],
                'publisher_id' => $publisherRes['id'],
                'isbn'         => substr($isbnInput, 0, 13),
                'year'         => $this->request->getPost('year'),
                'rack_id'      => $this->request->getPost('rack'),
                'category_id'  => $categoryRes['id'],
                'synopsis'     => $this->request->getPost('synopsis'),
                'ddc'          => $this->request->getPost('ddc'),
                'call_number'  => $this->request->getPost('call_number'),
                'book_cover'   => $coverImageFileName ?? $book['book_cover'],
            ];

            if (!$this->bookModel->save($saveData)) {
                session()->setFlashdata(['msg' => 'Gagal memperbarui data buku ke database.', 'error' => true]);
                return redirect()->back()->withInput();
            }

            session()->setFlashdata(['msg' => 'Data buku berhasil diperbarui']);
            return redirect()->to('admin/books/' . $slug);
        } catch (\Throwable $e) {
            log_message('error', 'Update Book Exception: ' . $e->getMessage());
            session()->setFlashdata(['msg' => 'Terjadi kesalahan sistem saat menyimpan perubahan buku: ' . $e->getMessage(), 'error' => true]);
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($slug = null)
    {
        $book = $this->bookModel->where('slug', $slug)->first();

        if (empty($book)) {
            throw new PageNotFoundException('Book with slug \'' . $slug . '\' not found');
        }

        $bookStock = $this->bookStockModel->where('book_id', $book['id'])->first();
        if ($bookStock) {
            $this->bookStockModel->delete($bookStock['id']);
        }

        if (!$this->bookModel->delete($book['id'])) {
            session()->setFlashdata(['msg' => 'Gagal menghapus data buku', 'error' => true]);
            return redirect()->back();
        }

        // delete former image file
        deleteBookCover($book['book_cover']);

        session()->setFlashdata(['msg' => 'Buku berhasil dihapus']);
        return redirect()->to('admin/books');
    }

    /**
     * Update condition of a specific book copy item
     */
    public function updateItemCondition($itemId = null)
    {
        $item = $this->bookItemModel->find($itemId);
        if (!$item) {
            session()->setFlashdata(['msg' => 'Eksemplar tidak ditemukan', 'error' => true]);
            return redirect()->back();
        }

        $newCondition = $this->request->getPost('condition');
        $conditionNote = trim($this->request->getPost('condition_note') ?? '');
        if (in_array($newCondition, ['baik', 'rusak', 'hilang'])) {
            $activeLoan = $this->loanModel
                ->where('book_item_id', $itemId)
                ->where('return_date', null)
                ->where('deleted_at', null)
                ->first();

            $newStatus = $activeLoan ? 'dipinjam' : ($newCondition === 'hilang' ? 'hilang' : 'tersedia');

            $this->bookItemModel->update($itemId, [
                'condition'      => $newCondition,
                'condition_note' => $conditionNote,
                'status'         => $newStatus
            ]);

            // Save log to book_item_condition_logs
            $conditionLogModel = new \App\Models\BookItemConditionLogModel();
            $conditionLogModel->save([
                'book_item_id'    => $itemId,
                'loan_id'         => $activeLoan['id'] ?? null,
                'member_id'       => $activeLoan['member_id'] ?? null,
                'condition_state' => $newCondition,
                'condition_note'  => $conditionNote ?: "Kondisi fisik eksemplar diperbarui manual.",
                'recorded_by'     => auth()->id()
            ]);

            $this->updateBookStockQuantity($item['book_id']);
            session()->setFlashdata(['msg' => 'Kondisi eksemplar ' . $item['item_code'] . ' berhasil diperbarui']);
        }

        $book = $this->bookModel->find($item['book_id']);
        if ($book && !empty($book['slug'])) {
            return redirect()->to("admin/books/{$book['slug']}");
        }
        return redirect()->to('admin/books');
    }

    /**
     * Add a copy card for a book
     */
    public function addCopy($bookId = null)
    {
        $book = $this->bookModel->find($bookId);
        if (!$book) {
            session()->setFlashdata(['msg' => 'Buku tidak ditemukan', 'error' => true]);
            return redirect()->back();
        }

        $itemCode = trim($this->request->getPost('item_code') ?? '');
        if (empty($itemCode)) {
            do {
                $itemCode = 'BK' . sprintf('%04d', $bookId) . '-' . strtoupper(substr(md5(uniqid((string)rand(), true)), 0, 5));
            } while ($this->bookItemModel->where('item_code', $itemCode)->first());
        } else {
            $existingItem = $this->bookItemModel->where('item_code', $itemCode)->first();
            if ($existingItem) {
                session()->setFlashdata(['msg' => "Kode Kartu Buku '{$itemCode}' sudah terdaftar pada eksemplar lain. Setiap Kartu Buku wajib memiliki kode unik!", 'error' => true]);
                return redirect()->to("admin/books/{$book['slug']}");
            }
        }

        $acquisition = strtolower($this->request->getPost('acquisition_type') ?? $this->request->getPost('acquisition') ?? 'pembelian');
        if (!in_array($acquisition, ['pembelian', 'donasi', 'hibah'])) {
            $acquisition = 'pembelian';
        }

        $donorMemberId = in_array($acquisition, ['donasi', 'hibah']) ? $this->request->getPost('donated_by_member_id') : null;
        $donorMemberId = !empty($donorMemberId) ? (int)$donorMemberId : null;

        $copyType = $this->request->getPost('copy_type') ?? 'fisik';
        $condition = $this->request->getPost('condition') ?? 'baik';
        $status = $this->request->getPost('status') ?? 'tersedia';
        $rackId = $this->request->getPost('rack_id') ?? $book['rack_id'];
        $price = floatval($this->request->getPost('price') ?? 0);

        $this->bookItemModel->insert([
            'book_id'              => $bookId,
            'item_code'            => $itemCode,
            'condition'            => $condition,
            'copy_type'            => $copyType,
            'status'               => $status,
            'acquisition'          => $acquisition,
            'price'                => $price,
            'rack_id'              => $rackId,
            'donated_by_member_id' => $donorMemberId,
        ]);

        $this->updateBookStockQuantity($bookId);
        if ($donorMemberId) {
            $this->syncMemberDonationCount($donorMemberId);
        }

        session()->setFlashdata(['msg' => "Kartu buku '{$itemCode}' berhasil ditambahkan."]);
        return redirect()->to("admin/books/{$book['slug']}");
    }

    /**
     * Update copy card info
     */
    public function updateCopy($copyId = null)
    {
        $copy = $this->bookItemModel->find($copyId);
        if (!$copy) {
            session()->setFlashdata(['msg' => 'Kartu buku tidak ditemukan', 'error' => true]);
            return redirect()->to('admin/books');
        }

        $itemCode = trim($this->request->getPost('item_code') ?? '');
        if (empty($itemCode)) {
            $itemCode = $copy['item_code'];
        }

        $existingItem = $this->bookItemModel->where('item_code', $itemCode)->where('id !=', $copyId)->first();
        if ($existingItem) {
            session()->setFlashdata(['msg' => "Kode Kartu Buku '{$itemCode}' sudah digunakan oleh eksemplar lain. Setiap Kartu Buku wajib unik!", 'error' => true]);
            $book = $this->bookModel->find($copy['book_id']);
            if ($book && !empty($book['slug'])) {
                return redirect()->to("admin/books/{$book['slug']}");
            }
            return redirect()->to('admin/books');
        }

        $acquisition = strtolower($this->request->getPost('acquisition_type') ?? $this->request->getPost('acquisition') ?? 'pembelian');
        if (!in_array($acquisition, ['pembelian', 'donasi', 'hibah'])) {
            $acquisition = 'pembelian';
        }

        $oldDonorId = $copy['donated_by_member_id'];
        $newDonorId = in_array($acquisition, ['donasi', 'hibah']) ? $this->request->getPost('donated_by_member_id') : null;
        $newDonorId = !empty($newDonorId) ? (int)$newDonorId : null;

        $copyType = $this->request->getPost('copy_type') ?? $copy['copy_type'];
        $condition = $this->request->getPost('condition') ?? $copy['condition'];
        $status = $this->request->getPost('status') ?? $copy['status'];
        $rackId = $this->request->getPost('rack_id') ?? $copy['rack_id'];
        $price = floatval($this->request->getPost('price') ?? $copy['price'] ?? 0);

        // Auto-fix status logic:
        // Check if there is an active loan linked to this specific book copy item
        $activeLoan = $this->loanModel
            ->where('book_item_id', $copyId)
            ->where('return_date', null)
            ->where('deleted_at', null)
            ->first();

        if ($activeLoan) {
            // If currently borrowed, status must remain 'dipinjam'
            $status = 'dipinjam';
        } else {
            // If NOT borrowed, status must be 'tersedia' (or 'rusak'/'hilang' based on condition)
            if (in_array($condition, ['baik', 'rusak'])) {
                $status = 'tersedia';
            } else {
                $status = 'hilang';
            }
        }

        $conditionNote = trim($this->request->getPost('condition_note') ?? $copy['condition_note'] ?? '');

        $this->bookItemModel->update($copyId, [
            'item_code'            => $itemCode,
            'condition'            => $condition,
            'condition_note'       => $conditionNote,
            'copy_type'            => $copyType,
            'status'               => $status,
            'acquisition'          => $acquisition,
            'price'                => $price,
            'rack_id'              => $rackId,
            'donated_by_member_id' => $newDonorId,
        ]);

        if ($condition !== $copy['condition'] || $conditionNote !== ($copy['condition_note'] ?? '')) {
            $conditionLogModel = new \App\Models\BookItemConditionLogModel();
            $conditionLogModel->save([
                'book_item_id'    => $copyId,
                'loan_id'         => $activeLoan['id'] ?? null,
                'member_id'       => $activeLoan['member_id'] ?? null,
                'condition_state' => $condition,
                'condition_note'  => $conditionNote ?: "Detail eksemplar diperbarui oleh petugas.",
                'recorded_by'     => auth()->id()
            ]);
        }

        $this->updateBookStockQuantity($copy['book_id']);
        if ($oldDonorId) $this->syncMemberDonationCount($oldDonorId);
        if ($newDonorId) $this->syncMemberDonationCount($newDonorId);

        session()->setFlashdata(['msg' => "Detail kartu buku '{$itemCode}' berhasil diperbarui."]);
        $book = $this->bookModel->find($copy['book_id']);
        if ($book && !empty($book['slug'])) {
            return redirect()->to("admin/books/{$book['slug']}");
        }
        return redirect()->to('admin/books');
    }

    /**
     * Delete a copy card
     */
    public function deleteCopy($copyId = null)
    {
        $copy = $this->bookItemModel->find($copyId);
        if (!$copy) {
            session()->setFlashdata(['msg' => 'Salinan buku tidak ditemukan', 'error' => true]);
            return redirect()->to('admin/books');
        }

        $donorId = $copy['donated_by_member_id'];
        $this->bookItemModel->delete($copyId);

        $this->updateBookStockQuantity($copy['book_id']);
        $this->syncMemberDonationCount($donorId);

        session()->setFlashdata(['msg' => 'Salinan fisik buku berhasil dihapus.']);
        $book = $this->bookModel->find($copy['book_id']);
        if ($book && !empty($book['slug'])) {
            return redirect()->to("admin/books/{$book['slug']}");
        }
        return redirect()->to('admin/books');
    }

    /**
     * Sync member's donated_books_count
     */
    protected function syncMemberDonationCount($memberId)
    {
        if (empty($memberId)) return;
        $count = \App\Models\MemberModel::getDonatedBooksCount($memberId);
        $this->memberModel->update($memberId, ['donated_books_count' => $count]);
    }

    /**
     * Update quantity in book_stock table to ensure backwards compatibility
     */
    protected function updateBookStockQuantity($bookId)
    {
        $count = $this->bookItemModel->where([
            'book_id' => $bookId,
            'deleted_at' => null
        ])->countAllResults();

        $stock = $this->bookStockModel->where('book_id', $bookId)->first();
        if ($stock) {
            $this->bookStockModel->update($stock['id'], ['quantity' => $count]);
        } else {
            $this->bookStockModel->insert(['book_id' => $bookId, 'quantity' => $count]);
        }
    }

    /**
     * Resolve author string, array, or ID into valid primary author ID & comma-separated names
     */
    private function resolveAuthorId($authorVal): array
    {
        $authorModel = new \App\Models\AuthorModel();
        $items = is_array($authorVal) ? $authorVal : array_map('trim', explode(',', (string)$authorVal));
        $names = [];
        $primaryId = null;

        foreach ($items as $val) {
            $val = trim((string)$val);
            if (empty($val)) continue;

            if (is_numeric($val)) {
                $a = $authorModel->find($val);
                if ($a) {
                    $names[] = $a['name'];
                    if (!$primaryId) $primaryId = $a['id'];
                    continue;
                }
            }

            $normalizedSearch = strtolower(preg_replace('/[\s\-]+/', ' ', $val));
            $allAuthors = $authorModel->findAll();
            $found = false;
            foreach ($allAuthors as $a) {
                if (strtolower(preg_replace('/[\s\-]+/', ' ', $a['name'])) === $normalizedSearch) {
                    $names[] = $a['name'];
                    if (!$primaryId) $primaryId = $a['id'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $authorModel->insert(['name' => $val]);
                $newId = $authorModel->insertID();
                $names[] = $val;
                if (!$primaryId) $primaryId = $newId;
            }
        }

        return [
            'id'   => $primaryId,
            'name' => implode(', ', array_unique($names))
        ];
    }

    /**
     * Resolve publisher string, array, or ID into valid primary publisher ID & comma-separated names
     */
    private function resolvePublisherId($publisherVal): array
    {
        $publisherModel = new \App\Models\PublisherModel();
        $items = is_array($publisherVal) ? $publisherVal : array_map('trim', explode(',', (string)$publisherVal));
        $names = [];
        $primaryId = null;

        foreach ($items as $val) {
            $val = trim((string)$val);
            if (empty($val)) continue;

            if (is_numeric($val)) {
                $p = $publisherModel->find($val);
                if ($p) {
                    $names[] = $p['name'];
                    if (!$primaryId) $primaryId = $p['id'];
                    continue;
                }
            }

            $cleanSearch = strtolower(preg_replace('/[^a-z0-9]/i', '', $val));
            $allPublishers = $publisherModel->findAll();
            $found = false;
            foreach ($allPublishers as $p) {
                $cleanName = strtolower(preg_replace('/[^a-z0-9]/i', '', $p['name']));
                if (!empty($cleanName) && (
                    $cleanName === $cleanSearch || 
                    str_contains($cleanSearch, $cleanName) || 
                    str_contains($cleanName, $cleanSearch)
                )) {
                    $names[] = $p['name'];
                    if (!$primaryId) $primaryId = $p['id'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $publisherModel->insert(['name' => $val]);
                $newId = $publisherModel->insertID();
                $names[] = $val;
                if (!$primaryId) $primaryId = $newId;
            }
        }

        return [
            'id'   => $primaryId,
            'name' => implode(', ', array_unique($names))
        ];
    }

    /**
     * Resolve category string, array, or ID into valid primary category ID & names
     */
    private function resolveCategoryId($categoryVal): array
    {
        $categoryModel = new \App\Models\CategoryModel();
        $items = is_array($categoryVal) ? $categoryVal : array_map('trim', explode(',', (string)$categoryVal));
        $names = [];
        $primaryId = null;

        foreach ($items as $val) {
            $val = trim((string)$val);
            if (empty($val)) continue;

            if (is_numeric($val)) {
                $c = $categoryModel->find($val);
                if ($c) {
                    $names[] = $c['name'];
                    if (!$primaryId) $primaryId = $c['id'];
                    continue;
                }
            }

            $normalizedSearch = strtolower(preg_replace('/[\s\-]+/', ' ', $val));
            $allCats = $categoryModel->findAll();
            $found = false;
            foreach ($allCats as $c) {
                if (strtolower(preg_replace('/[\s\-]+/', ' ', $c['name'])) === $normalizedSearch) {
                    $names[] = $c['name'];
                    if (!$primaryId) $primaryId = $c['id'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $categoryModel->insert(['name' => $val]);
                $newId = $categoryModel->insertID();
                $names[] = $val;
                if (!$primaryId) $primaryId = $newId;
            }
        }

        return [
            'id'   => $primaryId,
            'name' => implode(', ', array_unique($names))
        ];
    }

    /**
     * Lookup book details by ISBN from multiple providers (Google Books, OpenLibrary, ISBNSearch)
     */
    public function lookupIsbn()
    {
        $isbn = preg_replace('/[^0-9X]/i', '', $this->request->getGet('isbn') ?? '');

        if (empty($isbn) || (strlen($isbn) !== 10 && strlen($isbn) !== 13)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Nomor ISBN harus terdiri dari 10 atau 13 digit angka.'
            ]);
        }

        // Cek jika ISBN ini sudah terdaftar di database lokal
        $existing = $this->bookModel->where('deleted_at', null)->where('isbn', $isbn)->first();
        if (!empty($existing)) {
            return $this->response->setJSON([
                'status'  => false,
                'exists'  => true,
                'message' => 'Buku dengan ISBN ' . $isbn . ' ("' . esc($existing['title']) . '") sudah terdaftar di database! Tidak dapat mendaftarkan buku ganda.'
            ]);
        }

        $bookData = $this->fetchFromIsbnSearch($isbn);
        $source = 'ISBNSearch.org';

        if (empty($bookData) || empty($bookData['title'])) {
            $bookData = $this->fetchFromGoogleBooks($isbn);
            $source = 'Google Books';
        }

        if (empty($bookData) || empty($bookData['title'])) {
            $bookData = $this->fetchFromOpenLibrary($isbn);
            $source = 'OpenLibrary';
        }

        if (empty($bookData) || empty($bookData['title'])) {
            $bookData = $this->fetchFromIsbnSearchByPost($isbn);
            $source = 'ISBNSearch (Form)';
        }

        // If publisher or cover_url is missing, attempt fallback merge with Google Books / OpenLibrary
        if (!empty($bookData) && !empty($bookData['title'])) {
            if (empty($bookData['publisher']) || empty($bookData['cover_url'])) {
                $gData = $this->fetchFromGoogleBooks($isbn);
                if (!empty($gData)) {
                    if (empty($bookData['publisher']) && !empty($gData['publisher'])) {
                        $bookData['publisher'] = $gData['publisher'];
                    }
                    if (empty($bookData['cover_url']) && !empty($gData['cover_url'])) {
                        $bookData['cover_url'] = $gData['cover_url'];
                    }
                }
            }

            if (empty($bookData['publisher']) || empty($bookData['cover_url']) || empty($bookData['synopsis'])) {
                $olData = $this->searchOpenLibraryByQuery($isbn);
                if (!empty($olData)) {
                    if (empty($bookData['publisher']) && !empty($olData['publisher'])) {
                        $bookData['publisher'] = $olData['publisher'];
                    }
                    if (empty($bookData['cover_url']) && !empty($olData['cover_url'])) {
                        $bookData['cover_url'] = $olData['cover_url'];
                    }
                    if (empty($bookData['synopsis']) && !empty($olData['synopsis'])) {
                        $bookData['synopsis'] = $olData['synopsis'];
                    }
                }
            }
        }

        if (!empty($bookData) && !empty($bookData['title'])) {
            if (empty($bookData['ddc'])) {
                $bookData['ddc'] = $this->detectDdcCode($bookData['category'] ?? null, $bookData['title'] ?? null);
            }
            // Find existing matching Author and Publisher in Master tables without creating new rows yet
            if (!empty($bookData['author'])) {
                $authorModel = new \App\Models\AuthorModel();
                $normalizedSearch = strtolower(preg_replace('/[\s\-]+/', ' ', $bookData['author']));
                $allAuthors = $authorModel->findAll();
                $foundId = null;
                foreach ($allAuthors as $a) {
                    if (strtolower(preg_replace('/[\s\-]+/', ' ', $a['name'])) === $normalizedSearch) {
                        $foundId = $a['id'];
                        $bookData['author'] = $a['name'];
                        break;
                    }
                }
                $bookData['author_id'] = $foundId;
            }

            if (!empty($bookData['publisher'])) {
                $publisherModel = new \App\Models\PublisherModel();
                $cleanSearch = strtolower(preg_replace('/[^a-z0-9]/i', '', $bookData['publisher']));
                $allPublishers = $publisherModel->findAll();
                $foundId = null;
                foreach ($allPublishers as $p) {
                    $cleanName = strtolower(preg_replace('/[^a-z0-9]/i', '', $p['name']));
                    if (!empty($cleanName) && (
                        $cleanName === $cleanSearch || 
                        str_contains($cleanSearch, $cleanName) || 
                        str_contains($cleanName, $cleanSearch)
                    )) {
                        $foundId = $p['id'];
                        $bookData['publisher'] = $p['name'];
                        break;
                    }
                }
                $bookData['publisher_id'] = $foundId;
            }

            return $this->response->setJSON([
                'status' => true,
                'source' => $source,
                'data'   => $bookData
            ]);
        }

        return $this->response->setJSON([
            'status'  => false,
            'message' => 'Data buku untuk ISBN tersebut tidak ditemukan di database publik online.'
        ]);
    }

    /**
     * AI & Multi-Source Smart Book Search (Title, Author, or ISBN) with Cloudinary Cover Fetching
     */
    public function lookupAi()
    {
        $query = trim((string)($this->request->getGet('query') ?? ''));

        if (empty($query)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Silakan masukkan Judul Buku, Penulis, atau ISBN untuk dicari oleh AI.'
            ]);
        }

        // Check if numeric/ISBN format matches existing local book
        $cleanIsbn = preg_replace('/[^0-9X]/i', '', $query);
        if (!empty($cleanIsbn) && (strlen($cleanIsbn) === 10 || strlen($cleanIsbn) === 13)) {
            $existing = $this->bookModel->where('isbn', $cleanIsbn)->first();
            if (!empty($existing)) {
                return $this->response->setJSON([
                    'status'  => false,
                    'exists'  => true,
                    'message' => 'Buku dengan ISBN ' . $cleanIsbn . ' ("' . esc($existing['title']) . '") sudah terdaftar di database!'
                ]);
            }
        }

        $bookData = null;
        $source = 'AI Multi-Source Engine';

        // 1. If ISBN format (10 or 13 digits), check ISBNSearch Engine first!
        if (!empty($cleanIsbn) && (strlen($cleanIsbn) === 10 || strlen($cleanIsbn) === 13)) {
            $bookData = $this->fetchFromIsbnSearch($cleanIsbn);
            if (!empty($bookData) && !empty($bookData['title'])) {
                $bookData['isbn'] = $cleanIsbn;
                $source = 'AI ISBN Engine';
            }
        }

        // 2. Search Google Books Volumes API
        if (empty($bookData) || empty($bookData['title'])) {
            $bookData = $this->searchGoogleBooksByQuery($query);
            $source = 'AI Google Books Engine';
        }

        // 3. Search OpenLibrary API if empty
        if (empty($bookData) || empty($bookData['title'])) {
            $bookData = $this->searchOpenLibraryByQuery($query);
            $source = 'AI OpenLibrary Engine';
        }

        // 4. Fallback: Smart AI Web Search Engine
        if (empty($bookData) || empty($bookData['title'])) {
            $bookData = $this->searchPublicAiBookEngine($query);
            $source = 'AI Smart Search Engine';
        }

        $isbnOnlyFallback = false;

        if (empty($bookData) || empty($bookData['title'])) {
            if (!empty($cleanIsbn)) {
                $bookData = [
                    'title'     => '',
                    'author'    => '',
                    'publisher' => '',
                    'year'      => '',
                    'isbn'      => $cleanIsbn,
                    'category'  => 'Umum',
                    'ddc'       => '',
                    'synopsis'  => '',
                    'cover_url' => null
                ];
                $source = 'Scan Barcode ISBN';
                $isbnOnlyFallback = true;
            } else {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'Data buku online tidak ditemukan untuk "' . esc($query) . '". Silakan masukkan Judul / ISBN yang valid.'
                ]);
            }
        }

        if (!empty($cleanIsbn) && empty($bookData['isbn'])) {
            $bookData['isbn'] = $cleanIsbn;
        }

        if (!$isbnOnlyFallback && empty($bookData['synopsis'])) {
            $bookData['synopsis'] = $this->generateSynopsisFromAi($bookData['title'] ?? '', $bookData['author'] ?? '');
        }

        // Re-detect DDC: jika kosong atau masih default '000' dari sub-function
        if (empty($bookData['ddc']) || $bookData['ddc'] === '000') {
            $bookData['ddc'] = $this->detectDdcCode($bookData['category'] ?? null, $bookData['title'] ?? null);
        }

        // Match existing category ID if category name matches
        if (!empty($bookData['category'])) {
            $matchedCategory = $this->categoryModel
                ->like('name', $bookData['category'], insensitiveSearch: true)
                ->first();
            if ($matchedCategory) {
                $bookData['category_id'] = $matchedCategory['id'];
            }
        }

        if (empty($bookData['category_id'])) {
            $bookData['category_id'] = $this->autoMatchCategoryId($bookData['title'] ?? '', $bookData['author'] ?? '');
        }

        // Resolve publisher_id from publisher name (contains-match against database)
        if (!empty($bookData['publisher']) && empty($bookData['publisher_id'])) {
            $publisherModel = new \App\Models\PublisherModel();
            $cleanSearch = strtolower(preg_replace('/[^a-z0-9]/i', '', $bookData['publisher']));
            $allPublishers = $publisherModel->findAll();
            foreach ($allPublishers as $p) {
                $cleanName = strtolower(preg_replace('/[^a-z0-9]/i', '', $p['name']));
                if (!empty($cleanName) && (
                    $cleanName === $cleanSearch ||
                    str_contains($cleanSearch, $cleanName) ||
                    str_contains($cleanName, $cleanSearch)
                )) {
                    $bookData['publisher_id'] = $p['id'];
                    $bookData['publisher']    = $p['name'];
                    break;
                }
            }
        }

        return $this->response->setJSON([
            'status'    => true,
            'source'    => $source,
            'isbn_only' => $isbnOnlyFallback,
            'data'      => $bookData
        ]);
    }

    private function generateSynopsisFromAi(string $title, string $author = ''): ?string
    {
        if (empty($title)) return null;

        // 1. Try searchOpenLibraryByQuery
        $olData = $this->searchOpenLibraryByQuery($title . ' ' . $author);
        if (!empty($olData['synopsis'])) {
            return $olData['synopsis'];
        }

        // 2. Try DuckDuckGo Sinopsis Search
        $query = "Sinopsis ringkasan buku " . $title . ($author ? " " . $author : "");
        $gUrl = "https://html.duckduckgo.com/html/?q=" . urlencode($query);
        $gHtml = $this->httpGet($gUrl);
        if ($gHtml && preg_match('/class="result__snippet"[^>]*>(.*?)</i', $gHtml, $sm)) {
            $snippet = strip_tags(html_entity_decode($sm[1]));
            if (strlen($snippet) > 25) {
                return trim($snippet);
            }
        }

        return null;
    }

    private function autoMatchCategoryId(string $title, string $author = ''): ?int
    {
        $text = strtolower($title . ' ' . $author);
        $categories = $this->categoryModel->findAll();
        foreach ($categories as $cat) {
            $catName = strtolower($cat['name']);
            if (str_contains($text, $catName) || (str_contains($catName, 'fiksi') && (str_contains($text, 'novel') || str_contains($text, 'cerpen')))) {
                return (int)$cat['id'];
            }
        }
        return !empty($categories[0]) ? (int)$categories[0]['id'] : null;
    }

    private function searchGoogleBooksByQuery(string $query): ?array
    {
        $cleanIsbn = preg_replace('/[^0-9X]/i', '', $query);
        $isIsbn = (!empty($cleanIsbn) && (strlen($cleanIsbn) === 10 || strlen($cleanIsbn) === 13));
        $qParam = $isIsbn ? "isbn:" . $cleanIsbn : urlencode($query);

        $url = "https://www.googleapis.com/books/v1/volumes?q=" . $qParam . "&maxResults=1&hl=id";
        $response = $this->httpGet($url);

        if (!$response && $isIsbn) {
            $url = "https://www.googleapis.com/books/v1/volumes?q=" . urlencode($query) . "&maxResults=1&hl=id";
            $response = $this->httpGet($url);
        }
        if (!$response) return null;

        $json = json_decode($response, true);
        if (!empty($json['error'])) return null;
        if (empty($json['items'][0]['volumeInfo'])) return null;

        $info = $json['items'][0]['volumeInfo'];

        $title = $info['title'] ?? null;
        $author = !empty($info['authors']) ? implode(', ', $info['authors']) : '';
        $publisher = $info['publisher'] ?? '';
        $year = !empty($info['publishedDate']) ? substr($info['publishedDate'], 0, 4) : null;
        
        $isbn = null;
        if (!empty($info['industryIdentifiers'])) {
            foreach ($info['industryIdentifiers'] as $id) {
                if (in_array($id['type'] ?? '', ['ISBN_13', 'ISBN_10'])) {
                    $isbn = $id['identifier'];
                    break;
                }
            }
        }

        $coverUrl = null;
        if (!empty($info['imageLinks']['extraLarge'])) {
            $coverUrl = $info['imageLinks']['extraLarge'];
        } elseif (!empty($info['imageLinks']['large'])) {
            $coverUrl = $info['imageLinks']['large'];
        } elseif (!empty($info['imageLinks']['medium'])) {
            $coverUrl = $info['imageLinks']['medium'];
        } elseif (!empty($info['imageLinks']['thumbnail'])) {
            $coverUrl = $info['imageLinks']['thumbnail'];
        }

        if ($coverUrl) {
            $coverUrl = str_replace('http://', 'https://', $coverUrl);
        }

        $category = !empty($info['categories'][0]) ? $info['categories'][0] : null;

        return [
            'title'     => $title,
            'author'    => $author,
            'publisher' => $publisher,
            'year'      => $year,
            'isbn'      => $isbn,
            'category'  => $category,
            'ddc'       => $this->detectDdcCode($category, $title),
            'synopsis'  => !empty($info['description']) ? $info['description'] : null,
            'cover_url' => $coverUrl
        ];
    }

    private function detectDdcCode(?string $categoryName, ?string $title): string
    {
        $text = strtolower(($categoryName ?? '') . ' ' . ($title ?? ''));

        // Agama / Islam (ID + EN)
        if (str_contains($text, 'islam') || str_contains($text, 'fiqih') || str_contains($text, 'hadits') || str_contains($text, 'quran') || str_contains($text, 'tafsir') || str_contains($text, 'pesantren') || str_contains($text, 'agama') || str_contains($text, 'religion') || str_contains($text, 'islamic') || str_contains($text, 'muslim')) {
            return '297';
        }
        // Fiksi / Novel (ID + EN)
        if (str_contains($text, 'novel') || str_contains($text, 'fiksi') || str_contains($text, 'cerpen') || str_contains($text, 'sastra') || str_contains($text, 'komik') || str_contains($text, 'fiction') || str_contains($text, 'romance') || str_contains($text, 'thriller') || str_contains($text, 'fantasy') || str_contains($text, 'horror') || str_contains($text, 'literary') || str_contains($text, 'young adult') || str_contains($text, 'manga') || str_contains($text, 'comic')) {
            return '813';
        }
        // Komputer / IT (ID + EN)
        if (str_contains($text, 'komputer') || str_contains($text, 'pemrograman') || str_contains($text, 'web') || str_contains($text, 'database') || str_contains($text, 'algoritma') || str_contains($text, 'coding') || str_contains($text, 'sistem') || str_contains($text, 'computer') || str_contains($text, 'programming') || str_contains($text, 'software') || str_contains($text, 'technology') || str_contains($text, 'artificial intelligence') || str_contains($text, 'machine learning')) {
            return '005.75';
        }
        // Sains / Ilmu Alam (ID + EN)
        if (str_contains($text, 'matematika') || str_contains($text, 'fisika') || str_contains($text, 'kimia') || str_contains($text, 'biologi') || str_contains($text, 'sains') || str_contains($text, 'mathematics') || str_contains($text, 'physics') || str_contains($text, 'chemistry') || str_contains($text, 'biology') || str_contains($text, 'science') || str_contains($text, 'nature')) {
            return '500';
        }
        // Sejarah / Biografi / Geografi (ID + EN)
        if (str_contains($text, 'sejarah') || str_contains($text, 'biografi') || str_contains($text, 'geografi') || str_contains($text, 'history') || str_contains($text, 'biography') || str_contains($text, 'geography') || str_contains($text, 'historical')) {
            return '900';
        }
        // Bahasa / Linguistik (ID + EN)
        if (str_contains($text, 'bahasa') || str_contains($text, 'kamus') || str_contains($text, 'inggris') || str_contains($text, 'arab') || str_contains($text, 'language') || str_contains($text, 'linguistics') || str_contains($text, 'dictionary') || str_contains($text, 'english') || str_contains($text, 'arabic')) {
            return '400';
        }
        // Sosial / Ekonomi / Hukum (ID + EN)
        if (str_contains($text, 'ekonomi') || str_contains($text, 'hukum') || str_contains($text, 'politik') || str_contains($text, 'sosiologi') || str_contains($text, 'pendidikan') || str_contains($text, 'economics') || str_contains($text, 'law') || str_contains($text, 'politics') || str_contains($text, 'sociology') || str_contains($text, 'education') || str_contains($text, 'business') || str_contains($text, 'management') || str_contains($text, 'social')) {
            return '300';
        }
        // Filsafat / Psikologi (ID + EN)
        if (str_contains($text, 'filsafat') || str_contains($text, 'psikologi') || str_contains($text, 'philosophy') || str_contains($text, 'psychology') || str_contains($text, 'self-help') || str_contains($text, 'self help') || str_contains($text, 'motivation') || str_contains($text, 'inspirational')) {
            return '100';
        }
        // Seni / Musik / Olahraga (ID + EN)
        if (str_contains($text, 'seni') || str_contains($text, 'musik') || str_contains($text, 'olahraga') || str_contains($text, 'art') || str_contains($text, 'music') || str_contains($text, 'sport') || str_contains($text, 'cooking') || str_contains($text, 'photography') || str_contains($text, 'design') || str_contains($text, 'craft')) {
            return '700';
        }
        return '000';
    }

    private function searchOpenLibraryByQuery(string $query): ?array
    {
        $url = "https://openlibrary.org/search.json?q=" . urlencode($query) . "&limit=1";
        $response = $this->httpGet($url);
        if (!$response) return null;

        $json = json_decode($response, true);
        if (empty($json['docs'][0])) return null;

        $doc = $json['docs'][0];

        $title = $doc['title'] ?? null;
        $author = !empty($doc['author_name']) ? implode(', ', $doc['author_name']) : '';
        $publisher = !empty($doc['publisher'][0]) ? $doc['publisher'][0] : '';
        $year = !empty($doc['first_publish_year']) ? (string)$doc['first_publish_year'] : null;
        $isbn = !empty($doc['isbn'][0]) ? $doc['isbn'][0] : null;
        $cat = !empty($doc['subject'][0]) ? $doc['subject'][0] : null;

        $synopsis = null;
        if (!empty($doc['key'])) {
            $workUrl = "https://openlibrary.org" . $doc['key'] . ".json";
            $workRes = $this->httpGet($workUrl);
            if ($workRes) {
                $wJson = json_decode($workRes, true);
                $desc = $wJson['description'] ?? null;
                if (is_array($desc)) {
                    $desc = $desc['value'] ?? null;
                }
                if (!empty($desc)) {
                    $synopsis = trim((string)$desc);
                }
            }
        }

        $coverUrl = null;
        if (!empty($doc['cover_i'])) {
            $coverUrl = "https://covers.openlibrary.org/b/id/{$doc['cover_i']}-L.jpg";
        }

        return [
            'title'     => $title,
            'author'    => $author,
            'publisher' => $publisher,
            'year'      => $year,
            'isbn'      => $isbn,
            'category'  => $cat,
            'ddc'       => $this->detectDdcCode($cat, $title),
            'synopsis'  => $synopsis,
            'cover_url' => $coverUrl
        ];
    }

    private function searchPublicAiBookEngine(string $query): ?array
    {
        $gUrl = "https://html.duckduckgo.com/html/?q=" . urlencode($query . " sinopsis blurb buku pengarang");
        $gHtml = $this->httpGet($gUrl);
        if (!$gHtml) return null;

        $title = null;
        $author = '';
        $publisher = '';
        $year = date('Y');
        $isbn = null;
        $synopsis = null;

        if (preg_match('/<a[^>]+class="result__title"[^>]*>(.*?)<\/a>/i', $gHtml, $tm)) {
            $rawTitle = strip_tags(html_entity_decode($tm[1]));
            $rawTitle = preg_replace('/\s*[-|]\s*Wikipedia.*$/i', '', $rawTitle);
            $rawTitle = preg_replace('/\s*[-|]\s*Goodreads.*$/i', '', $rawTitle);
            $title = trim($rawTitle);
        }

        if (preg_match('/class="result__snippet"[^>]*>(.*?)</i', $gHtml, $sm)) {
            $snippet = strip_tags(html_entity_decode($sm[1]));
            $synopsis = $snippet;
            if (preg_match('/(?:karya|oleh|penulis|pengarang)\s*:\s*([^.\n,]+)/i', $snippet, $am)) $author = trim($am[1]);
            if (preg_match('/(?:penerbit)\s*:\s*([^.\n,]+)/i', $snippet, $pm)) $publisher = trim($pm[1]);
            if (preg_match('/\b(19\d{2}|20\d{2})\b/', $snippet, $ym)) $year = $ym[1];
            if (preg_match('/\b97[89]\d{10}\b/', $snippet, $im)) $isbn = $im[0];
        }

        if (!empty($title)) {
            return [
                'title'     => $title,
                'author'    => $author,
                'publisher' => $publisher,
                'year'      => $year,
                'isbn'      => $isbn,
                'category'  => null,
                'ddc'       => $this->detectDdcCode(null, $title),
                'synopsis'  => $synopsis,
                'cover_url' => null
            ];
        }

        return null;
    }

    private function fetchFromIsbnSearch(string $isbn): ?array
    {
        $url = "https://isbnsearch.org/isbn/{$isbn}";
        $html = $this->httpGet($url);

        // If direct fetch triggers Captcha / blocked, use google search snippet API fallback
        if (!$html || str_contains(strtolower($html), 'verify to continue') || str_contains(strtolower($html), 'recaptcha')) {
            $gUrl = "https://html.duckduckgo.com/html/?q=site:isbnsearch.org+" . urlencode($isbn);
            $gHtml = $this->httpGet($gUrl);
            if ($gHtml) {
                $title = null;
                $author = '';
                $publisher = '';
                $year = null;

                if (preg_match('/<a[^>]+class="result__url"[^>]*>[\s\S]*?<\/a>[\s\S]*?<a[^>]+class="result__snippet"[^>]*>(.*?)<\/a>/i', $gHtml, $m) || preg_match('/class="result__snippet"[^>]*>(.*?)</i', $gHtml, $m)) {
                    $snippet = strip_tags(html_entity_decode($m[1]));
                    if (preg_match('/Author(?:s)?:\s*([^.\n]+)/i', $snippet, $am)) $author = trim($am[1]);
                    if (preg_match('/Publisher:\s*([^.\n]+)/i', $snippet, $pm)) $publisher = trim($pm[1]);
                    if (preg_match('/Published:\s*([^.\n]+)/i', $snippet, $ym)) {
                        if (preg_match('/\b\d{4}\b/', $ym[1], $yMatch)) $year = $yMatch[0];
                    }
                }

                if (preg_match('/<a[^>]+class="result__title"[^>]*>(.*?)<\/a>/i', $gHtml, $tm)) {
                    $rawTitle = strip_tags(html_entity_decode($tm[1]));
                    $rawTitle = preg_replace('/^ISBN\s*\d+\s*[-:]\s*/i', '', $rawTitle);
                    $rawTitle = preg_replace('/\s*[-|]\s*ISBN\s*Search.*$/i', '', $rawTitle);
                    $title = trim($rawTitle);
                }

                if (!empty($title)) {
                    return [
                        'title'     => $title,
                        'author'    => $author,
                        'publisher' => $publisher,
                        'year'      => $year,
                        'cover_url' => null
                    ];
                }
            }
            return null;
        }

        $title = null;
        $author = '';
        $publisher = '';
        $year = null;
        $coverUrl = null;

        // Try DOMDocument / XPath parsing first for precise structure matching
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if ($doc->loadHTML('<?xml encoding="UTF-8">' . $html)) {
            $xpath = new \DOMXPath($doc);

            // Title: H1/H2 inside .bookinfo or main content (excluding Cloudflare challenge text)
            $titleNode = $xpath->query('//div[contains(@class, "bookinfo")]//h1 | //div[contains(@class, "bookinfo")]//h2 | //div[contains(@class, "bookinfo")]//h3')->item(0);
            if ($titleNode) {
                $title = trim($titleNode->textContent);
            }

            // Paragraph key-value pairs (Authors, Publisher, Published)
            $pNodes = $xpath->query('//div[contains(@class, "bookinfo")]//p | //p');
            foreach ($pNodes as $p) {
                $text = trim($p->textContent);
                if (preg_match('/Author(?:s)?:\s*(.+)/i', $text, $m)) {
                    $author = trim($m[1]);
                } elseif (preg_match('/Publisher:\s*(.+)/i', $text, $m)) {
                    $publisher = trim($m[1]);
                } elseif (preg_match('/Published:\s*(.+)/i', $text, $m)) {
                    if (preg_match('/\b\d{4}\b/', $m[1], $ym)) {
                        $year = $ym[0];
                    }
                }
            }

            // Cover Image
            $imgNode = $xpath->query('//div[contains(@class, "image")]//img | //img[contains(@src, "isbnsearch") or contains(@src, "covers")]')->item(0);
            if ($imgNode && $imgNode->hasAttribute('src')) {
                $coverUrl = $imgNode->getAttribute('src');
            }
        }
        libxml_clear_errors();

        // Regex fallbacks if DOMDocument missed anything
        if (empty($title)) {
            if (preg_match('/<div\s+class="bookinfo"[^>]*>\s*<h[12][^>]*>(.*?)<\/h[12]>/i', $html, $m)) {
                $title = trim(html_entity_decode(strip_tags($m[1])));
            }
        }

        // Filter out Cloudflare bot protection / challenge pages or error pages
        $invalidTitles = ['please verify to continue', 'just a moment', 'attention required', 'book not found', '404 not found', 'access denied'];
        if (empty($title) || in_array(strtolower(trim($title)), $invalidTitles) || str_contains(strtolower($title), 'verify to continue')) {
            return null;
        }

        if (empty($author) && (preg_match('/<strong>\s*Authors?:\s*<\/strong>\s*([^<]+)/i', $html, $m) || preg_match('/Authors?:\s*([^\n<]+)/i', $html, $m))) {
            $author = trim(html_entity_decode(strip_tags($m[1])));
        }

        if (empty($publisher) && (preg_match('/<strong>\s*Publisher:\s*<\/strong>\s*([^<]+)/i', $html, $m) || preg_match('/Publisher:\s*([^\n<]+)/i', $html, $m))) {
            $publisher = trim(html_entity_decode(strip_tags($m[1])));
        }

        if (empty($year) && (preg_match('/<strong>\s*Published:\s*<\/strong>\s*([^<]+)/i', $html, $m) || preg_match('/Published:\s*([^\n<]+)/i', $html, $m))) {
            if (preg_match('/\b\d{4}\b/', $m[1], $ym)) {
                $year = $ym[0];
            }
        }

        if (empty($coverUrl)) {
            if (preg_match('/<div\s+class="image"[^>]*>\s*<img\s+src="([^"]+)"/i', $html, $m)) {
                $coverUrl = $m[1];
            } elseif (preg_match('/<img[^>]+src="([^"]*(?:isbnsearch|covers|book|images)[^"]*)"/i', $html, $m)) {
                $coverUrl = $m[1];
            }
        }

        return [
            'title'     => $title,
            'author'    => $author,
            'publisher' => $publisher,
            'year'      => $year,
            'cover_url' => $coverUrl
        ];
    }

    private function fetchFromIsbnSearchByPost(string $isbn): ?array
    {
        $ch = curl_init('https://isbnsearch.org/search');
        $cookieFile = WRITEPATH . 'cache/curl_cookies.txt';

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query(['searchQuery' => $isbn]),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Referer: https://isbnsearch.org/'
            ]
        ]);

        $html = curl_exec($ch);
        curl_close($ch);

        if (!$html || str_contains(strtolower($html), 'verify to continue')) {
            return null;
        }

        $title = null;
        $author = '';
        $publisher = '';
        $year = null;

        if (preg_match('/<h2[^>]*>\s*<a[^>]*>([^<]+)<\/a>/i', $html, $m)) {
            $title = trim(html_entity_decode($m[1]));
        }

        if (preg_match('/Author(?:s)?:\s*([^<\n]+)/i', $html, $m)) {
            $author = trim(html_entity_decode(strip_tags($m[1])));
        }

        if (preg_match('/Publisher:\s*([^<\n]+)/i', $html, $m)) {
            $publisher = trim(html_entity_decode(strip_tags($m[1])));
        }

        if (preg_match('/Published:\s*([^<\n]+)/i', $html, $m)) {
            if (preg_match('/\b\d{4}\b/', $m[1], $ym)) {
                $year = $ym[0];
            }
        }

        if (!empty($title)) {
            return [
                'title'     => $title,
                'author'    => $author,
                'publisher' => $publisher,
                'year'      => $year,
                'cover_url' => null
            ];
        }

        return null;
    }

    private function fetchFromGoogleBooks(string $isbn): ?array
    {
        $urls = [
            "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}",
            "https://www.googleapis.com/books/v1/volumes?q={$isbn}"
        ];

        foreach ($urls as $url) {
            $json = $this->httpGet($url);
            if ($json) {
                $res = json_decode($json, true);
                if (!empty($res['items'][0]['volumeInfo'])) {
                    $info = $res['items'][0]['volumeInfo'];
                    $year = null;
                    if (!empty($info['publishedDate'])) {
                        preg_match('/\b\d{4}\b/', $info['publishedDate'], $m);
                        $year = $m[0] ?? null;
                    }
                    return [
                        'title'     => $info['title'] ?? '',
                        'author'    => !empty($info['authors']) ? implode(', ', $info['authors']) : '',
                        'publisher' => $info['publisher'] ?? '',
                        'year'      => $year,
                        'cover_url' => isset($info['imageLinks']['thumbnail']) ? str_replace('http:', 'https:', $info['imageLinks']['thumbnail']) : null
                    ];
                }
            }
        }
        return null;
    }

    private function fetchFromOpenLibrary(string $isbn): ?array
    {
        $url = "https://openlibrary.org/api/books?bibkeys=ISBN:{$isbn}&format=json&jscmd=data";
        $json = $this->httpGet($url);
        if ($json) {
            $res = json_decode($json, true);
            $key = "ISBN:{$isbn}";
            if (!empty($res[$key])) {
                $info = $res[$key];
                $authors = !empty($info['authors']) ? array_column($info['authors'], 'name') : [];
                $publishers = !empty($info['publishers']) ? array_column($info['publishers'], 'name') : [];
                $year = null;
                if (!empty($info['publish_date'])) {
                    preg_match('/\b\d{4}\b/', $info['publish_date'], $m);
                    $year = $m[0] ?? null;
                }
                $cover = null;
                if (!empty($info['cover'])) {
                    $cover = $info['cover']['large'] ?? $info['cover']['medium'] ?? $info['cover']['small'] ?? null;
                }
                return [
                    'title'     => $info['title'] ?? '',
                    'author'    => implode(', ', $authors),
                    'publisher' => implode(', ', $publishers),
                    'year'      => $year,
                    'cover_url' => $cover
                ];
            }
        }
        return null;
    }

    private function httpGet(string $url): ?string
    {
        $ch = curl_init();
        $cookieFile = WRITEPATH . 'cache/curl_cookies.txt';

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                'Cache-Control: max-age=0'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && !empty($response)) {
            return $response;
        }

        return null;
    }
}
