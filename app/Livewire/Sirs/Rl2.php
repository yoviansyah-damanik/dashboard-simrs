<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsStaffRepository;
use Livewire\Component;

class Rl2 extends Component
{
    public function render()
    {
        return view('pages.sirs.rl2', [
            'data' => SirsStaffRepository::getRL2(),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 2 - Ketenagaan');
    }
}
