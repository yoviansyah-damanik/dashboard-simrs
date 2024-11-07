<?php

namespace App\Livewire\Patient;

use Carbon\Carbon;
use Livewire\Component;
use App\Repository\PatientRepository;

class Recap extends Component
{
    public array $statusGroup;
    public array $advanceStatusGroup;
    public array $typeGroup;
    public array $ageGroup;
    public array $payTypes;
    public array $polyclinics;
    public array $genders;
    public array $filterGroup;
    public array $modeGroup;
    public array $doctors;
    public array $tniGroups;
    public array $polriGroups;

    public int $todaysRecap;
    public int $recapOfTheMonth;
    public int $recapOfTheYear;
    public int $overallRecap;

    #[Url]
    public string $filter;

    #[Url]
    public string $mode;

    public array $today;
    public array $thisMonth;
    public array $thisYear;

    public function mount()
    {
        $this->overallRecap = PatientRepository::getRecap();

        $this->modeGroup = ['dalam_angka', 'grafik'];
        $this->mode = $this->modeGroup[0];
    }

    public function render()
    {
        return view('pages.patient.recap');
    }
}
