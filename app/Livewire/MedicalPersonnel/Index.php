<?php

namespace App\Livewire\MedicalPersonnel;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Helpers\FilterHelper;
use App\Repository\DoctorRepository;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'semua';

    #[Url]
    public string $spesialis = 'semua';

    #[Url]
    public int $limit = 25;

    public array $statusOptions;
    public array $spesialisOptions;
    public array $limits;

    public function mount(): void
    {
        $this->statusOptions = [
            ['title' => 'Semua', 'value' => 'semua'],
            ['title' => 'Aktif', 'value' => '1'],
            ['title' => 'Nonaktif', 'value' => '0'],
        ];

        $spesialisList = DB::connection('simrs')
            ->table('spesialis')
            ->orderBy('nm_sps')
            ->get();

        $this->spesialisOptions = [
            ['title' => 'Semua', 'value' => 'semua'],
            ...collect($spesialisList)->map(fn($s) => [
                'title' => $s->nm_sps,
                'value' => $s->kd_sps,
            ])->toArray(),
        ];

        $this->limits = FilterHelper::getPerPageList();
    }

    public function render()
    {
        $records = DoctorRepository::getAll(
            limit: $this->limit,
            spesialisCode: $this->spesialis !== 'semua' ? $this->spesialis : null,
            withRelations: true,
            search: $this->search ?: null,
            status: $this->status !== 'semua' ? (int) $this->status : null,
            withPagination: true
        );

        return view('pages.medical-personnel.index', compact('records'));
    }
}
