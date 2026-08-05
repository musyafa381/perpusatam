<?php

namespace App\Controllers\Loans;

use App\Libraries\QRGenerator;
use App\Models\BookModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Method;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;

class LoansController extends ResourceController
{
    protected LoanModel $loanModel;
    protected MemberModel $memberModel;
    protected BookModel $bookModel;

    public function __construct()
    {
        $this->loanModel = new LoanModel;
        $this->memberModel = new MemberModel;
        $this->bookModel = new BookModel;

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
        $search = $this->request->getGet('search');

        $query = $this->loanModel
            ->select('loans.*, members.first_name, members.last_name, members.email, members.phone, members.address, members.uid as member_uid, COUNT(loans.id) as total_books, COUNT(CASE WHEN loans.return_date IS NOT NULL THEN 1 END) as returned_books, COUNT(CASE WHEN loans.return_date IS NULL THEN 1 END) as active_books, GROUP_CONCAT(DISTINCT book_items.item_code SEPARATOR ", ") as item_codes')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->where('loans.deleted_at', null);

        if (!empty($search)) {
            $query->groupStart()
                ->like('first_name', $search, insensitiveSearch: true)
                ->orLike('last_name', $search, insensitiveSearch: true)
                ->orLike('email', $search, insensitiveSearch: true)
                ->orLike('title', $search, insensitiveSearch: true)
                ->orLike('book_items.item_code', $search, insensitiveSearch: true)
                ->groupEnd();
        }

        // Group by member_id and date (up to minute or timestamp) so 1 transaction session displays as 1 row!
        $query->groupBy('loans.member_id, SUBSTRING(loans.loan_date, 1, 16)')
              ->having('active_books >', 0);

        $loans = $query->paginate($itemPerPage, 'loans');

        $data = [
            'loans'       => $loans,
            'pager'       => $this->loanModel->pager,
            'currentPage' => $this->request->getVar('page_loans') ?? 1,
            'itemPerPage' => $itemPerPage,
            'search'      => $search
        ];

        return view('loans/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($uid = null)
    {
        $this->ensureLoansHaveBookItems();

        $loan = $this->loanModel
            ->select('members.*, members.uid as member_uid, books.*, loans.*, loans.qr_code as loan_qr_code, book_stock.quantity as book_stock, racks.name as rack, categories.name as category, book_items.item_code')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->where('loans.uid', $uid)
            ->first();

        if (empty($loan)) {
            throw new PageNotFoundException('Loan not found');
        }

        if ($this->request->getGet('update-qr-code') && $loan['return_date'] == null) {
            $qrGenerator = new QRGenerator();
            $qrCodeLabel = substr($loan['first_name'] . ($loan['last_name'] ? " {$loan['last_name']}" : ''), 0, 12) . '_' . substr($loan['title'], 0, 12);
            $qrCode = $qrGenerator->generateQRCode(
                $loan['uid'],
                labelText: $qrCodeLabel,
                dir: LOANS_QR_CODE_PATH,
                filename: $qrCodeLabel
            );

            // delete former qr code
            deleteLoansQRCode($loan['qr_code']);

            $this->loanModel->update($loan['id'], ['qr_code' => $qrCode]);

            $loan = $this->loanModel
                ->select('members.*, members.uid as member_uid, books.*, loans.*, loans.qr_code as loan_qr_code, book_stock.quantity as book_stock, racks.name as rack, categories.name as category')
                ->join('members', 'loans.member_id = members.id', 'LEFT')
                ->join('books', 'loans.book_id = books.id', 'LEFT')
                ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
                ->join('racks', 'books.rack_id = racks.id', 'LEFT')
                ->join('categories', 'books.category_id = categories.id', 'LEFT')
                ->where('loans.uid', $uid)
                ->first();

            return redirect()->to("admin/loans/{$loan['uid']}");
        }

        // Fetch all books/items borrowed by this member in the same loan transaction session (matched up to the minute)
        $loanMinute = substr($loan['loan_date'], 0, 16);

        $allSessionLoans = $this->loanModel
            ->select('loans.*, books.title as book_title, books.author as book_author, books.publisher as book_publisher, books.year as book_year, books.book_cover, books.slug as book_slug, book_items.item_code, categories.name as category_name, racks.name as rack_name, racks.floor as rack_floor')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('loans.member_id', $loan['member_id'])
            ->where('loans.deleted_at', null)
            ->like('loans.loan_date', $loanMinute, 'after')
            ->findAll();

        if (empty($allSessionLoans)) {
            $allSessionLoans = [$loan];
        }

        $member = $this->memberModel->find($loan['member_id']);
        $tierDetails = \App\Models\MemberModel::getTierDetails($member);
        $activeLoansCount = $this->loanModel->where([
            'member_id'   => $loan['member_id'],
            'return_date' => null,
            'deleted_at'  => null
        ])->countAllResults();

        // Available book items for add item modal
        $bookItemModel = new \App\Models\BookItemModel();
        $availableItems = $bookItemModel
            ->select('book_items.*, books.title as book_title, books.author as book_author, books.isbn')
            ->join('books', 'book_items.book_id = books.id', 'LEFT')
            ->where('book_items.status', 'tersedia')
            ->where('book_items.deleted_at', null)
            ->where('books.deleted_at', null)
            ->orderBy('books.title', 'ASC')
            ->findAll();

        $data = [
            'loan'             => $loan,
            'allSessionLoans'  => $allSessionLoans,
            'member'           => $member,
            'tierDetails'      => $tierDetails,
            'activeLoansCount' => $activeLoansCount,
            'availableItems'   => $availableItems,
        ];

        return view('loans/show', $data);
    }

    public function searchMember()
    {
        if ($this->request->isAJAX()) {
            $param = $this->request->getVar('param');

            if (empty($param)) return;

            $members = $this->memberModel
                ->like('first_name', $param, insensitiveSearch: true)
                ->orLike('last_name', $param, insensitiveSearch: true)
                ->orLike('email', $param, insensitiveSearch: true)
                ->orLike('uid', $param, insensitiveSearch: true)
                ->findAll();

            $members = array_filter($members, function ($member) {
                return $member['deleted_at'] == null;
            });

            if (empty($members)) {
                return view('loans/member', ['msg' => 'Member tidak ditemukan']);
            }

            $members = array_map(function ($member) {
                $unreturnedCount = $this->loanModel->where([
                    'member_id'   => $member['id'],
                    'return_date' => null,
                    'deleted_at'  => null
                ])->countAllResults();
                $member['unreturned_count'] = $unreturnedCount;
                return $member;
            }, $members);

            return view('loans/member', ['members' => $members]);
        }

        return view('loans/search_member');
    }

    public function searchBook()
    {
        if ($this->request->isAJAX()) {
            $param = trim($this->request->getVar('param'));
            $memberUid = $this->request->getVar('memberUid');

            if (empty($param)) return;

            if (empty($memberUid)) {
                return view('loans/book', ['msg' => 'Member UID is empty']);
            }

            $bookItemModel = new \App\Models\BookItemModel();

            // Check if exact item_code match (Barcode scanner result)
            $exactItem = $bookItemModel
                ->select('book_items.*, books.id as b_id, books.title, books.slug, books.author, books.publisher, books.year, books.book_cover, books.category_id, books.rack_id, categories.name as category, racks.name as rack')
                ->join('books', 'book_items.book_id = books.id', 'LEFT')
                ->join('categories', 'books.category_id = categories.id', 'LEFT')
                ->join('racks', 'books.rack_id = racks.id', 'LEFT')
                ->where('book_items.item_code', $param)
                ->where('book_items.deleted_at', null)
                ->where('books.deleted_at', null)
                ->first();

            $matchedItemCode = null;
            if (!empty($exactItem)) {
                $matchedItemCode = $exactItem['item_code'];
            }

            // Find matching books
            $books = $this->bookModel
                ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
                ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
                ->join('categories', 'books.category_id = categories.id', 'LEFT')
                ->join('racks', 'books.rack_id = racks.id', 'LEFT')
                ->join('book_items', 'books.id = book_items.book_id', 'LEFT')
                ->groupStart()
                    ->like('books.title', $param, insensitiveSearch: true)
                    ->orLike('books.slug', $param, insensitiveSearch: true)
                    ->orLike('books.author', $param, insensitiveSearch: true)
                    ->orLike('books.publisher', $param, insensitiveSearch: true)
                    ->orLike('books.isbn', $param, insensitiveSearch: true)
                    ->orLike('book_items.item_code', $param, insensitiveSearch: true)
                ->groupEnd()
                ->groupBy('books.id')
                ->findAll();

            $books = array_filter($books, function ($book) {
                return $book['deleted_at'] == null;
            });

            if (empty($books)) {
                return view('loans/book', ['msg' => 'Buku / Barcode tidak ditemukan']);
            }

            $books = array_map(function ($book) use ($bookItemModel) {
                $newBook = $book;
                $remainingStock = $this->getRemainingBookStocks($book);
                $newBook['stock'] = $remainingStock;

                // Sync or generate items if quantity > items count
                $existingItems = $bookItemModel->where('book_id', $book['id'])->findAll();
                if (count($existingItems) < intval($book['quantity'])) {
                    $bookItemModel->syncBookItems($book['id'], intval($book['quantity']));
                }

                // Get available items/eksemplar for this book
                $availableItems = $bookItemModel
                    ->where('book_id', $book['id'])
                    ->groupStart()
                        ->where('status', 'available')
                        ->orWhere('status', 'tersedia')
                        ->orWhere('status', null)
                    ->groupEnd()
                    ->findAll();

                $newBook['available_items'] = $availableItems;
                return $newBook;
            }, $books);


            return view('loans/book', [
                'books'           => $books,
                'memberUid'       => $memberUid,
                'matchedItemCode' => $matchedItemCode
            ]);
        }


        $memberUid = $this->request->getVar('member-uid');

        if (empty($memberUid)) {
            session()->setFlashdata(['msg' => 'Select member first', 'error' => true]);
            return redirect()->to('admin/loans/new/members/search');
        }

        $member = $this->memberModel->where('uid', $memberUid)->first();

        if (empty($member)) {
            session()->setFlashdata(['msg' => 'Anggota tidak ditemukan', 'error' => true]);
            return redirect()->to('admin/loans/new/members/search');
        }

        // Validation: Block new borrowing if member has ANY unreturned active loans!
        $unreturnedCount = $this->loanModel->where([
            'member_id'   => $member['id'],
            'return_date' => null,
            'deleted_at'  => null
        ])->countAllResults();

        if ($unreturnedCount > 0) {
            session()->setFlashdata([
                'msg'   => '⛔ PEMINJAMAN DITOLAK: Anggota ' . esc("{$member['first_name']} {$member['last_name']}") . ' masih memiliki ' . $unreturnedCount . ' buku yang belum dikembalikan! Seluruh buku lama harus dikembalikan terlebih dahulu.',
                'error' => true
            ]);
            return redirect()->to('admin/loans/new/members/search');
        }

        return view('loans/search_book', ['member' => $member]);
    }

    public function getRemainingBookStocks($book)
    {
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

        return $book['quantity'] - $loanCount;
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new($validation = null, $oldInput = null)
    {
        if ($this->request->getMethod() !== Method::POST) {
            return redirect()->to('admin/loans/new/members/search');
        }

        $member = $this->memberModel
            ->where('uid', $this->request->getVar('member_uid'))
            ->first();

        if (empty($member)) {
            session()->setFlashdata(['msg' => 'Anggota tidak ditemukan', 'error' => true]);
            return redirect()->to('admin/loans/new/members/search');
        }

        // Validation: Block new borrowing if member has ANY unreturned active loans!
        $unreturnedCount = $this->loanModel->where([
            'member_id'   => $member['id'],
            'return_date' => null,
            'deleted_at'  => null
        ])->countAllResults();

        if ($unreturnedCount > 0) {
            session()->setFlashdata([
                'msg'   => '⛔ Transaksi Gagal: Anggota ' . esc("{$member['first_name']} {$member['last_name']}") . ' masih memiliki ' . $unreturnedCount . ' buku yang belum dikembalikan.',
                'error' => true
            ]);
            return redirect()->to('admin/loans/new/members/search');
        }

        $books = [];

        $bookSlugs = $this->request->getVar('slugs');
        $itemCodes = $this->request->getVar('item_codes');

        if (empty($bookSlugs)) {
            return redirect()->back();
        }

        $bookItemModel = new \App\Models\BookItemModel();
        $selectedItemCodesMap = [];
        $selectedItemsList = [];

        foreach ($bookSlugs as $index => $slug) {
            $book = $this->bookModel
                ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
                ->where('books.slug', $slug)->first();

            if (!empty($book)) {
                $book['stock'] = $this->getRemainingBookStocks($book);

                // Auto-sync/generate items if DB items count < book quantity
                $existingItems = $bookItemModel->where('book_id', $book['id'])->findAll();
                if (count($existingItems) < intval($book['quantity'] ?? 1)) {
                    $bookItemModel->syncBookItems($book['id'], intval($book['quantity'] ?? 1));
                }

                $book['available_items'] = $bookItemModel
                    ->where('book_id', $book['id'])
                    ->groupStart()
                        ->where('status', 'available')
                        ->orWhere('status', 'tersedia')
                        ->orWhere('status', null)
                    ->groupEnd()
                    ->findAll();

                $chosenItemCode = $itemCodes[$index] ?? null;
                $book['selected_item_code'] = $chosenItemCode;

                array_push($books, $book);

                if (!empty($chosenItemCode)) {
                    $selectedItemCodesMap[$slug] = $chosenItemCode;
                }
            }
        }



        $data = [
            'books'                => $books,
            'selectedItemCodesMap' => $selectedItemCodesMap,
            'member'               => $member,
            'validation'           => $validation ?? \Config\Services::validation(),
            'oldInput'             => $oldInput,
        ];

        return view('loans/create', $data);
    }


    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $bookSlugs = $this->request->getVar('slugs');
        if (empty($bookSlugs) || !is_array($bookSlugs)) {
            session()->setFlashdata(['msg' => 'Pilih minimal 1 buku untuk dipinjam', 'error' => true]);
            return redirect()->to('admin/loans/new/members/search');
        }

        $memberUid = $this->request->getVar('member_uid');
        if (empty($memberUid)) {
            session()->setFlashdata(['msg' => 'Data anggota tidak ditemukan', 'error' => true]);
            return redirect()->to('admin/loans/new/members/search');
        }

        $member = $this->memberModel->where('uid', $memberUid)->first();

        if (empty($member)) {
            session()->setFlashdata(['msg' => 'Anggota tidak ditemukan', 'error' => true]);
            return redirect()->to('admin/loans/new/members/search');
        }

        $tier = \App\Models\MemberModel::getTierDetails($member);
        $globalDuration = intval($this->request->getVar('global_duration') ?? 7);
        if ($globalDuration <= 0) {
            $globalDuration = 7;
        }

        // Collect selected item IDs from form
        $selectedItemIds = $this->request->getVar('selected_item_ids');
        if (empty($selectedItemIds) || !is_array($selectedItemIds)) {
            $selectedItemIds = [];
            foreach ($bookSlugs as $slug) {
                $items = $this->request->getVar('items-' . $slug);
                if (is_array($items)) {
                    foreach ($items as $itId) {
                        $selectedItemIds[] = (int)$itId;
                    }
                }
            }
        }

        if (empty($selectedItemIds)) {
            session()->setFlashdata(['msg' => 'Pilih minimal 1 eksemplar fisik buku untuk dipinjam', 'error' => true]);
            return redirect()->back()->withInput();
        }

        // Check active loan limit per tier
        $activeLoansCount = $this->loanModel->where([
            'member_id'   => $member['id'],
            'return_date' => null
        ])->countAllResults();

        $totalItemsCount = count($selectedItemIds);

        if (($activeLoansCount + $totalItemsCount) > $tier['max_loans']) {
            session()->setFlashdata([
                'msg'   => "Jumlah total eksemplar yang dipinjam ({$totalItemsCount}) melebihi kuota aktif member ({$tier['name']} maks {$tier['max_loans']} buku, aktif saat ini: {$activeLoansCount}).",
                'error' => true
            ]);
            return redirect()->back()->withInput();
        }

        $newLoanIds = [];
        $bookItemModel = new \App\Models\BookItemModel();
        $sharedTransactionTime = Time::now()->toDateTimeString();

        foreach ($selectedItemIds as $bookItemId) {
            $bookItemId = (int)$bookItemId;
            if ($bookItemId <= 0) {
                session()->setFlashdata([
                    'msg'   => "Gagal memproses peminjaman: Salah satu buku terpilih tidak memiliki eksemplar fisik yang dapat dipinjam (Stok Habis / 0).",
                    'error' => true
                ]);
                return redirect()->back()->withInput();
            }

            // Verify the selected item exists and is available
            $bookItem = $bookItemModel->find($bookItemId);
            if (!$bookItem) {
                session()->setFlashdata([
                    'msg'   => "Gagal memproses peminjaman: Eksemplar fisik buku tidak ditemukan di database.",
                    'error' => true
                ]);
                return redirect()->back()->withInput();
            }

            $book = $this->bookModel
                ->select('books.*, categories.name as category_name')
                ->join('categories', 'books.category_id = categories.id', 'LEFT')
                ->where('books.id', $bookItem['book_id'])->first();

            if (empty($book)) {
                continue;
            }

            $bookTitle = $book['title'] ?? 'terpilih';

            // Check Novel restriction for non-members (None tier)
            $isNovel = (stripos($book['category_name'] ?? '', 'novel') !== false);
            if ($isNovel && !$tier['allow_novel']) {
                session()->setFlashdata([
                    'msg'   => "Buku kategori Novel (\"{$bookTitle}\") hanya dapat dipinjam oleh Anggota berstatus minimal Silver Member.",
                    'error' => true
                ]);
                return redirect()->back()->withInput();
            }

            $itemStatus = strtolower($bookItem['status'] ?? 'tersedia');
            if ($itemStatus !== 'tersedia' && $itemStatus !== 'available') {
                session()->setFlashdata([
                    'msg'   => "Gagal memproses peminjaman: Eksemplar buku \"{$bookTitle}\" (Kode: {$bookItem['item_code']}) saat ini berstatus '{$bookItem['status']}' (sedang dipinjam / tidak tersedia).",
                    'error' => true
                ]);
                return redirect()->back()->withInput();
            }

            if (($bookItem['condition'] ?? 'baik') === 'hilang') {
                session()->setFlashdata([
                    'msg'   => "Gagal memproses peminjaman: Eksemplar buku \"{$bookTitle}\" berstatus 'Hilang'.",
                    'error' => true
                ]);
                return redirect()->back()->withInput();
            }

            $duration = intval($this->request->getVar('duration-' . $book['slug']) ?? $globalDuration);
            if ($duration <= 0) {
                $duration = $globalDuration;
            }

            $loanUid = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $qrGenerator = new QRGenerator();
            $qrCodeLabel = substr($member['first_name'] . ($member['last_name'] ? " {$member['last_name']}" : ''), 0, 12) . '_' . substr($book['title'], 0, 12);

            $qrCode = $qrGenerator->generateQRCode(
                data: $loanUid,
                labelText: $qrCodeLabel,
                dir: LOANS_QR_CODE_PATH,
                filename: $qrCodeLabel
            );

            $newLoan = [
                'book_id'      => $book['id'],
                'book_item_id' => $bookItemId,
                'quantity'     => 1,
                'member_id'    => $member['id'],
                'uid'          => $loanUid,
                'loan_date'    => $sharedTransactionTime,
                'due_date'     => Time::now()->addDays(intval($duration))->toDateTimeString(),
                'qr_code'      => $qrCode,
            ];

            $this->loanModel->insert($newLoan);
            $bookItemModel->update($bookItemId, ['status' => 'dipinjam']);
            array_push($newLoanIds, $this->loanModel->getInsertID());
        }

        if (empty($newLoanIds)) {
            session()->setFlashdata([
                'msg'   => 'Gagal menyimpan transaksi peminjaman. Tidak ada eksemplar fisik buku yang berhasil dipinjam (Stok Habis / 0).',
                'error' => true
            ]);
            return redirect()->back()->withInput();
        }

        session()->setFlashdata('msg', '🎉 Transaksi peminjaman berhasil disimpan! Silakan cetak struk transaksi di bawah ini.');
        if (!empty($newLoanIds[0])) {
            $firstLoan = $this->loanModel->find($newLoanIds[0]);
            if (!empty($firstLoan['uid'])) {
                return redirect()->to("admin/loans/receipt/{$firstLoan['uid']}?print=true");
            }
        }
        return redirect()->to('admin/loans');
    }

    /**
     * Display printable receipt for loan transaction
     */
    public function receipt($uid = null)
    {
        $loan = $this->loanModel
            ->select('members.*, members.uid as member_uid, books.*, loans.*, book_items.item_code')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->where('loans.uid', $uid)
            ->first();

        if (empty($loan)) {
            throw new PageNotFoundException('Loan transaction not found');
        }

        $loanMinute = substr($loan['loan_date'], 0, 16);

        $allSessionLoans = $this->loanModel
            ->select('loans.*, books.title as book_title, books.author as book_author, books.publisher as book_publisher, books.year as book_year, book_items.item_code, categories.name as category_name, racks.name as rack_name')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('loans.member_id', $loan['member_id'])
            ->like('loans.loan_date', $loanMinute, 'after')
            ->where('loans.return_date', null)
            ->findAll();

        if (empty($allSessionLoans)) {
            $allSessionLoans = [$loan];
        }

        $data = [
            'loan'            => $loan,
            'allSessionLoans' => $allSessionLoans,
        ];

        return view('loans/receipt', $data);
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    // public function edit($uid = null)
    // {
    //! Not implemented
    // }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    // public function update($uid = null)
    // {
    //! Not implemented
    // }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($uid = null)
    {
        $loan = $this->loanModel->where('uid', $uid)->first();

        if (empty($loan)) {
            throw new PageNotFoundException('Loan not found');
        }

        $loanMinute = substr($loan['loan_date'], 0, 16);
        $allSessionLoans = $this->loanModel
            ->where('member_id', $loan['member_id'])
            ->where('deleted_at', null)
            ->like('loan_date', $loanMinute, 'after')
            ->findAll();

        if (empty($allSessionLoans)) {
            $allSessionLoans = [$loan];
        }

        $bookItemModel = new \App\Models\BookItemModel();
        $countDeleted = 0;

        foreach ($allSessionLoans as $sLoan) {
            if ($this->loanModel->delete($sLoan['id'])) {
                $countDeleted++;
                if (!empty($sLoan['book_item_id'])) {
                    $bookItemModel->update($sLoan['book_item_id'], ['status' => 'tersedia']);
                }
                if (!empty($sLoan['qr_code'])) {
                    deleteLoansQRCode($sLoan['qr_code']);
                }
            }
        }

        session()->setFlashdata('msg', '🎉 Seluruh peminjaman (' . $countDeleted . ' buku) dalam transaksi ini berhasil dibatalkan dan unit buku telah dikembalikan ke rak.');
        return redirect()->to('admin/loans');
    }

    /**
     * Add a book item to an existing loan transaction session with Member Tier Limit Validation
     */
    public function addItem($uid = null)
    {
        $loan = $this->loanModel->where('uid', $uid)->first();
        if (empty($loan)) {
            session()->setFlashdata(['msg' => 'Peminjaman tidak ditemukan.', 'error' => true]);
            return redirect()->back();
        }

        $member = $this->memberModel->find($loan['member_id']);
        if (empty($member)) {
            session()->setFlashdata(['msg' => 'Data anggota tidak ditemukan.', 'error' => true]);
            return redirect()->to("admin/loans/{$uid}");
        }

        $tierDetails = \App\Models\MemberModel::getTierDetails($member);
        $activeLoansCount = $this->loanModel->where([
            'member_id'   => $loan['member_id'],
            'return_date' => null,
            'deleted_at'  => null
        ])->countAllResults();

        // Enforce member tier max loans limit!
        if ($activeLoansCount >= $tierDetails['max_loans']) {
            session()->setFlashdata([
                'msg'   => "Gagal menambah buku: Anggota " . esc($member['first_name'] . ' ' . $member['last_name']) . " (" . esc($tierDetails['name']) . ") telah mencapai batas peminjaman maksimal (" . esc($tierDetails['max_loans']) . " buku). Anggota saat ini sudah meminjam {$activeLoansCount} buku.",
                'error' => true
            ]);
            return redirect()->to("admin/loans/{$uid}");
        }

        $bookItemId = $this->request->getVar('book_item_id');
        if (empty($bookItemId)) {
            session()->setFlashdata(['msg' => 'Silakan pilih buku / eksemplar yang ingin ditambahkan.', 'error' => true]);
            return redirect()->to("admin/loans/{$uid}");
        }

        $bookItemModel = new \App\Models\BookItemModel();
        $item = $bookItemModel->find($bookItemId);

        if (empty($item) || $item['status'] !== 'tersedia') {
            session()->setFlashdata(['msg' => 'Eksemplar buku yang dipilih tidak tersedia untuk dipinjam saat ini.', 'error' => true]);
            return redirect()->to("admin/loans/{$uid}");
        }

        // Prevent duplicate borrowing of the exact same item
        $alreadyInSession = $this->loanModel->where([
            'member_id'    => $loan['member_id'],
            'book_item_id' => $bookItemId,
            'return_date'  => null,
            'deleted_at'   => null
        ])->first();
        if ($alreadyInSession) {
            session()->setFlashdata(['msg' => 'Eksemplar buku ini sudah ada dalam peminjaman aktif anggota.', 'error' => true]);
            return redirect()->to("admin/loans/{$uid}");
        }

        $book = $this->bookModel->find($item['book_id']);
        $qrGenerator = new \App\Libraries\QRGenerator();
        $newUid = 'LN' . date('YmdHis') . rand(100, 999);
        $qrCodeLabel = substr(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? ''), 0, 12) . '_' . substr($book['title'] ?? 'Buku', 0, 12);
        $qrCode = $qrGenerator->generateQRCode(
            $newUid,
            labelText: $qrCodeLabel,
            dir: LOANS_QR_CODE_PATH,
            filename: $qrCodeLabel
        );

        $this->loanModel->insert([
            'uid'          => $newUid,
            'member_id'    => $loan['member_id'],
            'book_id'      => $item['book_id'],
            'book_item_id' => $item['id'],
            'quantity'     => 1,
            'loan_date'    => $loan['loan_date'],
            'due_date'     => $loan['due_date'],
            'duration'     => $loan['duration'] ?? 7,
            'qr_code'      => $qrCode,
        ]);

        // Update book item status to dipinjam
        $bookItemModel->update($item['id'], ['status' => 'dipinjam']);

        session()->setFlashdata(['msg' => 'Buku "' . esc($book['title']) . '" (Kode: ' . esc($item['item_code']) . ') berhasil ditambahkan ke transaksi ini.']);
        return redirect()->to("admin/loans/{$uid}");
    }

    /**
     * Remove / cancel a single book item from a loan transaction session
     */
    public function removeItem($uid = null, $loanId = null)
    {
        $loanItem = $this->loanModel->find($loanId);
        if (empty($loanItem)) {
            session()->setFlashdata(['msg' => 'Item peminjaman tidak ditemukan.', 'error' => true]);
            return redirect()->to("admin/loans/{$uid}");
        }

        if (!empty($loanItem['return_date'])) {
            session()->setFlashdata(['msg' => 'Buku yang sudah dikembalikan tidak dapat dihapus dari transaksi.', 'error' => true]);
            return redirect()->to("admin/loans/{$uid}");
        }

        // Restore book_item status to tersedia
        if (!empty($loanItem['book_item_id'])) {
            $bookItemModel = new \App\Models\BookItemModel();
            $bookItemModel->update($loanItem['book_item_id'], ['status' => 'tersedia']);
        }

        if (!empty($loanItem['qr_code'])) {
            deleteLoansQRCode($loanItem['qr_code']);
        }

        $this->loanModel->delete($loanId);

        session()->setFlashdata(['msg' => 'Buku berhasil dihapus/dibatalkan dari transaksi ini dan unit dikembalikan ke rak.']);
        return redirect()->to("admin/loans/{$uid}");
    }

    /**
     * Auto-assign or create Kartu Buku (book_items) for any legacy loan missing book_item_id
     */
    private function ensureLoansHaveBookItems()
    {
        return; // Disabled background auto-creation of book_items
        $allActiveLoans = $this->loanModel->where('return_date', null)->findAll();
        if (empty($allActiveLoans)) {
            return;
        }

        $bookItemModel = new \App\Models\BookItemModel();
        $assignedInLoop = [];

        foreach ($allActiveLoans as $l) {
            $loanId = $l['id'];
            $bookId = $l['book_id'];
            $currentBookItemId = $l['book_item_id'];

            // If current item ID is null or already assigned to another loan in this loop:
            if (empty($currentBookItemId) || in_array($currentBookItemId, $assignedInLoop)) {
                // Find an existing book_item for this book that is NOT assigned yet
                $availableItem = null;
                if (!empty($assignedInLoop)) {
                    $availableItem = $bookItemModel
                        ->where('book_id', $bookId)
                        ->whereNotIn('id', $assignedInLoop)
                        ->first();
                } else {
                    $availableItem = $bookItemModel
                        ->where('book_id', $bookId)
                        ->first();
                }

                if ($availableItem) {
                    $newBookItemId = $availableItem['id'];
                } else {
                    // Create a brand new UNIQUE Kartu Buku (book_items) for this copy!
                    $book = $this->bookModel->find($bookId);
                    $cleanTitle = preg_replace('/[^A-Za-z0-9]/', '', $book['title'] ?? 'BOOK');
                    $codePrefix = strtolower(substr($cleanTitle, 0, 4));
                    if (strlen($codePrefix) < 3) $codePrefix = 'kb' . $bookId;

                    $newItemCode = $codePrefix . sprintf('%03d', rand(100, 999));
                    while ($bookItemModel->where('item_code', $newItemCode)->first()) {
                        $newItemCode = $codePrefix . sprintf('%03d', rand(100, 999));
                    }

                    $bookItemData = [
                        'book_id'     => $bookId,
                        'item_code'   => $newItemCode,
                        'status'      => 'dipinjam',
                        'condition'   => 'baik',
                        'copy_type'   => 'fisik',
                        'acquisition' => 'pembelian'
                    ];
                    $bookItemModel->insert($bookItemData);
                    $newBookItemId = $bookItemModel->getInsertID();

                    $stockModel = new \App\Models\BookStockModel();
                    $currentStock = $stockModel->where('book_id', $bookId)->first();
                    if ($currentStock) {
                        $countItems = $bookItemModel->where('book_id', $bookId)->countAllResults();
                        $stockModel->update($currentStock['id'], ['quantity' => $countItems]);
                    }
                }

                $this->loanModel->update($loanId, ['book_item_id' => $newBookItemId]);
                $assignedInLoop[] = $newBookItemId;
            } else {
                $assignedInLoop[] = $currentBookItemId;
            }
        }
    }
}
