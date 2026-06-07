<?php

namespace App\Livewire\Patient;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Patient;
use App\Repository\PatientRepository;

class ShowData extends Component
{
    public $patient;

    #[On('setPatient')]
    public function setPatient($patient)
    {
        $patientModel = Patient::where(Patient::NO_REKAM_MEDIS, $patient)->first();
        if ($patientModel) {
            $this->patient = PatientRepository::getPatient($patientModel);
        }
    }

    public function render()
    {
        return view('pages.patient.show-data');
    }
}
