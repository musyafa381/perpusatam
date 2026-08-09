<?php

namespace App\Controllers\Loans;

use App\Libraries\QRGenerator;
use App\Models\BookModel;
use App\Models\FineModel;
use App\Models\FinesPerDayModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;

class ReturnsController extends ResourceController
{
    protected LoanModel $loanModel;
    protected FineModel $fineModel;
    protected MemberModel $memberModel;
    protected BookModel $bookModel;

    public function __construct()
    {
        $this->loanModel = new LoanModel;
        $this->fineModel = new FineModel;
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

        if ($this->request->getGet('search')) {
            $keyword = $this->request->getGet('search');
            $rawLoans = $this->loanModel
                ->select('loans.*, members.first_name, members.last_name, members.email, members.phone, members.address, members.uid as member_uid, books.title, books.author, books.publisher, books.year, books.slug, fines.id as fine_id, fines.fine_amount, fines.amount_paid, fines.deleted_at as fine_deleted, book_items.item_code')
                ->join('members', 'loans.member_id = members.id', 'LEFT')
                ->join('books', 'loans.book_id = books.id', 'LEFT')
                ->join('fines', 'fines.loan_id = loans.id', 'LEFT')
                ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
                ->like('first_name', $keyword, insensitiveSearch: true)
                ->orLike('last_name', $keyword, insensitiveSearch: true)
                ->orLike('email', $keyword, insensitiveSearch: true)
                ->orLike('title', $keyword, insensitiveSearch: true)
                ->orLike('slug', $keyword, insensitiveSearch: true)
                ->orderBy('loans.return_date', 'DESC')
                ->findAll();
        } else {
            $rawLoans = $this->loanModel
                ->select('loans.*, members.first_name, members.last_name, members.email, members.phone, members.address, members.uid as member_uid, books.title, books.author, books.publisher, books.year, books.slug, fines.id as fine_id, fines.fine_amount, fines.amount_paid, fines.deleted_at as fine_deleted, book_items.item_code')
                ->join('members', 'loans.member_id = members.id', 'LEFT')
                ->join('books', 'loans.book_id = books.id', 'LEFT')
                ->join('fines', 'fines.loan_id = loans.id', 'LEFT')
                ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
                ->orderBy('loans.return_date', 'DESC')
                ->findAll();
        }

        $rawLoans = array_filter($rawLoans, function ($loan) {
            return $loan['deleted_at'] == null && $loan['return_date'] != null && $loan['fine_deleted'] == null;
        });

        // Verify that each transaction session has 0 unreturned books before displaying in Returns table
        $fullyReturnedSessions = [];
        $groupedTransactions = [];

        foreach ($rawLoans as $loan) {
            $sessionTime = substr($loan['loan_date'], 0, 16);
            $sessionKey = $loan['member_id'] . '_' . $sessionTime;

            if (!isset($fullyReturnedSessions[$sessionKey])) {
                $unreturnedCount = $this->loanModel
                    ->where('member_id', $loan['member_id'])
                    ->where('SUBSTRING(loan_date, 1, 16)', $sessionTime)
                    ->where('return_date', null)
                    ->where('deleted_at', null)
                    ->countAllResults();

                $fullyReturnedSessions[$sessionKey] = ($unreturnedCount === 0);
            }

            // Only add to /admin/returns if ALL books in the transaction session are returned!
            if (!$fullyReturnedSessions[$sessionKey]) {
                continue;
            }

            if (!isset($groupedTransactions[$sessionKey])) {
                $groupedTransactions[$sessionKey] = [
                    'session_key'   => $sessionKey,
                    'primary_uid'   => $loan['uid'],
                    'first_name'    => $loan['first_name'],
                    'last_name'     => $loan['last_name'],
                    'member_uid'    => $loan['member_uid'],
                    'loan_date'     => $loan['loan_date'],
                    'due_date'      => $loan['due_date'],
                    'return_date'   => $loan['return_date'],
                    'is_fined'      => false,
                    'is_fine_paid'  => true,
                    'items'         => []
                ];
            }

            $groupedTransactions[$sessionKey]['items'][] = [
                'title'     => $loan['title'],
                'author'    => $loan['author'],
                'item_code' => $loan['item_code'],
                'year'      => $loan['year'],
                'uid'       => $loan['uid']
            ];

            if ($loan['fine_id'] != null) {
                $groupedTransactions[$sessionKey]['is_fined'] = true;
                $paid = ($loan['amount_paid'] ?? 0) >= $loan['fine_amount'];
                if (!$paid) {
                    $groupedTransactions[$sessionKey]['is_fine_paid'] = false;
                }
            }
        }

        $groupedTransactions = array_values($groupedTransactions);

        // Manual pagination for grouped transactions
        $currentPage = (int) ($this->request->getVar('page_returns') ?? 1);
        $totalItems = count($groupedTransactions);
        $offset = ($currentPage - 1) * $itemPerPage;
        $pagedTransactions = array_slice($groupedTransactions, $offset, $itemPerPage);

        $pager = \Config\Services::pager();
        $pagerLinks = $pager->makeLinks($currentPage, $itemPerPage, $totalItems, 'my_pager', 0, 'returns');

        $data = [
            'transactions'  => $pagedTransactions,
            'pager'         => $pagerLinks,
            'currentPage'   => $currentPage,
            'itemPerPage'   => $itemPerPage,
            'search'        => $this->request->getGet('search') ?? '',
        ];

        return view('returns/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($uid = null)
    {
        $loan = $this->loanModel
            ->select('members.*, members.uid as member_uid, books.*, fines.*, fines.id as fine_id, loans.*, loans.return_condition as return_condition, loans.qr_code as loan_qr_code, book_stock.quantity as book_stock, racks.name as rack, categories.name as category, book_items.item_code, book_items.condition as item_condition')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('fines', 'fines.loan_id = loans.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->where('loans.uid', $uid)
            ->where("return_date IS NOT NULL")
            ->first();

        if (empty($loan)) {
            throw new PageNotFoundException('Loan not found');
        }

        // Retrieve all returned books in the same transaction session
        $loanDateMin = substr($loan['loan_date'], 0, 16);
        $transactionLoans = $this->loanModel
            ->select('members.*, members.uid as member_uid, books.*, fines.*, fines.id as fine_id, loans.*, loans.return_condition as return_condition, loans.qr_code as loan_qr_code, book_stock.quantity as book_stock, racks.name as rack, categories.name as category, book_items.item_code, book_items.condition as item_condition')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('fines', 'fines.loan_id = loans.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->where('loans.member_id', $loan['member_id'])
            ->where('SUBSTRING(loans.loan_date, 1, 16)', $loanDateMin)
            ->where("return_date IS NOT NULL")
            ->findAll();



        if (empty($transactionLoans)) {
            $transactionLoans = [$loan];
        }

        if ($this->request->getGet('update-qr-code')) {
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

            return redirect()->to("admin/returns/{$loan['uid']}");
        }

        $data = [
            'loan'             => $loan,
            'transactionLoans' => $transactionLoans,
        ];

        return view('returns/show', $data);
    }

    public function searchLoan()
    {
        if ($this->request->isAJAX()) {
            $param = trim((string) $this->request->getVar('param'));

            if (empty($param)) return;

            // Strip optional 'TRX-' or 'trx-' prefix if scanned from receipt label
            $cleanParam = preg_replace('/^TRX-?/i', '', $param);

            // Check if param matches a specific loan UID first
            $matchedLoan = $this->loanModel
                ->groupStart()
                    ->where('uid', $param)
                    ->orWhere('uid', $cleanParam)
                ->groupEnd()
                ->where('deleted_at', null)
                ->where('return_date', null)
                ->first();

            $query = $this->loanModel
                ->select('loans.*, members.first_name, members.last_name, members.email, members.phone, members.address, members.uid as member_uid, COUNT(loans.id) as total_books, GROUP_CONCAT(DISTINCT books.title SEPARATOR " || ") as book_titles, GROUP_CONCAT(DISTINCT book_items.item_code SEPARATOR ", ") as item_codes')
                ->join('members', 'loans.member_id = members.id', 'LEFT')
                ->join('books', 'loans.book_id = books.id', 'LEFT')
                ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
                ->where('loans.deleted_at', null)
                ->where('loans.return_date', null);

            if ($matchedLoan) {
                // If scanned by specific Loan UID, aggregate all loans from that same borrowing session
                $sessionTime = substr($matchedLoan['loan_date'], 0, 16);
                $query->where('loans.member_id', $matchedLoan['member_id'])
                      ->where('SUBSTRING(loans.loan_date, 1, 16)', $sessionTime);
            } else {
                $query->groupStart()
                    ->like('first_name', $param, insensitiveSearch: true)
                    ->orLike('last_name', $param, insensitiveSearch: true)
                    ->orLike('email', $param, insensitiveSearch: true)
                    ->orLike('title', $param, insensitiveSearch: true)
                    ->orLike('author', $param, insensitiveSearch: true)
                    ->orLike('publisher', $param, insensitiveSearch: true)
                    ->orLike('book_items.item_code', $param, insensitiveSearch: true)
                    ->orLike('loans.uid', $param, insensitiveSearch: true)
                    ->orLike('loans.uid', $cleanParam, insensitiveSearch: true)
                    ->orLike('members.uid', $param, insensitiveSearch: true)
                    ->orLike('members.uid', $cleanParam, insensitiveSearch: true)
                    ->groupEnd();
            }

            $query->groupBy('loans.member_id, SUBSTRING(loans.loan_date, 1, 16)');

            $loans = $query->findAll();

            if (empty($loans)) {
                return view('returns/loan', ['msg' => 'Peminjaman tidak ditemukan']);
            }

            return view('returns/loan', ['loans' => $loans]);
        }

        return view('returns/search_loan');
    }


    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        $loanUid = $this->request->getVar('loan-uid');

        if (empty($loanUid)) {
            session()->setFlashdata(['msg' => 'Pilih peminjaman terlebih dahulu', 'error' => true]);
            return redirect()->to('admin/returns/new/search');
        }

        $targetLoan = $this->loanModel
            ->where('uid', $loanUid)
            ->where('deleted_at', null)
            ->where('return_date', null)
            ->first();

        // If the specific loan UID is already returned (partial return scenario),
        // look up the session and find any remaining unreturned loan to use instead.
        if (empty($targetLoan)) {
            $returnedLoan = $this->loanModel
                ->where('uid', $loanUid)
                ->where('deleted_at', null)
                ->first();

            if (!empty($returnedLoan)) {
                $sessionTime = substr($returnedLoan['loan_date'], 0, 16);
                $targetLoan = $this->loanModel
                    ->where('member_id', $returnedLoan['member_id'])
                    ->where('SUBSTRING(loan_date, 1, 16)', $sessionTime)
                    ->where('deleted_at', null)
                    ->where('return_date', null)
                    ->first();
            }

            if (empty($targetLoan)) {
                throw new PageNotFoundException('Loan not found');
            }
        }

        $loanDateMin = substr($targetLoan['loan_date'], 0, 16);
        $loans = $this->loanModel
            ->select('loans.id as loan_id, loans.id as id, loans.uid as uid, loans.uid as loan_uid, loans.member_id, loans.book_id, loans.book_item_id, loans.loan_date, loans.due_date, members.first_name, members.last_name, members.email, members.phone, members.address, members.member_type, members.institution, members.class_level, members.uid as member_uid, books.title, books.author, books.publisher, books.slug, books.year, categories.name as category, book_items.item_code, book_items.price as item_price')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->where('loans.member_id', $targetLoan['member_id'])
            ->where('SUBSTRING(loans.loan_date, 1, 16)', $loanDateMin)
            ->where('loans.deleted_at', null)
            ->where('loans.return_date', null)
            ->findAll();

        if (empty($loans)) {
            throw new PageNotFoundException('Loan not found');
        }

        $data = [
            'loan'       => $loans[0],
            'loans'      => $loans,
            'validation' => $validation ?? \Config\Services::validation()
        ];

        return view('returns/create', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $date = Time::parse($this->request->getVar('date') ?? 'now', locale: 'id');
        $loanUid = $this->request->getVar('loan_uid');

        $targetLoan = $this->loanModel->where('uid', $loanUid)->first();

        if (empty($targetLoan)) {
            throw new PageNotFoundException('Loan not found');
        }

        $loanDateMin = substr($targetLoan['loan_date'], 0, 16);
        $loans = $this->loanModel
            ->where('member_id', $targetLoan['member_id'])
            ->where('SUBSTRING(loan_date, 1, 16)', $loanDateMin)
            ->where('deleted_at', null)
            ->where('return_date', null)
            ->findAll();

        $targetSingleLoanId = $this->request->getPost('target_loan_id');
        if (!empty($targetSingleLoanId)) {
            $loans = array_values(array_filter($loans, function ($l) use ($targetSingleLoanId) {
                return (string)$l['id'] === (string)$targetSingleLoanId;
            }));
        }

        if (empty($loans)) {
            session()->setFlashdata(['msg' => 'Buku yang dipilih sudah dikembalikan sebelumnya.', 'error' => true]);
            return redirect()->to("admin/loans/{$targetLoan['uid']}");
        }

        $bookItemModel = new \App\Models\BookItemModel();
        $conditionsInput = $this->request->getPost('conditions') ?? [];
        $conditionNotesInput = $this->request->getPost('condition_notes') ?? [];
        $itemPricesInput = $this->request->getPost('item_prices') ?? [];
        $dispensationsInput = $this->request->getPost('dispensation') ?? [];

        helper('library');

        $damagedCount = 0;
        $lostCount = 0;

        $conditionLogModel = new \App\Models\BookItemConditionLogModel();

        foreach ($loans as $loan) {
            $loanDueDate = Time::parse($loan['due_date'], locale: 'id');
            $isLate = $date->isAfter($loanDueDate);

            $chosenCondition = strtolower($conditionsInput[$loan['id']] ?? 'baik');
            if (!in_array($chosenCondition, ['baik', 'rusak', 'hilang'])) {
                $chosenCondition = 'baik';
            }

            // 1. Calculate late fine using smart helper (gender schedule & closed days aware)
            $lateFine = 0;
            $isDispensed = !empty($dispensationsInput[$loan['id']]);
            if ($isLate && !$isDispensed) {
                $calc = calculate_loan_fine($loan, $date->toDateTimeString());
                $lateFine = $calc['fine_amount'];
            }

            // 2. Calculate condition fine (Rusak = 50% book price, Hilang = 100% book price)
            $conditionFine = 0;
            $bookPrice = 50000; // default fallback book price if item price not set

            if (!empty($loan['book_item_id'])) {
                $bookItem = $bookItemModel->find($loan['book_item_id']);
                if ($bookItem && !empty($bookItem['price']) && floatval($bookItem['price']) > 0) {
                    $bookPrice = floatval($bookItem['price']);
                }

                $customPrice = floatval($itemPricesInput[$loan['id']] ?? 0);
                if ($customPrice > 0) {
                    $bookPrice = $customPrice;
                }

                $condNote = trim($conditionNotesInput[$loan['id']] ?? '');
                if (empty($condNote)) {
                    if ($chosenCondition === 'rusak') $condNote = 'Dikembalikan dalam kondisi rusak.';
                    elseif ($chosenCondition === 'hilang') $condNote = 'Dikembalikan dalam kondisi hilang.';
                    else $condNote = 'Dikembalikan dalam kondisi baik.';
                }

                $newStatus = 'tersedia';
                $bookItemModel->update($loan['book_item_id'], [
                    'status'         => $newStatus,
                    'condition'      => $chosenCondition,
                    'condition_note' => $condNote,
                    'price'          => $bookPrice
                ]);

                // Record history log
                $conditionLogModel->save([
                    'book_item_id'    => $loan['book_item_id'],
                    'loan_id'         => $loan['id'],
                    'member_id'       => $loan['member_id'],
                    'condition_state' => $chosenCondition,
                    'condition_note'  => $condNote,
                    'recorded_by'     => auth()->id()
                ]);

                if ($chosenCondition === 'rusak') {
                    $damagedCount++;
                    $conditionFine = 0.5 * $bookPrice;
                } elseif ($chosenCondition === 'hilang') {
                    $lostCount++;
                    $conditionFine = 1.0 * $bookPrice;
                }
            }

            $totalFineForLoan = $lateFine + $conditionFine;

            // Always update loan return_date & condition (Loan is marked as Returned)
            $updateData = [
                'return_date'      => $date->toDateTimeString(),
                'return_condition' => $chosenCondition,
            ];
            if (!$isLate) {
                deleteLoansQRCode($loan['qr_code']);
                $updateData['qr_code'] = null;
            }
            $this->loanModel->update($loan['id'], $updateData);

            // Record Fine if total fine > 0
            if ($totalFineForLoan > 0) {
                $existingFine = $this->fineModel->where('loan_id', $loan['id'])->first();
                if (!empty($existingFine)) {
                    $this->fineModel->update($existingFine['id'], [
                        'fine_amount' => $totalFineForLoan
                    ]);
                } else {
                    $this->fineModel->save([
                        'loan_id'     => $loan['id'],
                        'fine_amount' => $totalFineForLoan,
                        'amount_paid' => 0
                    ]);
                }
            }
        }

        // Check if there are still unreturned books in this transaction session
        $remainingUnreturned = $this->loanModel
            ->where('member_id', $targetLoan['member_id'])
            ->where('SUBSTRING(loan_date, 1, 16)', $loanDateMin)
            ->where('return_date', null)
            ->where('deleted_at', null)
            ->countAllResults();

        if ($remainingUnreturned > 0) {
            $noteMsg = 'Pengembalian sebagian berhasil disimpan! Buku yang dikembalikan kini telah tersedia di rak, sementara ' . $remainingUnreturned . ' buku lainnya tetap tercatat dipinjam.';
            if ($damagedCount > 0 || $lostCount > 0) {
                $noteMsg .= " Catatan sanksi: Surat pertanggungjawaban telah dibuat.";
                session()->setFlashdata(['msg' => $noteMsg, 'error' => false]);
                return redirect()->to('admin/returns/responsibility-letter/' . $targetLoan['uid'] . '?print=true');
            }
            session()->setFlashdata(['msg' => $noteMsg, 'error' => false]);
            return redirect()->to('admin/loans/' . $targetLoan['uid']);
        } else {
            $noteMsg = 'Seluruh buku dalam transaksi telah lengkap dikembalikan. Transaksi kini resmi dipindahkan ke tabel Pengembalian.';
            if ($damagedCount > 0 || $lostCount > 0) {
                $noteMsg .= " Catatan sanksi: Surat pertanggungjawaban telah dibuat.";
                session()->setFlashdata(['msg' => $noteMsg, 'error' => false]);
                return redirect()->to('admin/returns/responsibility-letter/' . $targetLoan['uid'] . '?print=true');
            }
            session()->setFlashdata(['msg' => $noteMsg, 'error' => false]);
            return redirect()->to('admin/returns/' . $targetLoan['uid']);
        }
    }

    /**
     * Render Printable Surat Pernyataan Pertanggungjawaban Kerusakan/Kehilangan Buku (PDF)
     */
    public function responsibilityLetter($uid = null)
    {
        $loan = $this->loanModel
            ->select('loans.*, members.first_name, members.last_name, members.uid as member_uid, members.phone, members.email, members.address, members.donated_books_count, members.manual_tier')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->where('loans.uid', $uid)
            ->first();

        if (empty($loan)) {
            throw new PageNotFoundException('Data Peminjaman / Pengembalian tidak ditemukan.');
        }

        $tier = \App\Models\MemberModel::getTierDetails($loan);
        $loan['tier_name'] = $tier['name'];

        $loanDateMin = substr($loan['loan_date'], 0, 16);
        $transactionLoans = $this->loanModel
            ->select('loans.*, books.title, books.isbn, books.author, books.publisher, book_items.item_code, book_items.price as item_price, fines.fine_amount')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->join('fines', 'fines.loan_id = loans.id', 'LEFT')
            ->where('loans.member_id', $loan['member_id'])
            ->where('SUBSTRING(loans.loan_date, 1, 16)', $loanDateMin)
            ->whereIn('loans.return_condition', ['rusak', 'hilang'])
            ->findAll();

        if (empty($transactionLoans)) {
            session()->setFlashdata(['msg' => 'Tidak ada buku berkondisi rusak atau hilang pada transaksi ini.', 'error' => true]);
            return redirect()->to("admin/returns/{$uid}");
        }

        $data = [
            'loan'             => $loan,
            'transactionLoans' => $transactionLoans,
            'letterNumber'     => 'SPK/PERPUS/' . date('Ymd', strtotime($loan['return_date'] ?? 'now')) . '/' . $loan['uid']
        ];

        return view('returns/responsibility_letter', $data);
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
        $targetLoan = $this->loanModel->where('uid', $uid)->first();

        if (empty($targetLoan)) {
            throw new PageNotFoundException('Transaksi pengembalian tidak ditemukan');
        }

        // Fetch all returned loans in the same transaction session
        $sessionTime = substr($targetLoan['loan_date'], 0, 16);
        $transactionLoans = $this->loanModel
            ->select('loans.*, members.first_name, members.last_name, books.title')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->where('loans.member_id', $targetLoan['member_id'])
            ->where('SUBSTRING(loans.loan_date, 1, 16)', $sessionTime)
            ->where('loans.return_date IS NOT NULL')
            ->findAll();

        if (empty($transactionLoans)) {
            session()->setFlashdata(['msg' => 'Tidak ada transaksi pengembalian yang dapat dibatalkan', 'error' => true]);
            return redirect()->to('admin/returns');
        }

        $bookItemModel = new \App\Models\BookItemModel();
        $qrGenerator = new QRGenerator();

        foreach ($transactionLoans as $loan) {
            $qrCode = $loan['qr_code'];
            if (empty($qrCode)) {
                $qrCodeLabel = substr($loan['first_name'] . ($loan['last_name'] ? " {$loan['last_name']}" : ''), 0, 12) . '_' . substr($loan['title'], 0, 12);
                $qrCode = $qrGenerator->generateQRCode(
                    data: $loan['uid'],
                    labelText: $qrCodeLabel,
                    dir: LOANS_QR_CODE_PATH,
                    filename: $qrCodeLabel
                );
            }

            // 1. Reset return date & condition back to active loan
            $this->loanModel->update($loan['id'], [
                'return_date'      => null,
                'return_condition' => null,
                'qr_code'          => $qrCode
            ]);

            // 2. Restore book item copy status to 'dipinjam' & condition to 'baik'
            if (!empty($loan['book_item_id'])) {
                $bookItemModel->update($loan['book_item_id'], [
                    'status'    => 'dipinjam',
                    'condition' => 'baik'
                ]);
            }

            // 3. Remove fine record for this loan if any
            $fine = $this->fineModel->where('loan_id', $loan['id'])->first();
            if (!empty($fine)) {
                $this->fineModel->delete($fine['id']);
            }
        }

        $count = count($transactionLoans);
        session()->setFlashdata([
            'msg'   => "🎉 Pembatalan pengembalian berhasil! Seluruh paket transaksi ({$count} buku) telah dikembalikan ke Peminjaman Aktif.",
            'error' => false
        ]);

        return redirect()->to('admin/loans');
    }
}
