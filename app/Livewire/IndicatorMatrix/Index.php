<?php

namespace App\Livewire\IndicatorMatrix;

use App\Helpers\SirsHelper;
use App\Repository\SirsFacilityRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    #[Url]
    public int $tahun = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        return view('pages.indicator-matrix.index', [
            'matrix' => SirsFacilityRepository::getYearlyIndicatorMatrix($this->tahun),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('Matriks Indikator Tahunan');
    }
}
