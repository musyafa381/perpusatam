<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\CategoryModel;
use App\Models\MemberModel;

class Home extends BaseController
{
    protected BookModel $bookModel;
    protected CategoryModel $categoryModel;

    public function __construct()
    {
        $this->bookModel = new BookModel;
        $this->categoryModel = new CategoryModel;
    }

    /**
     * Official Public Library Portal (UNIDA Gontor Style)
     */
    public function index(): string
    {
        $search = $this->request->getGet('search');
        $categoryFilter = $this->request->getGet('category');

        $query = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, categories.id as category_id, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.deleted_at', null);

        if (!empty($categoryFilter)) {
            $query->where('categories.id', $categoryFilter);
        }

        if (!empty($search)) {
            $query->groupStart()
                ->like('books.title', $search, insensitiveSearch: true)
                ->orLike('books.author', $search, insensitiveSearch: true)
                ->orLike('books.publisher', $search, insensitiveSearch: true)
                ->orLike('books.isbn', $search, insensitiveSearch: true)
                ->groupEnd();
        }

        $latestBooks = $query->orderBy('books.id', 'DESC')->findAll(200);

        // Calculate available stock
        $loanModel = new \App\Models\LoanModel();
        $activeLoansGrouped = $loanModel
            ->select('book_id, SUM(COALESCE(quantity, 1)) as total_borrowed')
            ->where('return_date', null)
            ->groupBy('book_id')
            ->findAll();

        $borrowedMap = [];
        foreach ($activeLoansGrouped as $al) {
            $borrowedMap[$al['book_id']] = (int)$al['total_borrowed'];
        }

        foreach ($latestBooks as &$bk) {
            $totalQty = (int)($bk['quantity'] ?? 0);
            $borrowedQty = $borrowedMap[$bk['id']] ?? 0;
            $bk['quantity'] = max(0, $totalQty - $borrowedQty);
        }
        unset($bk);

        // Top 7 categories with most books
        $categories = $this->categoryModel
            ->select('categories.*, COUNT(books.id) as total_books')
            ->join('books', 'books.category_id = categories.id AND books.deleted_at IS NULL', 'LEFT')
            ->groupBy('categories.id')
            ->orderBy('total_books', 'DESC')
            ->findAll(7);

        // Portal Stats
        $memberModel = new MemberModel();
        $visitorLogModel = new \App\Models\VisitorLogModel();

        $totalBooksCount = $this->bookModel->where('deleted_at', null)->countAllResults();
        $db = \Config\Database::connect();
        $stockRow = $db->table('book_stock')->selectSum('quantity')->get()->getRow();
        $totalCopiesCount = max($totalBooksCount, (int)($stockRow->quantity ?? 0));

        $totalMembersCount = $memberModel->where('deleted_at', null)->countAllResults();
        $totalVisitorsCount = $visitorLogModel->countAllResults();
        $totalLoansCount = $loanModel->countAllResults();

        // Most borrowed / popular books (Top 6)
        $popularBooks = $this->bookModel
            ->select('books.*, book_stock.quantity, COUNT(loans.id) as total_borrowed, categories.name as category, racks.name as rack, racks.floor')
            ->join('loans', 'loans.book_id = books.id AND loans.deleted_at IS NULL', 'LEFT')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.deleted_at', null)
            ->groupBy('books.id')
            ->orderBy('total_borrowed', 'DESC')
            ->orderBy('books.id', 'DESC')
            ->findAll(6);

        foreach ($popularBooks as &$pb) {
            $totalQty = (int)($pb['quantity'] ?? 0);
            $borrowedQty = $borrowedMap[$pb['id']] ?? 0;
            $pb['quantity'] = max(0, $totalQty - $borrowedQty);
        }
        unset($pb);

        $data = [
            'latestBooks'        => $latestBooks,
            'popularBooks'       => $popularBooks,
            'categories'         => $categories,
            'search'             => $search,
            'categoryFilter'     => $categoryFilter,
            'totalBooksCount'    => $totalBooksCount,
            'totalCopiesCount'   => $totalCopiesCount,
            'totalMembersCount'  => $totalMembersCount,
            'totalVisitorsCount' => $totalVisitorsCount,
            'totalLoansCount'    => $totalLoansCount,
            'tvBanners'          => $tvBanners,
        ];

        return view('home/portal', $data);
    }

    /**
     * Digital TV Display Dashboard View
     */
    public function tvDisplay(): string
    {
        // Get highlighted / latest books
        $latestBooks = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.deleted_at', null)
            ->orderBy('books.id', 'DESC')
            ->findAll();

        $loanModel = new \App\Models\LoanModel();
        $activeLoansGrouped = $loanModel
            ->select('book_id, SUM(COALESCE(quantity, 1)) as total_borrowed')
            ->where('return_date', null)
            ->groupBy('book_id')
            ->findAll();

        $borrowedMap = [];
        foreach ($activeLoansGrouped as $al) {
            $borrowedMap[$al['book_id']] = (int)$al['total_borrowed'];
        }

        foreach ($latestBooks as &$bk) {
            $totalQty = (int)($bk['quantity'] ?? 0);
            $borrowedQty = $borrowedMap[$bk['id']] ?? 0;
            $bk['quantity'] = max(0, $totalQty - $borrowedQty);
        }
        unset($bk);

        $activeLoans = $loanModel
            ->select('loans.*, books.title as book_title, members.first_name, members.last_name, members.uid as member_uid')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->where('loans.return_date', null)
            ->orderBy('loans.loan_date', 'DESC')
            ->findAll(20);

        $visitorLogModel = new \App\Models\VisitorLogModel();
        $visitorSessionModel = new \App\Models\VisitorSessionModel();
        $activeSession = $visitorSessionModel->getActiveSession();
        $todayVisitorLogs = $visitorLogModel->getTodayLogs($activeSession['id'] ?? null);
        $todayVisitorCount = $visitorLogModel->getTodayCount();

        $categories = $this->categoryModel
            ->select('categories.*, COUNT(books.id) as total_books')
            ->join('books', 'books.category_id = categories.id AND books.deleted_at IS NULL', 'LEFT')
            ->groupBy('categories.id')
            ->findAll();

        $data = [
            'latestBooks'        => $latestBooks,
            'activeLoans'        => $activeLoans,
            'activeSession'      => $activeSession,
            'todayVisitorLogs'   => $todayVisitorLogs,
            'todayVisitorCount'  => $todayVisitorCount,
            'categories'         => $categories,
        ];

        return view('home/home', $data);
    }

    public function apiLiveData()
    {
        helper(['upload_helper']);

        $latestBooks = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.deleted_at', null)
            ->orderBy('books.id', 'DESC')
            ->findAll();

        $loanModel = new \App\Models\LoanModel();
        $activeLoansGrouped = $loanModel
            ->select('book_id, SUM(COALESCE(quantity, 1)) as total_borrowed')
            ->where('return_date', null)
            ->groupBy('book_id')
            ->findAll();

        $borrowedMap = [];
        foreach ($activeLoansGrouped as $al) {
            $borrowedMap[$al['book_id']] = (int)$al['total_borrowed'];
        }

        $allBooks = [];
        if (!empty($latestBooks)) {
            foreach ($latestBooks as $bk) {
                $totalQty = (int)($bk['quantity'] ?? 0);
                $borrowedQty = $borrowedMap[$bk['id']] ?? 0;
                $availStock = max(0, $totalQty - $borrowedQty);

                $coverUrl = !empty($bk['book_cover']) ? getBookCoverUrl($bk['book_cover']) : null;
                $hasRealCover = !empty($coverUrl) && ($coverUrl !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
                $allBooks[] = [
                    'title'    => $bk['title'],
                    'cat'      => $bk['category'] ?: 'Umum',
                    'cat_cls'  => 'tag-default',
                    'stock'    => $availStock,
                    'hasCover' => $hasRealCover,
                    'coverUrl' => $coverUrl,
                    'style'    => 'cover-default'
                ];
            }
        }

        $activeLoans = $loanModel
            ->select('loans.*, books.title as book_title, members.first_name, members.last_name, members.uid as member_uid')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->where('loans.return_date', null)
            ->orderBy('loans.loan_date', 'DESC')
            ->findAll(20);

        $loansPayload = [];
        if (!empty($activeLoans)) {
            foreach ($activeLoans as $ln) {
                $mName = trim(($ln['first_name'] ?? '') . ' ' . ($ln['last_name'] ?? ''));
                $loansPayload[] = [
                    'name'      => $mName ?: 'Anggota Perpustakaan',
                    'book'      => $ln['book_title'] ?? 'Buku Perpustakaan',
                    'loan_date' => !empty($ln['loan_date']) ? date('d M Y', strtotime($ln['loan_date'])) : '-',
                    'due_date'  => !empty($ln['due_date']) ? date('d M Y', strtotime($ln['due_date'])) : '-',
                ];
            }
        }

        $visitorLogModel = new \App\Models\VisitorLogModel();
        $todayVisitorLogs = $visitorLogModel->getTodayLogs(null);
        $todayVisitorCount = count($todayVisitorLogs);

        $visitorsPayload = [];
        if (!empty($todayVisitorLogs)) {
            foreach ($todayVisitorLogs as $vl) {
                $name = $vl['visitor_name'];
                if (empty($name)) {
                    $name = trim(($vl['first_name'] ?? '') . ' ' . ($vl['last_name'] ?? ''));
                }
                $visitorsPayload[] = [
                    'name' => $name ?: 'Pengunjung',
                    'time' => !empty($vl['created_at']) ? date('H:i', strtotime($vl['created_at'])) . ' WIB' : '-',
                ];
            }
        }

        $tvBanners = getTvBanners();

        return $this->response->setJSON([
            'status'            => true,
            'books'             => $allBooks,
            'loans'             => $loansPayload,
            'visitors'          => $visitorsPayload,
            'todayVisitorCount' => $todayVisitorCount,
            'tvBanners'         => $tvBanners
        ]);
    }

    public function book(): string
    {
        $itemPerPage = 20;
        $selectedCategory = $this->request->getGet('category');
        $keyword = $this->request->getGet('search');

        $query = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.deleted_at', null);

        if ($selectedCategory) {
            $query->where('books.category_id', $selectedCategory);
        }

        if ($keyword) {
            $query->groupStart()
                ->like('books.title', $keyword, insensitiveSearch: true)
                ->orLike('books.slug', $keyword, insensitiveSearch: true)
                ->orLike('books.author', $keyword, insensitiveSearch: true)
                ->orLike('books.publisher', $keyword, insensitiveSearch: true)
                ->orLike('books.isbn', $keyword, insensitiveSearch: true)
                ->orLike('books.ddc', $keyword, insensitiveSearch: true)
                ->orLike('books.call_number', $keyword, insensitiveSearch: true)
                ->groupEnd();
        }

        $books = $query->paginate($itemPerPage, 'books');

        // Calculate available stock (total stock minus active unreturned loans)
        $loanModel = new \App\Models\LoanModel();
        $activeLoansGrouped = $loanModel
            ->select('book_id, SUM(COALESCE(quantity, 1)) as total_borrowed')
            ->where('return_date', null)
            ->groupBy('book_id')
            ->findAll();

        $borrowedMap = [];
        foreach ($activeLoansGrouped as $al) {
            $borrowedMap[$al['book_id']] = (int)$al['total_borrowed'];
        }

        foreach ($books as &$bk) {
            $totalQty = (int)($bk['quantity'] ?? 0);
            $borrowedQty = $borrowedMap[$bk['id']] ?? 0;
            $bk['quantity'] = max(0, $totalQty - $borrowedQty);
        }
        unset($bk);

        $categories = $this->categoryModel
            ->select('categories.*, COUNT(books.id) as total_books')
            ->join('books', 'books.category_id = categories.id AND books.deleted_at IS NULL', 'LEFT')
            ->groupBy('categories.id')
            ->orderBy('total_books', 'DESC')
            ->findAll(7);

        $data = [
            'books'            => $books,
            'categories'       => $categories,
            'selectedCategory' => $selectedCategory,
            'pager'            => $this->bookModel->pager,
            'currentPage'      => $this->request->getVar('page_books') ?? 1,
            'itemPerPage'      => $itemPerPage,
            'search'           => $keyword
        ];

        return view('home/book', $data);
    }

    public function bookDetail($slugOrId)
    {
        $query = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.deleted_at', null);

        if (is_numeric($slugOrId)) {
            $query->where('books.id', $slugOrId);
        } else {
            $query->where('books.slug', $slugOrId);
        }

        $book = $query->first();

        if (!$book) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Buku tidak ditemukan.");
        }

        // Calculate available stock
        $loanModel = new \App\Models\LoanModel();
        $borrowed = $loanModel
            ->select('SUM(COALESCE(quantity, 1)) as total_borrowed')
            ->where('book_id', $book['id'])
            ->where('return_date', null)
            ->first();

        $totalQty = (int)($book['quantity'] ?? 0);
        $borrowedQty = (int)($borrowed['total_borrowed'] ?? 0);
        $book['quantity'] = max(0, $totalQty - $borrowedQty);

        // Related books (same category, fetch 5 books)
        $relatedBooks = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.deleted_at', null)
            ->where('books.id !=', $book['id'])
            ->where('books.category_id', $book['category_id'])
            ->findAll(5);

        foreach ($relatedBooks as &$rb) {
            $rBorrowed = $loanModel
                ->select('SUM(COALESCE(quantity, 1)) as total_borrowed')
                ->where('book_id', $rb['id'])
                ->where('return_date', null)
                ->first();
            $rTotal = (int)($rb['quantity'] ?? 0);
            $rBorrowedQty = (int)($rBorrowed['total_borrowed'] ?? 0);
            $rb['quantity'] = max(0, $rTotal - $rBorrowedQty);
        }
        unset($rb);

        $data = [
            'book'         => $book,
            'relatedBooks' => $relatedBooks
        ];

        return view('home/book_detail', $data);
    }
}

