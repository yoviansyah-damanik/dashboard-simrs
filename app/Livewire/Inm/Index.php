<?php

namespace App\Livewire\Inm;

use App\Repository\InmReportRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    #[Url]
    public int $selectedYear = 0;

    #[Url]
    public string $selectedMonth = ''; // '' for whole year, or '1'..'12'

    #[Url]
    public string $selectedCategory = 'all'; // 'all' | 'pelayanan_klinis' | 'ppi_keselamatan' | 'tata_kelola_penunjang'

    public ?string $activeIndicatorModal = null;
    public array $indicatorAuditDetails = [];
    public ?array $activeIndicatorInfo = null;

    public string $chartIndicator = 'waktu_tunggu_rajal';
    public array $trendData = [];

    public function mount(): void
    {
        $availableYears = InmReportRepository::getAvailableYears();
        if ($this->selectedYear <= 0 || !in_array($this->selectedYear, $availableYears)) {
            $this->selectedYear = $availableYears[0] ?? (int) date('Y');
        }

        $this->loadTrendData();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadTrendData();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadTrendData();
    }

    public function setCategory(string $category): void
    {
        if (in_array($category, ['all', 'pelayanan_klinis', 'ppi_keselamatan', 'tata_kelola_penunjang'])) {
            $this->selectedCategory = $category;
        }
    }

    public function setChartIndicator(string $indicatorId): void
    {
        $definitions = InmReportRepository::getIndicatorDefinitions();
        if (isset($definitions[$indicatorId])) {
            $this->chartIndicator = $indicatorId;
            $this->loadTrendData();
        }
    }

    public function loadTrendData(): void
    {
        $this->trendData = InmReportRepository::getMonthlyTrends($this->selectedYear, $this->chartIndicator);
        $this->dispatch('refresh-inm-chart', [
            'labels' => $this->trendData['labels'],
            'data' => $this->trendData['data'],
            'targets' => $this->trendData['targets'],
            'indicatorTitle' => InmReportRepository::getIndicatorDefinitions()[$this->chartIndicator]['title'] ?? 'Indikator Mutu',
            'unit' => InmReportRepository::getIndicatorDefinitions()[$this->chartIndicator]['unit'] ?? '%',
        ]);
    }

    public function openAuditModal(string $indicatorId): void
    {
        $definitions = InmReportRepository::getIndicatorDefinitions();
        if (isset($definitions[$indicatorId])) {
            $this->activeIndicatorModal = $indicatorId;
            $this->activeIndicatorInfo = $definitions[$indicatorId];
            $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
            $this->indicatorAuditDetails = InmReportRepository::getIndicatorAuditDetails(
                $indicatorId,
                $this->selectedYear,
                $month,
                50
            );
        }
    }

    public function closeAuditModal(): void
    {
        $this->activeIndicatorModal = null;
        $this->activeIndicatorInfo = null;
        $this->indicatorAuditDetails = [];
    }

    public function render()
    {
        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = InmReportRepository::getSummary($this->selectedYear, $month);
        $availableYears = InmReportRepository::getAvailableYears();
        $definitions = InmReportRepository::getIndicatorDefinitions();

        // Filter indicators by selected category
        $filteredIndicators = collect($summary['indicators'])->filter(function ($item) {
            if ($this->selectedCategory === 'all') {
                return true;
            }
            return ($item['category'] ?? '') === $this->selectedCategory;
        })->toArray();

        return view('pages.inm.index', [
            'summary' => $summary,
            'filteredIndicators' => $filteredIndicators,
            'availableYears' => $availableYears,
            'definitions' => $definitions,
            'months' => [
                '' => 'Semua Bulan (Kumulatif Tahunan)',
                '1' => 'Januari',
                '2' => 'Februari',
                '3' => 'Maret',
                '4' => 'April',
                '5' => 'Mei',
                '6' => 'Juni',
                '7' => 'Juli',
                '8' => 'Agustus',
                '9' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember',
            ],
        ])->title('Indikator Nasional Mutu (INM)');
    }

    /**
     * Ekspor laporan data Indikator Nasional Mutu (INM) ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = InmReportRepository::getSummary($this->selectedYear, $month);
        $definitions = InmReportRepository::getIndicatorDefinitions();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.inm-report-pdf', [
            'summary' => $summary,
            'definitions' => $definitions,
            'year' => $this->selectedYear,
            'month' => $month,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Mutu SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-inm-' . $this->selectedYear . ($month ? "-bln{$month}" : '') . '-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
