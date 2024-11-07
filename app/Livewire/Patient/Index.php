<?php

namespace App\Livewire\Patient;

use App\Models\Patient;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use App\Repository\PatientRepository;

class Index extends Component
{
    use WithPagination;

    public array $ageCategories;
    public array $genders;
    public array $types;
    public array $payTypes;
    public array $tniGroups;
    public array $tniUnits;
    public array $limits;

    #[Url]
    public string $search = '';

    #[Url]
    public string $ageCategory;

    #[Url]
    public string $gender;

    #[Url]
    public string $type;

    #[Url]
    public string $payType;

    #[Url]
    public string $tniGroup;

    #[Url]
    public string $tniUnit;

    #[Url]
    public string $limit;

    public function mount()
    {
        $this->ageCategories = FilterHelper::getAgeCategories();
        $this->ageCategory = $this->ageCategories[0]['value'];

        $this->genders = FilterHelper::getGenders();
        $this->gender = $this->genders[0]['value'];

        $this->types = FilterHelper::getTypes();
        $this->type = $this->types[0]['value'];

        $this->payTypes = FilterHelper::getPayTypes();
        $this->payType = $this->payTypes[0]['value'];

        $this->tniGroups = FilterHelper::getTniGroups();
        $this->tniGroup = $this->tniGroups[0]['value'];

        $this->tniUnits = FilterHelper::getTniUnits();
        $this->tniUnit = $this->tniUnits[0]['value'];

        $this->limits =  FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];
    }

    public function render()
    {
        $patients = PatientRepository::getAll(
            limit: $this->limit,
            ageCategory: $this->ageCategory,
            gender: $this->gender,
            search: $this->search,
            type: $this->type,
            payType: $this->payType,
            tniGroup: $this->tniGroup,
            tniUnit: $this->tniUnit
        );

        return view('pages.patient.index', compact('patients'));
    }

    public function updated($attribute)
    {
        $this->resetPage();
    }
}
