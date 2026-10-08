<?php

namespace App\Livewire\Ancillary;

use App\Helpers\SirsHelper;
use App\Repository\AncillaryReportRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class YearlyMatrix extends Component
{
    #[Url]
    public int $tahun = 0;

    #[Url]
    public string $activeTab = 'all'; // 'all' | 'lab' | 'rad' | 'farmasi' | 'gizi'

    public function mount(): void
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
        if (!in_array($this->activeTab, ['all', 'lab', 'rad', 'farmasi', 'gizi'])) {
            $this->activeTab = 'all';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['all', 'lab', 'rad', 'farmasi', 'gizi'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        $matrix = AncillaryReportRepository::getYearlyMatrix($this->tahun);
        $profil = SirsHelper::getProfilRS();

        return view('pages.ancillary.yearly-matrix', [
            'matrix' => $matrix,
            'profil' => $profil,
            'tahun' => $this->tahun,
            'activeTab' => $this->activeTab,
            'summary' => $matrix['summary'],
            'months' => $matrix['months'],
            'totals' => $matrix['totals'],
            'averages' => $matrix['averages'],
            'charts' => $matrix['charts'],
        ])->title('Matriks Indikator Tahunan Layanan Penunjang');
    }

    /**
     * Ekspor matriks indikator tahunan penunjang ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $matrix = AncillaryReportRepository::getYearlyMatrix($this->tahun);
        $profil = SirsHelper::getProfilRS();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.ancillary-yearly-matrix-pdf', [
            'matrix' => $matrix,
            'profil' => $profil,
            'tahun' => $this->tahun,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Penunjang Medis SIMRS',
        ])->setPaper('a4', 'landscape');

        $filename = 'matriks-indikator-penunjang-' . $this->tahun . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
