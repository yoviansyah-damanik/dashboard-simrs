<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsPharmacyRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Rl317 extends Component
{
    #[Url]
    public int $tahun = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
    }

    public function render()
    {
        return view('pages.sirs.rl317', [
            'data' => SirsPharmacyRepository::getRL317($this->tahun),
            'profil' => SirsHelper::getProfilRS(),
        ])->title('RL 3.17 - Farmasi Pengadaan Obat');
    }
}
