<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsFacilityRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Rl12 extends Component
{
    #[Url]
    public int $tahun = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        return view('pages.sirs.rl12', [
            'data' => SirsFacilityRepository::getRL12($this->tahun),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 1.2 - Indikator Pelayanan Rumah Sakit');
    }
}
