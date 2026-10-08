<?php

namespace App\Livewire\Mutu;

use App\Repository\InmReportRepository;
use App\Repository\KlpcmReportRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class KlpcmReport extends Component
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
        $summary = KlpcmReportRepository::getSummary($this->selectedYear, $month);
        $doctorCompliance = KlpcmReportRepository::getDoctorComplianceList($this->selectedYear, $month, 20);
        $availableYears = InmReportRepository::getAvailableYears();

        return view('pages.mutu.klpcm', [
            'summary' => $summary,
            'doctorCompliance' => $doctorCompliance,
            'availableYears' => $availableYears,
            'months' => [
                '' => 'Semua Bulan (Tahunan)',
                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            ],
        ])->title('Rekam Medis & KLPCM');
    }

    /**
     * Ekspor data kelengkapan rekam medis KLPCM ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = KlpcmReportRepository::getSummary($this->selectedYear, $month);
        $doctorCompliance = KlpcmReportRepository::getDoctorComplianceList($this->selectedYear, $month, 50);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.klpcm-report-pdf', [
            'summary' => $summary,
            'doctorCompliance' => $doctorCompliance,
            'year' => $this->selectedYear,
            'month' => $month,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Rekam Medis SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-klpcm-' . $this->selectedYear . ($month ? "-bln{$month}" : '') . '-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
