<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsPharmacyRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Rl318 extends Component
{
    #[Url]
    public int $tahun = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        return view('pages.sirs.rl318', [
            'data' => SirsPharmacyRepository::getRL318($this->tahun),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 3.18 - Farmasi Resep');
    }
}
