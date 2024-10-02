<?php

namespace App\Livewire\Inpatient;

use App\Helpers\FilterHelper;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Repository\InpatientsRepository;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public array $ageCategories;
    public array $genders;
    public array $statusGroup;
    public array $polyclinics;
    public array $types;
    public array $payTypes;
    public array $limits;
    public array $rooms;
    public array $wards;
    public array $doctors;
    public array $tniGroups;

    #[Url]
    public string $ageCategory;

    #[Url]
    public string $gender;

    #[Url]
    public string $startDate;

    #[Url]
    public string $endDate;

    #[Url]
    public string $status;

    #[Url]
    public string $polyclinic;

    #[Url]
    public string $type;

    #[Url]
    public string $room;

    #[Url]
    public string $ward;

    #[Url]
    public string $payType;

    #[Url]
    public string $education;

    #[Url]
    public string $limit;

    #[Url]
    public string $doctor;

    #[Url]
    public string $tniGroup;

    public function mount()
    {
        $this->ageCategories = FilterHelper::getAgeCategories();
        $this->ageCategory = $this->ageCategories[0]['value'];

        $this->genders = FilterHelper::getGenders();
        $this->gender = $this->genders[0]['value'];

        $this->startDate = Carbon::now()->addDays(-90)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');

        $this->statusGroup = FilterHelper::getStatus();
        $this->status = $this->statusGroup[0]['value'];

        $this->payTypes = FilterHelper::getPayTypes();
        $this->payType = $this->payTypes[0]['value'];

        $this->types = FilterHelper::getTypes();
        $this->type = $this->types[0]['value'];

        $this->doctors = FilterHelper::getDoctors();
        $this->doctor = $this->doctors[0]['value'];

        $this->rooms = FilterHelper::getRooms();
        $this->room = $this->rooms[0]['value'];

        $this->wards = FilterHelper::getWards();
        $this->ward = $this->wards[0]['value'];

        $this->tniGroups = FilterHelper::getTniGroups();
        $this->tniGroup = $this->tniGroups[0]['value'];

        $this->limits = [25, 50, 100, 200];
        $this->limit = 25;
    }

    public function render()
    {
        $patients = InpatientsRepository::getAll(
            startDate: $this->startDate,
            endDate: $this->endDate,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            type: $this->type,
            payType: $this->payType,
            limit: $this->limit,
            ward: $this->ward,
            room: $this->room,
            doctor: $this->doctor,
            tniGroup: $this->tniGroup
        );

        return view('pages.inpatient.index', compact('patients'));
    }

    public function updated($attribute)
    {
        $this->resetPage();
    }
}
