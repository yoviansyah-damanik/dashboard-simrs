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
    public string $activeTab = 'all'; // 'all' | 'lab' | 'rad'

    public function mount(): void
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
        if (!in_array($this->activeTab, ['all', 'lab', 'rad'])) {
            $this->activeTab = 'all';
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['all', 'lab', 'rad'])) {
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
}
