<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsFacilityRepository;
use Livewire\Component;

class Rl13 extends Component
{
    public function render()
    {
        return view('pages.sirs.rl13', [
            'data' => SirsFacilityRepository::getRL13(),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 1.3 - Fasilitas Tempat Tidur');
    }
}
