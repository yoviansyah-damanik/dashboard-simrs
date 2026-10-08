<?php

namespace App\Livewire\Pharmacy;

use App\Helpers\SirsHelper;
use App\Repository\PharmacyReportRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class YearlyMatrix extends Component
{
    #[Url]
    public int $tahun = 0;

    #[Url]
    public string $activeTab = 'summary'; // 'summary' | 'care_setting' | 'prescription_type' | 'quality'

    public function mount(): void
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
        if (!in_array($this->activeTab, ['summary', 'care_setting', 'prescription_type', 'quality'])) {
            $this->activeTab = 'summary';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['summary', 'care_setting', 'prescription_type', 'quality'])) {
            $this->activeTab = $tab;
        }
    }

    public function render()
    {
        $matrix = PharmacyReportRepository::getYearlyMatrix($this->tahun);
        $profil = SirsHelper::getProfilRS();

        return view('pages.pharmacy.yearly-matrix', [
            'matrix' => $matrix,
            'profil' => $profil,
            'tahun' => $this->tahun,
            'activeTab' => $this->activeTab,
            'summary' => $matrix['summary'],
            'months' => $matrix['months'],
            'totals' => $matrix['totals'],
            'averages' => $matrix['averages'],
            'charts' => $matrix['charts'],
        ])->title('Matriks Indikator Tahunan Farmasi');
    }

    /**
     * Ekspor matriks indikator tahunan farmasi ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $matrix = PharmacyReportRepository::getYearlyMatrix($this->tahun);
        $profil = SirsHelper::getProfilRS();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pharmacy-yearly-matrix-pdf', [
            'matrix' => $matrix,
            'profil' => $profil,
            'tahun' => $this->tahun,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Farmasi SIMRS',
        ])->setPaper('a4', 'landscape');

        $filename = 'matriks-indikator-farmasi-' . $this->tahun . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
