<?php

namespace App\Livewire\Mutu;

use App\Repository\InmReportRepository;
use App\Repository\SpmReportRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Spm extends Component
{
    #[Url]
    public int $selectedYear = 0;

    #[Url]
    public string $selectedMonth = '';

    public function mount(): void
    {
        $years = InmReportRepository::getAvailableYears();
        $this->selectedYear = $years[0] ?? (int) date('Y');
    }

    public function render()
    {
        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = SpmReportRepository::getSummary($this->selectedYear, $month);
        $availableYears = InmReportRepository::getAvailableYears();

        return view('pages.mutu.spm', [
            'summary' => $summary,
            'availableYears' => $availableYears,
            'months' => [
                '' => 'Semua Bulan (Tahunan)',
                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            ],
        ])->title('Standar Pelayanan Minimal (SPM)');
    }

    /**
     * Ekspor data Standar Pelayanan Minimal (SPM) ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = SpmReportRepository::getSummary($this->selectedYear, $month);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.spm-report-pdf', [
            'summary' => $summary,
            'year' => $this->selectedYear,
            'month' => $month,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Mutu SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-spm-' . $this->selectedYear . ($month ? "-bln{$month}" : '') . '-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
