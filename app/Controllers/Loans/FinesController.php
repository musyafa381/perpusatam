<?php

namespace App\Controllers\Loans;

use App\Models\BookModel;
use App\Models\FineModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;

class FinesController extends ResourceController
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

        $query = $this->loanModel
            ->select('members.first_name, members.last_name, members.email, members.phone, members.address, members.uid as member_uid, SUM(fines.fine_amount) as fine_amount, SUM(fines.amount_paid) as amount_paid, MAX(fines.paid_at) as paid_at, MAX(fines.created_at) as fine_created_at, COUNT(loans.id) as total_books, GROUP_CONCAT(DISTINCT loans.return_condition) as return_conditions, loans.*')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('fines', 'fines.loan_id = loans.id', 'INNER')
            ->where('loans.deleted_at', null)
            ->where('loans.return_date IS NOT NULL')
            ->where('fines.deleted_at', null);

        if ($this->request->getGet('search')) {
            $keyword = $this->request->getGet('search');
            $query->groupStart()
                ->like('first_name', $keyword, insensitiveSearch: true)
                ->orLike('last_name', $keyword, insensitiveSearch: true)
                ->orLike('email', $keyword, insensitiveSearch: true)
                ->orLike('title', $keyword, insensitiveSearch: true)
                ->orLike('loans.uid', $keyword, insensitiveSearch: true)
                ->orLike('members.uid', $keyword, insensitiveSearch: true)
                ->groupEnd();
        }

        $query->groupBy('loans.member_id, SUBSTRING(loans.loan_date, 1, 16)');
        $rawFines = $query->findAll();

        $paidOffFilter = ($this->request->getVar('paid-off') ?? 'false') === 'true';

        if ($paidOffFilter) {
            $fines = array_filter($rawFines, function ($fine) {
                return $fine['paid_at'] != null || ($fine['amount_paid'] ?? 0) >= $fine['fine_amount'];
            });
        } else {
            $fines = array_filter($rawFines, function ($fine) {
                return $fine['paid_at'] == null && ($fine['amount_paid'] ?? 0) < $fine['fine_amount'];
            });
        }

        $fines = array_values($fines);

        // Manual pagination for grouped fines
        $currentPage = (int) ($this->request->getVar('page_fines') ?? 1);
        $totalItems = count($fines);
        $offset = ($currentPage - 1) * $itemPerPage;
        $pagedFines = array_slice($fines, $offset, $itemPerPage);

        $pager = \Config\Services::pager();
        $pagerLinks = $pager->makeLinks($currentPage, $itemPerPage, $totalItems, 'my_pager', 0, 'fines');

        $data = [
            'paidOffFilter' => $paidOffFilter,
            'fines'         => $pagedFines,
            'pager'         => $pagerLinks,
            'currentPage'   => $currentPage,
            'itemPerPage'   => $itemPerPage,
            'search'        => $this->request->getGet('search') ?? ''
        ];

        return view('fines/index', $data);
    }


    public function searchReturn()
    {
        if ($this->request->isAJAX()) {
            $param = $this->request->getVar('param');

            if (empty($param)) return;

            $returns = $this->loanModel
                ->select('members.*, books.*, fines.*, fines.id as fine_id, fines.deleted_at as fine_deleted, loans.*')
                ->join('members', 'loans.member_id = members.id', 'LEFT')
                ->join('books', 'loans.book_id = books.id', 'LEFT')
                ->join('fines', 'fines.loan_id = loans.id', 'INNER')
                ->like('first_name', $param, insensitiveSearch: true)
                ->orLike('last_name', $param, insensitiveSearch: true)
                ->orLike('email', $param, insensitiveSearch: true)
                ->orLike('title', $param, insensitiveSearch: true)
                ->orLike('author', $param, insensitiveSearch: true)
                ->orLike('publisher', $param, insensitiveSearch: true)
                ->orWhere('loans.uid', $param)
                ->orWhere('members.uid', $param)
                ->findAll();

            $returns = array_filter($returns, function ($return) {
                return $return['deleted_at'] == null && $return['return_date'] != null && $return['fine_deleted'] == null;
            });

            if (empty($returns)) {
                return view('fines/return', ['msg' => 'Loan not found']);
            }

            return view('fines/return', ['returns' => $returns]);
        }

        return view('fines/search_return');
    }

    public function pay($uid = null, $validation = null, $oldInput = null)
    {
        $targetLoan = $this->loanModel
            ->select('loans.*, members.first_name, members.last_name, members.email, members.phone, members.address')
            ->join('members', 'loans.member_id = members.id', 'LEFT')
            ->where('loans.uid', $uid)
            ->first();

        if (empty($targetLoan)) {
            throw new PageNotFoundException('Return not found');
        }

        $sessionTime = substr($targetLoan['loan_date'], 0, 16);
        $transactionLoans = $this->loanModel
            ->select('loans.*, books.title, books.slug, book_items.item_code, book_items.price as item_price, fines.id as fine_id, fines.fine_amount, fines.amount_paid, fines.paid_at')
            ->join('books', 'loans.book_id = books.id', 'LEFT')
            ->join('book_items', 'loans.book_item_id = book_items.id', 'LEFT')
            ->join('fines', 'fines.loan_id = loans.id', 'INNER')
            ->where('loans.member_id', $targetLoan['member_id'])
            ->where('SUBSTRING(loans.loan_date, 1, 16)', $sessionTime)
            ->where('loans.return_date IS NOT NULL')
            ->where('fines.deleted_at', null)
            ->findAll();

        if (empty($transactionLoans)) {
            throw new PageNotFoundException('Data denda tidak ditemukan.');
        }

        $totalFineAmount = 0;
        $totalAmountPaid = 0;
        $finePerDay = \App\Models\FinesPerDayModel::getAmount();

        foreach ($transactionLoans as &$item) {
            $fAmount = floatval($item['fine_amount'] ?? 0);
            $fPaid = floatval($item['amount_paid'] ?? 0);
            $totalFineAmount += $fAmount;
            $totalAmountPaid += $fPaid;

            // Calculate violation details for each book
            $cond = strtolower($item['return_condition'] ?? 'baik');
            $bookPrice = floatval($item['item_price'] ?? 50000);
            if ($bookPrice <= 0) {
                $bookPrice = 50000;
            }

            $itemLoanDueDate = Time::parse($item['due_date'], locale: 'id');
            $itemLoanReturnDate = Time::parse($item['return_date'], locale: 'id');
            $daysLate = 0;
            if ($itemLoanReturnDate->isAfter($itemLoanDueDate)) {
                $daysLate = abs($itemLoanReturnDate->difference($itemLoanDueDate)->getDays());
            }

            $breakdown = [];
            if ($daysLate > 0) {
                $lateAmt = $daysLate * $finePerDay;
                $breakdown[] = [
                    'label'  => "Terlambat {$daysLate} Hari",
                    'amount' => $lateAmt,
                    'desc'   => "Rp " . number_format($finePerDay, 0, ',', '.') . " / hari × {$daysLate} hari"
                ];
            }
            if ($cond === 'rusak') {
                $damageAmt = 0.5 * $bookPrice;
                $breakdown[] = [
                    'label'  => "Buku Rusak (Denda 50%)",
                    'amount' => $damageAmt,
                    'desc'   => "50% dari harga buku Rp " . number_format($bookPrice, 0, ',', '.')
                ];
            } elseif ($cond === 'hilang') {
                $lostAmt = 1.0 * $bookPrice;
                $breakdown[] = [
                    'label'  => "Buku Hilang (Denda 100%)",
                    'amount' => $lostAmt,
                    'desc'   => "100% dari harga buku Rp " . number_format($bookPrice, 0, ',', '.')
                ];
            }

            $item['violation_details'] = $breakdown;
        }
        unset($item);

        return view('fines/pay', [
            'validation'       => $validation ?? \Config\Services::validation(),
            'oldInput'         => $oldInput,
            'member'           => $targetLoan,
            'transactionLoans' => $transactionLoans,
            'totalFineAmount'  => $totalFineAmount,
            'totalAmountPaid'  => $totalAmountPaid,
            'primaryUid'       => $uid
        ]);
    }

    public function update($uid = null)
    {
        if (!$this->validate([
            'nominal'  => 'required|numeric|greater_than_equal_to[1000]'
        ])) {
            return $this->pay($uid, \Config\Services::validation(), $this->request->getVar());
        }

        $targetLoan = $this->loanModel->where('uid', $uid)->first();
        if (empty($targetLoan)) {
            throw new PageNotFoundException('Return not found');
        }

        $sessionTime = substr($targetLoan['loan_date'], 0, 16);
        $transactionFines = $this->loanModel
            ->select('fines.*, fines.id as fine_id, fines.deleted_at as fine_deleted, loans.*')
            ->join('fines', 'fines.loan_id = loans.id', 'INNER')
            ->where('loans.member_id', $targetLoan['member_id'])
            ->where('SUBSTRING(loans.loan_date, 1, 16)', $sessionTime)
            ->where('loans.return_date IS NOT NULL')
            ->where('fines.deleted_at', null)
            ->findAll();

        if (empty($transactionFines)) {
            throw new PageNotFoundException('Return not found');
        }

        $nominalToPay = floatval($this->request->getVar('nominal'));
        $remainingPayment = $nominalToPay;

        foreach ($transactionFines as $fItem) {
            $fAmount = floatval($fItem['fine_amount'] ?? 0);
            $fPaid = floatval($fItem['amount_paid'] ?? 0);
            $fDue = max(0, $fAmount - $fPaid);

            if ($fDue > 0 && $remainingPayment > 0) {
                $payForThis = min($fDue, $remainingPayment);
                $newAmountPaid = $fPaid + $payForThis;
                $remainingPayment -= $payForThis;

                $this->fineModel->update($fItem['fine_id'], [
                    'amount_paid' => $newAmountPaid,
                    'paid_at'     => ($newAmountPaid >= $fAmount) ? Time::now()->toDateTimeString() : null
                ]);

                if ($newAmountPaid >= $fAmount && !empty($fItem['qr_code'])) {
                    deleteLoansQRCode($fItem['qr_code']);
                    $this->loanModel->update($fItem['id'], ['qr_code' => null]);
                }
            }
        }

        session()->setFlashdata(['msg' => 'Pembayaran denda berhasil disimpan!', 'error' => false]);
        return redirect()->to('admin/fines');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    // public function delete($id = null)
    // {
    //! Not implemented
    // }
}
