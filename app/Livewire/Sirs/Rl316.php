<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsOperativeRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Rl316 extends Component
{
    #[Url]
    public int $tahun = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        return view('pages.sirs.rl316', [
            'data' => SirsOperativeRepository::getRL316($this->tahun),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 3.16 - Keluarga Berencana');
    }
}
