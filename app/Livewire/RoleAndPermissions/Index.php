<?php

namespace App\Livewire\RoleAndPermissions;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use App\Helpers\FilterHelper;
use App\Repository\RoleAndPermissionRepository;

class Index extends Component
{
    public array $limits;

    #[Url]
    public ?string $search = null;

    #[Url]
    public string $limit;

    #[On('refreshRoles')]
    public function refreshRoles()
    {
        // Memicu render ulang tabel peran setelah aksi tambah/ubah/hapus
    }

    public function mount()
    {
        $this->limits = FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];
    }

    public function render()
    {
        $roles = RoleAndPermissionRepository::getAll(
            search: $this->search,
            limit: $this->limit
        );

        return view('pages.role-and-permissions.index', compact('roles'));
    }
}
