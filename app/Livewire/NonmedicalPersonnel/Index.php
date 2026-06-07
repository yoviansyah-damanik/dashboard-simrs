<?php

namespace App\Livewire\NonmedicalPersonnel;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use App\Repository\NonmedicalPersonnelRepository;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusAktif = 'semua';

    #[Url]
    public string $departemen = 'semua';

    #[Url]
    public string $gender = 'semua';

    #[Url]
    public int $limit = 25;

    public array $statusOptions;
    public array $departemenOptions;
    public array $genderOptions;
    public array $limits;

    public function mount(): void
    {
        $this->statusOptions = NonmedicalPersonnelRepository::getStatusOptions();
        $this->departemenOptions = NonmedicalPersonnelRepository::getDepartemenOptions();
        $this->genderOptions = [
            ['title' => 'Semua', 'value' => 'semua'],
            ['title' => 'Pria', 'value' => 'Pria'],
            ['title' => 'Wanita', 'value' => 'Wanita'],
        ];
        $this->limits = FilterHelper::getPerPageList();
    }

    public function render()
    {
        $records = NonmedicalPersonnelRepository::getAll(
            limit: $this->limit,
            search: $this->search ?: null,
            statusAktif: $this->statusAktif,
            departemen: $this->departemen,
            gender: $this->gender
        );

        return view('pages.nonmedical-personnel.index', compact('records'));
    }
}
