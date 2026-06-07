<?php

namespace App\Livewire\RoleAndPermissions;

use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;
use App\Repository\RoleAndPermissionRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Delete extends Component
{
    use LivewireAlert;

    public ?int $roleId = null;
    public ?string $name = null;
    public int $usersCount = 0;

    #[On('setRoleToDelete')]
    public function setRoleToDelete(int $id)
    {
        $role = Role::find($id);

        if ($role) {
            $this->roleId = $role->id;
            $this->name = $role->name;
            $this->usersCount = $role->users()->count();
        }
    }

    public function delete()
    {
        try {
            RoleAndPermissionRepository::delete($this->roleId);

            $this->alert('success', 'Berhasil menghapus peran.');
            $this->dispatch('refreshRoles');
            $this->dispatch('toggle-delete-role-modal');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('pages.role-and-permissions.delete');
    }
}
