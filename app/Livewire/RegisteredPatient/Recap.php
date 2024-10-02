<?php

namespace App\Livewire\RegisteredPatient;

use Carbon\Carbon;
use App\Models\Patient;
use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\RegisteredPatient;
use App\Repository\RegisteredPatientRepository;

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
        $this->today = [
            'start' => Carbon::now()->startOfDay(),
            'end' => Carbon::now()->endOfDay(),
        ];

        $this->thisMonth = [
            'start' => Carbon::now()->startOfMonth(),
            'end' => Carbon::now()->endOfMonth(),
        ];

        $this->thisYear = [
            'start' => Carbon::now()->startOfYear(),
            'end' => Carbon::now()->endOfYear(),
        ];

        $this->todaysRecap = RegisteredPatientRepository::getRecap(
            startDate: $this->today['start'],
            endDate: $this->today['end']
        );

        $this->recapOfTheMonth = RegisteredPatientRepository::getRecap(
            startDate: $this->thisMonth['start'],
            endDate: $this->thisMonth['end']
        );

        $this->recapOfTheYear = RegisteredPatientRepository::getRecap(
            startDate: $this->thisYear['start'],
            endDate: $this->thisYear['end']
        );

        $this->overallRecap = RegisteredPatientRepository::getRecap();

        $this->filterGroup = ['hari_ini', 'bulan_ini', 'tahun_ini', 'keseluruhan'];
        $this->filter = $this->filterGroup[0];

        $this->modeGroup = ['dalam_angka', 'grafik'];
        $this->mode = $this->modeGroup[0];
    }

    public function render()
    {
        if (!in_array($this->filter, $this->filterGroup)) {
            $this->filter = $this->filterGroup[0];
        }

        if (!in_array($this->mode, $this->modeGroup)) {
            $this->mode = $this->modeGroup[0];
        }

        $startDate = null;
        $endDate = null;

        if ($this->filter == 'hari_ini') {
            $startDate = $this->today['start'];
            $endDate = $this->today['end'];
        }

        if ($this->filter == 'bulan_ini') {
            $startDate = $this->thisMonth['start'];
            $endDate = $this->thisMonth['end'];
        }

        if ($this->filter == 'tahun_ini') {
            $startDate = $this->thisYear['start'];
            $endDate = $this->thisYear['end'];
        }

        $statusGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'statusGroup'
        );
        $this->statusGroup = collect(RegisteredPatient::KELOMPOK_STATUS_PELAYANAN)
            ->map(function ($status) use ($statusGroupData) {
                return [
                    'title' => $status,
                    'value' => $statusGroupData[$status],
                ];
            })->toArray();

        $typeGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'typeGroup'
        );
        $this->typeGroup = collect(Patient::KELOMPOK_PASIEN)
            ->map(function ($type, $key) use ($typeGroupData) {
                return [
                    'title' => $type,
                    'value' => $typeGroupData[$key],
                ];
            })->toArray();

        $ageGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'ageGroup'
        );
        $this->ageGroup = collect(Patient::KELOMPOK_UMUR)
            ->map(function ($age, $key) use ($ageGroupData) {
                return [
                    'title' => $age,
                    'value' => $ageGroupData[$key],
                ];
            })->toArray();

        $advanceStatusGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'advanceStatusGroup'
        );
        $this->advanceStatusGroup = collect(RegisteredPatient::KELOMPOK_STATUS_LANJUT)
            ->map(function ($status) use ($advanceStatusGroupData) {
                return [
                    'title' => $status,
                    'value' => $advanceStatusGroupData[$status],
                ];
            })->toArray();

        $payTypesData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'payTypes'
        );

        $this->payTypes = collect($payTypesData)
            ->map(function ($type, $key) {
                return [
                    'title' => $key,
                    'value' => $type,
                ];
            })->toArray();

        $polyclinicGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'polyclinicGroup'
        );
        $this->polyclinics =
            collect($polyclinicGroupData)
            ->map(function ($polyclinic, $key) {
                return [
                    'title' => $key,
                    'value' => $polyclinic,
                ];
            })->toArray();

        $genderGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'genderGroup'
        );

        $this->genders = collect(Patient::KELOMPOK_JENIS_KELAMIN)
            ->map(function ($gender, $key) use ($genderGroupData) {
                return [
                    'title' => $gender,
                    'value' => $genderGroupData[$key],
                ];
            })->toArray();

        $doctorGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'doctorGroup'
        );
        $this->doctors =
            collect($doctorGroupData)
            ->map(function ($doctor, $key) {
                return [
                    'title' => $key,
                    'value' => $doctor,
                ];
            })->toArray();

        $tniGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'tniGroup'
        );
        $this->tniGroups =
            collect($tniGroupData)
            ->map(function ($group, $key) {
                return [
                    'title' => $key,
                    'value' => $group,
                ];
            })->toArray();

        $polriGroupData = RegisteredPatientRepository::getRecap(
            startDate: $startDate,
            endDate: $endDate,
            type: 'polriGroup'
        );
        $this->polriGroups =
            collect($polriGroupData)
            ->map(function ($group, $key) {
                return [
                    'title' => $key,
                    'value' => $group,
                ];
            })->toArray();

        return view('pages.registered-patient.recap');
    }

    public function setStatus($status)
    {
        switch ($status) {
            case 'hari_ini':
                $this->filter = 'hari_ini';
                break;
            case 'bulan_ini':
                $this->filter = 'bulan_ini';
                break;
            case 'tahun_ini':
                $this->filter = 'tahun_ini';
                break;
            default:
                $this->filter = 'keseluruhan';
                break;
        }
    }
}
