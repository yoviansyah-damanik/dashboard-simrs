<?php

namespace App\Livewire\Ancillary;

use App\Helpers\SirsHelper;
use App\Repository\AncillaryReportRepository;
use Carbon\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;

class Summary extends Component
{
    #[Url]
    public string $startDate = '';

    #[Url]
    public string $endDate = '';

    #[Url]
    public string $period = 'this_month';

    public int $selectedMonth = 0;
    public int $selectedYear = 0;

    #[Url]
    public string $activeTab = 'overview'; // 'overview' | 'farmasi' | 'lab' | 'rad' | 'gizi'

    public function mount(): void
    {
        $this->selectedMonth = (int) date('n');
        $this->selectedYear = (int) date('Y');

        if (!in_array($this->activeTab, ['overview', 'farmasi', 'lab', 'rad', 'gizi'])) {
            $this->activeTab = 'overview';
        }

        if (empty($this->startDate) || empty($this->endDate)) {
            $this->syncDates();
        }
    }

    public function updatedPeriod(): void
    {
        $this->syncDates();
    }

    public function updatedSelectedMonth(): void
    {
        $this->period = 'monthly';
        $this->syncDates();
    }

    public function updatedSelectedYear(): void
    {
        $this->syncDates();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'farmasi', 'lab', 'rad', 'gizi'])) {
            $this->activeTab = $tab;
        }
    }

    public function syncDates(): void
    {
        switch ($this->period) {
            case 'today':
                $this->startDate = date('Y-m-d');
                $this->endDate = date('Y-m-d');
                break;
            case 'yesterday':
                $this->startDate = date('Y-m-d', strtotime('-1 day'));
                $this->endDate = date('Y-m-d', strtotime('-1 day'));
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
            case 'last_month':
                $this->startDate = date('Y-m-01', strtotime('first day of last month'));
                $this->endDate = date('Y-m-t', strtotime('last day of last month'));
                break;
            case 'this_year':
                $this->startDate = date('Y-01-01');
                $this->endDate = date('Y-12-31');
                break;
            case 'monthly':
                $this->startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                break;
            default:
                $this->startDate = date('Y-m-01');
                $this->endDate = date('Y-m-t');
                break;
        }
    }

    public function render()
    {
        $report = AncillaryReportRepository::getSummary($this->startDate, $this->endDate);
        $profil = SirsHelper::getProfilRS();

        return view('pages.ancillary.summary', [
            'report' => $report,
            'summary' => $report['summary'],
            'farmasi' => $report['farmasi'],
            'laboratorium' => $report['laboratorium'],
            'radiologi' => $report['radiologi'],
            'gizi' => $report['gizi'],
            'charts' => $report['charts'],
            'profil' => $profil,
            'activeTab' => $this->activeTab,
            'period' => $this->period,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ])->title('Ringkasan Layanan Penunjang');
    }

    /**
     * Ekspor ringkasan layanan penunjang medis ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $report = AncillaryReportRepository::getSummary($this->startDate, $this->endDate);
        $profil = SirsHelper::getProfilRS();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.ancillary-summary-pdf', [
            'report' => $report,
            'summary' => $report['summary'],
            'farmasi' => $report['farmasi'],
            'laboratorium' => $report['laboratorium'],
            'radiologi' => $report['radiologi'],
            'gizi' => $report['gizi'],
            'profil' => $profil,
            'startDate' => Carbon::parse($this->startDate)->translatedFormat('d F Y'),
            'endDate' => Carbon::parse($this->endDate)->translatedFormat('d F Y'),
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Penunjang Medis SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'ringkasan-layanan-penunjang-' . $this->startDate . '-sd-' . $this->endDate . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
