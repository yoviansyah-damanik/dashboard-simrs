<?php

namespace App\Livewire\Sirs;

use App\Helpers\SirsHelper;
use App\Repository\SirsClinicalRepository;
use Livewire\Attributes\Url;
use Livewire\Component;

class Rl36 extends Component
{
    #[Url]
    public int $tahun = 0;

    #[Url]
    public int $bulan = 0;

    public function mount()
    {
        $this->tahun = $this->tahun ?: (int) now()->year;
        $this->bulan = $this->bulan ?: (int) now()->month;
    }

    public function render()
    {
        return view('pages.sirs.rl36', [
            'data' => SirsClinicalRepository::getRL36($this->tahun, $this->bulan),
            'profil' => SirsHelper::getProfilRS(),
            'namaBulan' => SirsHelper::getMonthName($this->bulan),
        ])->title('RL 3.6 - Pelayanan Kebidanan');
    }
}
