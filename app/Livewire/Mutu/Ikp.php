<?php

namespace App\Livewire\Mutu;

use App\Repository\IkpReportRepository;
use App\Repository\InmReportRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Ikp extends Component
{
    #[Url]
    public int $selectedYear = 0;

    #[Url]
    public string $selectedMonth = '';

    #[Url]
    public string $selectedJenis = 'all';

    public ?array $activeIncidentModal = null;

    public function mount(): void
    {
        $years = InmReportRepository::getAvailableYears();
        $this->selectedYear = $years[0] ?? (int) date('Y');
    }

    public function filterJenis(string $jenis): void
    {
        $this->selectedJenis = $jenis;
    }

    public function openIncidentDetail(string $id, array $incident): void
    {
        $this->activeIncidentModal = $incident;
    }

    public function closeIncidentDetail(): void
    {
        $this->activeIncidentModal = null;
    }

    public function render()
    {
        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = IkpReportRepository::getSummary($this->selectedYear, $month);
        $jenisFilter = $this->selectedJenis !== 'all' ? $this->selectedJenis : null;
        $incidents = IkpReportRepository::getIncidentList($this->selectedYear, $month, $jenisFilter, 50);
        $gradingMatrix = IkpReportRepository::getRiskGradingMatrix();
        $availableYears = InmReportRepository::getAvailableYears();

        return view('pages.mutu.ikp', [
            'summary' => $summary,
            'incidents' => $incidents,
            'gradingMatrix' => $gradingMatrix,
            'availableYears' => $availableYears,
            'months' => [
                '' => 'Semua Bulan (Tahunan)',
                '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            ],
        ])->title('Insiden Keselamatan Pasien (IKP)');
    }

    /**
     * Ekspor data insiden keselamatan pasien ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $summary = IkpReportRepository::getSummary($this->selectedYear, $month);
        $jenisFilter = $this->selectedJenis !== 'all' ? $this->selectedJenis : null;
        $incidents = IkpReportRepository::getIncidentList($this->selectedYear, $month, $jenisFilter, 100);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.ikp-report-pdf', [
            'summary' => $summary,
            'incidents' => $incidents,
            'year' => $this->selectedYear,
            'month' => $month,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Keselamatan Pasien SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-ikp-' . $this->selectedYear . ($month ? "-bln{$month}" : '') . '-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
