<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsFacilityRepository;
use Livewire\Component;

class Rl11 extends Component
{
    public function render()
    {
        return view('pages.sirs.rl11', [
            'data' => SirsFacilityRepository::getRL11(),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 1.1 - Data Dasar Rumah Sakit');
    }
}
