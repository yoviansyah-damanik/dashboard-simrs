<?php

namespace App\Livewire\OnlineQueue;

use App\Repository\OnlineQueueComplianceRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Kepatuhan & Konsistensi Antrean Online')]
class Compliance extends Component
{
    #[Url]
    public int $selectedYear = 0;

    #[Url]
    public string $selectedMonth = '';

    #[Url]
    public string $selectedPolyclinic = '';

    #[Url]
    public float $threshold = 60.0;

    #[Url]
    public string $activeTab = 'ringkasan'; // 'ringkasan', 'tahunan', 'poliklinik', 'kesesuaian'

    #[Url]
    public string $conformityFilter = ''; // '', 'sesuai', 'tidak'

    public function mount(): void
    {
        $this->selectedYear = $this->selectedYear ?: (int) date('Y');
        $this->selectedMonth = $this->selectedMonth !== '' ? $this->selectedMonth : (string) (int) date('m');
    }

    public function updatedThreshold(): void
    {
        $this->threshold = max(1.0, min(100.0, (float) $this->threshold));
    }

    public function updatedSelectedMonth(): void
    {
        if ($this->selectedMonth !== '' && $this->activeTab === 'tahunan') {
            $this->activeTab = 'ringkasan';
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /**
     * Bersihkan cache dan segarkan data dari API Terapi
     */
    public function refreshData(): void
    {
        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : (int) date('m');
        $monthStr = sprintf('%04d-%02d', $this->selectedYear, $month);

        app(\App\Services\Bpjs\TerapiAntreanApiClient::class)->clearMonthCache($monthStr);
        $this->dispatch('notify', message: 'Data antrean dari API Terapi berhasil disegarkan.');
    }

    public function render()
    {
        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $kdPoli = $this->selectedPolyclinic !== '' ? $this->selectedPolyclinic : null;

        $summary = OnlineQueueComplianceRepository::getSummary(
            year: $this->selectedYear,
            month: $month,
            kdPoli: $kdPoli,
            threshold: $this->threshold
        );

        $availableYears = OnlineQueueComplianceRepository::getAvailableYears();
        $polyclinics = OnlineQueueComplianceRepository::getPolyclinics();

        $monthNames = [
            '' => 'Semua Bulan (Laporan Tahunan)',
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
            '4' => 'April', '5' => 'Mei', '6' => 'Juni',
            '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        return view('pages.online-queue.compliance', [
            'summary' => $summary,
            'availableYears' => $availableYears,
            'polyclinics' => $polyclinics,
            'monthNames' => $monthNames,
        ])->title('Kepatuhan & Konsistensi Antrean Online');
    }

    /**
     * Ekspor Laporan Formal ke format PDF Server-Side (DomPDF)
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $month = $this->selectedMonth !== '' ? (int) $this->selectedMonth : null;
        $kdPoli = $this->selectedPolyclinic !== '' ? $this->selectedPolyclinic : null;

        $summary = OnlineQueueComplianceRepository::getSummary(
            year: $this->selectedYear,
            month: $month,
            kdPoli: $kdPoli,
            threshold: $this->threshold
        );

        $monthlyReport = OnlineQueueComplianceRepository::getMonthlyReport(
            year: $this->selectedYear,
            kdPoli: $kdPoli,
            threshold: $this->threshold
        );

        $polyclinicRows = OnlineQueueComplianceRepository::getPolyclinicDoctorBreakdown(
            year: $this->selectedYear,
            month: $month,
            kdPoli: $kdPoli,
            threshold: $this->threshold
        );

        $annualPolyDoc = !$month
            ? OnlineQueueComplianceRepository::getAnnualPolyclinicDoctorReport(
                year: $this->selectedYear,
                kdPoli: $kdPoli,
                threshold: $this->threshold
            )
            : [];

        $polyclinics = OnlineQueueComplianceRepository::getPolyclinics();
        $namaPoli = $kdPoli ? ($polyclinics[$kdPoli] ?? $kdPoli) : 'Semua Poliklinik';

        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $periodeLabel = $month
            ? ($monthNames[$month] ?? $month) . ' ' . $this->selectedYear
            : 'Tahun ' . $this->selectedYear;

        $pdf = Pdf::loadView('reports.online-queue-compliance-pdf', [
            'summary' => $summary,
            'monthlyReport' => $monthlyReport,
            'polyclinicRows' => $polyclinicRows,
            'annualPolyDoc' => $annualPolyDoc,
            'year' => $this->selectedYear,
            'month' => $month,
            'periodeLabel' => $periodeLabel,
            'namaPoli' => $namaPoli,
            'threshold' => $this->threshold,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Evaluasi SIMRS',
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan-kepatuhan-antrean-' . $this->selectedYear . ($month ? "-bln{$month}" : '') . '-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
