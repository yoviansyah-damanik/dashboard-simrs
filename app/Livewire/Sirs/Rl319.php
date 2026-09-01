<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsVisitRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Rl319 extends Component
{
    #[Url]
    public int $tahun = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        return view('pages.sirs.rl319', [
            'data' => SirsVisitRepository::getRL319($this->tahun),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 3.19 - Cara Bayar Pasien');
    }
}
