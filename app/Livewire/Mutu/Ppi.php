<?php

namespace App\Livewire\Mutu;

use App\Repository\InmReportRepository;
use App\Repository\PpiReportRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Ppi extends Component
{
    #[Url]
    public int $selectedYear = 0;

    #[Url]
    public string $selectedMonth = '';

    public ?array $activePatientModal = null;
    public array $trendChartData = [];

    public function mount(): void
    {
        $years = InmReportRepository::getAvailableYears();
        $this->selectedYear = $years[0] ?? (int) date('Y');
        $this->loadTrends();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadTrends();
    }

    public function loadTrends(): void
    {
        $this->trendChartData = PpiReportRepository::getMonthlyHaisTrends($this->selectedYear);
        $this->dispatch('refresh-hais-chart', $this->trendChartData);
    }

    public function openPatientDetail(array $patient): void
    {
        $this->activePatientModal = $patient;
    }

    public function closePatientDetail(): void
    {
        $this->activePatientModal = null;
    }

    public function render()
    {
        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = PpiReportRepository::getSummary($this->selectedYear, $month);
        $patientList = PpiReportRepository::getHaisPatientList($this->selectedYear, $month, 50);
        $availableYears = InmReportRepository::getAvailableYears();

        return view('pages.mutu.ppi', [
            'summary' => $summary,
            'patientList' => $patientList,
            'availableYears' => $availableYears,
            'months' => [
                '' => 'Semua Bulan (Tahunan)',
                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            ],
        ])->title('Surveilans PPI & HAIs');
    }

    /**
     * Ekspor data surveilans PPI ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = PpiReportRepository::getSummary($this->selectedYear, $month);
        $patientList = PpiReportRepository::getHaisPatientList($this->selectedYear, $month, 100);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.ppi-report-pdf', [
            'summary' => $summary,
            'patientList' => $patientList,
            'year' => $this->selectedYear,
            'month' => $month,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas PPI SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-ppi-' . $this->selectedYear . ($month ? "-bln{$month}" : '') . '-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
