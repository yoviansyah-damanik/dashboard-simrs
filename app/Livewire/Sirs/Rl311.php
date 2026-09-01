<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsOperativeRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Rl311 extends Component
{
    #[Url]
    public int $tahun = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        return view('pages.sirs.rl311', [
            'data' => SirsOperativeRepository::getRL311($this->tahun),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 3.11 - Gigi dan Mulut');
    }
}
