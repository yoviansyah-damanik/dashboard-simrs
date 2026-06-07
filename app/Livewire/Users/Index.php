<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use App\Helpers\FilterHelper;
use App\Repository\UserRepository;

class Index extends Component
{
    public array $limits;
    public array $roles;

    #[Url]
    public ?string $search = null;

    #[Url]
    public string $limit;

    #[Url]
    public string $role;

    #[On('refreshUsers')]
    public function refreshUsers()
    {
        // Memicu render ulang tabel pengguna setelah aksi tambah/ubah/hapus/aktivasi
    }

    public function mount()
    {
        $this->limits =  FilterHelper::getPerPageList();
        $this->limit = $this->limits[0];

        $this->roles = FilterHelper::getRoles();
        $this->role = $this->roles[0]['value'];
    }

    public function render()
    {
        $users = UserRepository::getAll(
            limit: $this->limit,
            role: $this->role,
            search: $this->search
        );

        return view('pages.users.index', compact('users'));
    }
}
