<?php

namespace App\Livewire\FinancialReport;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use App\Repository\FinancialReportRepository;

class Index extends Component
{
    #[Url]
    public $startDate;

    #[Url]
    public $endDate;

    #[Url]
    public $period = 'this_month';

    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = date('n');
        $this->selectedYear = date('Y');
        $this->syncDates();
    }

    public function updatedPeriod()
    {
        $this->syncDates();
    }

    public function updatedSelectedMonth()
    {
        $this->syncDates();
    }

    public function updatedSelectedYear()
    {
        $this->syncDates();
    }

    private function syncDates()
    {
        switch ($this->period) {
            case 'today':
                $this->startDate = date('Y-m-d');
                $this->endDate = date('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = date('Y-m-d', strtotime('-7 days'));
                $this->endDate = date('Y-m-d');
                break;
            case 'last_30_days':
                $this->startDate = date('Y-m-d', strtotime('-30 days'));
                $this->endDate = date('Y-m-d');
                break;
            case 'this_week':
                $this->startDate = date('Y-m-d', strtotime('monday this week'));
                $this->endDate = date('Y-m-d', strtotime('sunday this week'));
                break;
            case 'this_month':
                $this->startDate = date('Y-m-01');
                $this->endDate = date('Y-m-t');
                break;
            case 'this_year':
                $this->startDate = date('Y-01-01');
                $this->endDate = date('Y-12-31');
                break;
            case 'monthly':
                $this->startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');
                break;
            case 'yearly':
                $this->startDate = Carbon::create($this->selectedYear, 1, 1)->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::create($this->selectedYear, 1, 1)->endOfYear()->format('Y-m-d');
                break;
        }
    }

    #[Computed]
    public function months()
    {
        return [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
    }

    #[Computed]
    public function years()
    {
        return range(date('Y') - 5, date('Y'));
    }

    #[Computed]
    public function summary()
    {
        return FinancialReportRepository::getSummary($this->startDate, $this->endDate);
    }

    public function render()
    {
        return view('pages.financial-report.index');
    }
}
