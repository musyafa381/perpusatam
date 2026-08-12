<?php

namespace App\Controllers\Dashboard;

use App\Models\BookModel;
use App\Models\CategoryModel;
use App\Models\FineModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use App\Models\RackModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;

class DashboardController extends ResourceController
{
    protected BookModel $bookModel;
    protected RackModel $rackModel;
    protected CategoryModel $categoryModel;
    protected MemberModel $memberModel;
    protected LoanModel $loanModel;
    protected FineModel $fineModel;

    public function __construct()
    {
        $this->bookModel = new BookModel;
        $this->rackModel = new RackModel;
        $this->categoryModel = new CategoryModel;
        $this->memberModel = new MemberModel;
        $this->loanModel = new LoanModel;
        $this->fineModel = new FineModel;
    }

    public function index()
    {
        return redirect('admin/dashboard');
    }

    public function dashboard()
    {
        $data = array_merge(
            $this->getDataSummaries(),
            $this->getReports(),
            $this->getMonthlyOverview(),
            $this->getMonthlyFines(),
            $this->getTotalArrears(),
        );

        return view('dashboard/index', $data);
    }

    protected function getDataSummaries(): array
    {
        $books = $this->bookModel
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->findAll();

        $totalBookStocks = array_reduce(
            array_map(function ($book) {
                return (int)($book['quantity'] ?? 0);
            }, $books),
            function ($carry, $item) {
                return ($carry + $item);
            }
        );

        // Grouped active loans (Peminjaman Aktif yang belum dikembalikan)
        $activeLoansQuery = $this->loanModel
            ->select('loans.id')
            ->where('deleted_at', null)
            ->where('return_date', null)
            ->groupBy('member_id, SUBSTRING(loan_date, 1, 16)')
            ->findAll();
        $activeLoansCount = count($activeLoansQuery);

        // Grouped returns (Pengembalian yang sudah selesai)
        $returnsQuery = $this->loanModel
            ->select('loans.id')
            ->where('deleted_at', null)
            ->where('return_date !=', null)
            ->groupBy('member_id, SUBSTRING(return_date, 1, 16)')
            ->findAll();
        $returnsCount = count($returnsQuery);

        // Grouped total transactions (Total seluruh sesi transaksi peminjaman)
        $totalTransactionsQuery = $this->loanModel
            ->select('loans.id')
            ->where('deleted_at', null)
            ->groupBy('member_id, SUBSTRING(loan_date, 1, 16)')
            ->findAll();
        $totalTransactionsCount = count($totalTransactionsQuery);

        return [
            'books'                  => $books,
            'totalBookStock'         => $totalBookStocks,
            'racks'                  => $this->rackModel->findAll(),
            'categories'             => $this->categoryModel->findAll(),
            'members'                => $this->memberModel->findAll(),
            'activeLoansCount'       => $activeLoansCount,
            'returnsCount'           => $returnsCount,
            'totalTransactionsCount' => $totalTransactionsCount,
        ];
    }

    protected function getReports(): array
    {
        $now = Time::now(locale: 'id');

        $todayMidnight = $now->today()->toDateTimeString();
        $tomorrowMidnight = $now->tomorrow()->toDateTimeString();

        $newMembersToday = $this->memberModel
            ->where("created_at BETWEEN '{$todayMidnight}' AND '{$tomorrowMidnight}'")
            ->findAll();

        $newLoansToday = $this->loanModel
            ->where("created_at BETWEEN '{$todayMidnight}' AND '{$tomorrowMidnight}'")
            ->findAll();

        $newBookReturnsToday = $this->loanModel
            ->where("return_date BETWEEN '{$todayMidnight}' AND '{$tomorrowMidnight}'")
            ->findAll();

        $returnDueToday = $this->loanModel
            ->where("due_date BETWEEN '{$todayMidnight}' AND '{$tomorrowMidnight}'")
            ->findAll();

        return [
            'newMembersToday'       => $newMembersToday,
            'newLoansToday'         => $newLoansToday,
            'newBookReturnsToday'   => $newBookReturnsToday,
            'returnDueToday'        => $returnDueToday,
        ];
    }

    protected function getMonthlyOverview(): array
    {
        $now = Time::now(locale: 'id');
        $weeklyDateRange = [];

        $newMembersOverview = [];
        $loansOverview = [];
        $returnsOverview = [];

        // 4 Weeks Overview (7 days per week for the last 28 days)
        for ($w = 3; $w >= 0; $w--) {
            $startOfWeek = $now->today()->subDays(($w * 7) + 6);
            $endOfWeek = $now->today()->subDays($w * 7);

            $startStr = $startOfWeek->toDateTimeString();
            $endStr = $endOfWeek->tomorrow()->subSeconds(1)->toDateTimeString();

            $startLabel = $startOfWeek->toLocalizedString('dd/MM');
            $endLabel = $endOfWeek->toLocalizedString('dd/MM');
            $weekNum = 4 - $w;

            array_push($weeklyDateRange, "Minggu {$weekNum} ({$startLabel}-{$endLabel})");

            array_push(
                $newMembersOverview,
                count(
                    $this->memberModel
                        ->where("created_at BETWEEN '{$startStr}' AND '{$endStr}'")
                        ->findAll()
                )
            );
            array_push(
                $loansOverview,
                count(
                    $this->loanModel
                        ->where("created_at BETWEEN '{$startStr}' AND '{$endStr}'")
                        ->findAll()
                )
            );
            array_push(
                $returnsOverview,
                count(
                    $this->loanModel
                        ->where("return_date BETWEEN '{$startStr}' AND '{$endStr}'")
                        ->findAll()
                )
            );
        }

        return [
            'dateNow'                   => $now,
            'lastMonthDateStringRange'  => $weeklyDateRange,
            'newMembersOverview'        => $newMembersOverview,
            'loansOverview'             => $loansOverview,
            'returnsOverview'           => $returnsOverview,
        ];
    }

    protected function getMonthlyFines(): array
    {
        $now = Time::now(locale: 'id');

        $firstDayLastMonth = $now->today()->subMonths(1)->setDay(1)->toDateTimeString();
        $lastDayLastMonth = $now->today()->setDay(1)->subSeconds(1)->toDateTimeString();
        $firstDayThisMonth = $now->today()->setDay(1)->toDateTimeString();

        $finesDataLastMonth = $this->fineModel
            ->where('deleted_at', null)
            ->where('amount_paid >', 0)
            ->where("created_at BETWEEN '{$firstDayLastMonth}' AND '{$lastDayLastMonth}'")
            ->findAll();
        $finesDataThisMonth = $this->fineModel
            ->where('deleted_at', null)
            ->where('amount_paid >', 0)
            ->where("created_at BETWEEN '{$firstDayThisMonth}' AND '{$now->toDateTimeString()}'")
            ->findAll();

        $fineIncomeLastMonth['value'] = array_reduce(
            array_map(function ($fine) {
                return intval($fine['amount_paid'] ?? 0);
            }, $finesDataLastMonth),
            function ($carry, $item) {
                return ($carry + $item);
            },
            0
        );
        $fineIncomeLastMonth['month'] = $now->subMonths(1)->toLocalizedString('MMMM Y');

        $fineIncomeThisMonth['value'] = array_reduce(
            array_map(function ($fine) {
                return intval($fine['amount_paid'] ?? 0);
            }, $finesDataThisMonth),
            function ($carry, $item) {
                return ($carry + $item);
            },
            0
        );
        $fineIncomeThisMonth['month'] = $now->toLocalizedString('MMMM Y');

        return [
            'fineIncomeLastMonth' => $fineIncomeLastMonth,
            'fineIncomeThisMonth' => $fineIncomeThisMonth
        ];
    }

    protected function getTotalArrears(): array
    {
        $fines = $this->fineModel->findAll();

        $totalFines = array_reduce(
            array_map(function ($fine) {
                return $fine['fine_amount'];
            }, $fines),
            function ($carry, $item) {
                return ($carry + $item);
            }
        );

        $totalFinesPaid = array_reduce(
            array_map(function ($fine) {
                if (($fine['amount_paid'] ?? 0) > $fine['fine_amount']) {
                    return $fine['fine_amount'];
                }
                return $fine['amount_paid'];
            }, $fines),
            function ($carry, $item) {
                return ($carry + $item);
            }
        );

        $fines = $this->fineModel->limit(100)->orderBy('created_at')->findAll();

        $carry = 0;
        $arrears = [];

        foreach ($fines as $fine) {
            $arrear = $carry;

            if (($fine['amount_paid'] ?? 0) <= $fine['fine_amount']) {
                $arrear = $carry + ($fine['fine_amount'] - $fine['amount_paid']);
            }

            array_push($arrears, [
                'arrear' => $arrear,
                'date' => Time::parse($fine['created_at'], locale: 'id')->toLocalizedString('d MMMM Y')
            ]);
            $carry = $arrear;
        }

        $totalArrears = $totalFines - $totalFinesPaid;

        $oldestFineDate = Time::parse($this->fineModel->selectMin('created_at')->first()['created_at'] ?? 'now', locale: 'id');

        return [
            'arrears' => $arrears,
            'totalArrears' => $totalArrears,
            'oldestFineDate' => $oldestFineDate,
        ];
    }
}
