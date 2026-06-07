<?php

namespace App\Livewire\Polyclinic;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class Index extends Component
{
    #[Url(history: true)]
    public $search = '';
    public $excludeList = [];

    public function mount()
    {
        $this->excludeList = config('app.exclude_polys');
    }

    #[Computed]
    public function poliklinik()
    {
        return DB::connection('simrs')
            ->table('poliklinik')
            ->whereNotIn('nm_poli', $this->excludeList)
            ->whereNotIn('kd_poli', $this->excludeList)
            ->where('status', 1)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nm_poli', 'like', '%' . $this->search . '%')
                        ->orWhere('kd_poli', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('nm_poli')
            ->get();
    }

    public function render()
    {
        return view('pages.polyclinic.index');
    }
}
