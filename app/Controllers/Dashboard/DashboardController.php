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
            $this->getWeeklyOverview(),
            $this->getMonthlyFines(),
            $this->getTotalArrears(),
        );

        return view('dashboard/index', $data);
    }

    /* ============================================================
       ===============  DATA SUMMARY (STATISTIK) ===================
       ============================================================*/
    protected function getDataSummaries(): array
    {
        $books = $this->bookModel
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->findAll();

        $totalBookStocks = array_sum(array_map(fn ($b) => $b['quantity'], $books));

        return [
            'books'          => $books,
            'totalBookStock' => $totalBookStocks,
            'racks'          => $this->rackModel->findAll(),
            'categories'     => $this->categoryModel->findAll(),
            'members'        => $this->memberModel->findAll(),
            'loans'          => $this->loanModel->findAll(),
        ];
    }


    /* ============================================================
       ================  LAPORAN HARIAN (TODAY) ===================
       ============================================================*/
    protected function getReports(): array
    {
        $now = Time::now('Asia/Jayapura', 'id');

        // Hari ini 00:00:00
        $dayStart = $now->setTime(0, 0, 0)->toDateTimeString();
        // Besok 00:00:00
        $dayEnd   = $now->addDays(1)->setTime(0, 0, 0)->toDateTimeString();

        return [
            'newMembersToday'     => $this->memberModel->where("created_at BETWEEN '$dayStart' AND '$dayEnd'")->findAll(),
            'newLoansToday'       => $this->loanModel->where("created_at BETWEEN '$dayStart' AND '$dayEnd'")->findAll(),
            'newBookReturnsToday' => $this->loanModel->where("return_date BETWEEN '$dayStart' AND '$dayEnd'")->findAll(),
            'returnDueToday'      => $this->loanModel->where("due_date BETWEEN '$dayStart' AND '$dayEnd'")->findAll(),
        ];
    }


    /* ============================================================
       ================  WEEKLY OVERVIEW (7 HARI) ==================
       ============================================================*/
    protected function getWeeklyOverview(): array
    {
        $now = Time::now('Asia/Jayapura', 'id');

        $lastWeekDateStringRange = [];
        $newMembersOverview = [];
        $loansOverview = [];
        $returnsOverview = [];

        for ($i = 6; $i >= 0; $i--) {

            $day = $now->subDays($i)->setTime(0, 0, 0);
            $dayStart = $day->toDateTimeString();
            $dayEnd   = $day->addDays(1)->toDateTimeString();

            $lastWeekDateStringRange[] = $day->format('d/m');

            $newMembersOverview[] = count($this->memberModel->where("created_at BETWEEN '$dayStart' AND '$dayEnd'")->findAll());
            $loansOverview[]      = count($this->loanModel->where("created_at BETWEEN '$dayStart' AND '$dayEnd'")->findAll());
            $returnsOverview[]    = count($this->loanModel->where("return_date BETWEEN '$dayStart' AND '$dayEnd'")->findAll());
        }

        return [
            'dateNow'                 => $now,
            'lastWeekDateStringRange' => $lastWeekDateStringRange,
            'newMembersOverview'      => $newMembersOverview,
            'loansOverview'           => $loansOverview,
            'returnsOverview'         => $returnsOverview,
        ];
    }


    /* ============================================================
       =================  PENDAPATAN DENDA BULANAN ================
       ============================================================*/
    protected function getMonthlyFines(): array
    {
        $now = Time::now('Asia/Jayapura', 'id');

        // Bulan ini
        $firstDayThisMonth = $now->setDay(1)->setTime(0,0,0)->toDateTimeString();
        $endOfToday        = Time::now('Asia/Jayapura', 'id')->toDateTimeString();

        // Bulan lalu
        $firstDayLastMonth = $now->subMonths(1)->setDay(1)->setTime(0,0,0)->toDateTimeString();
        $endLastMonth      = date('Y-m-d H:i:s', strtotime($firstDayThisMonth) - 1);

        $finesDataLastMonth = $this->fineModel->where("created_at BETWEEN '$firstDayLastMonth' AND '$endLastMonth'")->findAll();
        $finesDataThisMonth = $this->fineModel->where("created_at BETWEEN '$firstDayThisMonth' AND '$endOfToday'")->findAll();

        $fineIncomeLastMonth = [
            'value' => array_sum(array_map(fn ($f) => $f['amount_paid'] ?? 0, $finesDataLastMonth)),
            'month' => Time::parse($firstDayLastMonth, 'Asia/Jayapura', 'id')->toLocalizedString('MMMM Y')
        ];

        $fineIncomeThisMonth = [
            'value' => array_sum(array_map(fn ($f) => $f['amount_paid'] ?? 0, $finesDataThisMonth)),
            'month' => Time::parse($firstDayThisMonth, 'Asia/Jayapura', 'id')->toLocalizedString('MMMM Y')
        ];

        return [
            'fineIncomeLastMonth' => $fineIncomeLastMonth,
            'fineIncomeThisMonth' => $fineIncomeThisMonth,
        ];
    }


    /* ============================================================
       =================  TOTAL TUNGGAKAN ==========================
       ============================================================*/
    protected function getTotalArrears(): array
    {
        $fines = $this->fineModel->findAll();

        $totalFines = array_sum(array_map(fn ($f) => $f['fine_amount'], $fines));

        $totalFinesPaid = array_sum(array_map(function ($fine) {
            return min($fine['amount_paid'] ?? 0, $fine['fine_amount']);
        }, $fines));

        // Grafik tunggakan
        $finesTimeline = $this->fineModel->orderBy('created_at')->findAll();

        $carry = 0;
        $arrears = [];

        foreach ($finesTimeline as $fine) {
            $carry += max(0, $fine['fine_amount'] - ($fine['amount_paid'] ?? 0));

            $arrears[] = [
                'arrear' => $carry,
                'date'   => Time::parse($fine['created_at'], 'Asia/Jayapura', 'id')
                                ->toLocalizedString('d MMMM Y')
            ];
        }

        $oldestFineDate = Time::parse(
            $this->fineModel->selectMin('created_at')->first()['created_at'] ?? 'now',
            'Asia/Jayapura',
            'id'
        );

        return [
            'arrears'       => $arrears,
            'totalArrears'  => $totalFines - $totalFinesPaid,
            'oldestFineDate'=> $oldestFineDate,
        ];
    }
}

