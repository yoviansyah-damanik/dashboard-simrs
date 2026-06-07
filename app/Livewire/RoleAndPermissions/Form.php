<?php

namespace App\Livewire\RoleAndPermissions;

use Livewire\Component;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use App\Repository\RoleAndPermissionRepository;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Form extends Component
{
    use LivewireAlert;

    public ?int $roleId = null;

    public string $name = '';
    public array $permissions = [];
    public array $groupedPermissions = [];

    public function mount()
    {
        $this->groupedPermissions = RoleAndPermissionRepository::getGroupedPermissions();
    }

    /**
     * Menyiapkan form untuk mode tambah (tanpa $id) atau ubah (dengan $id) peran.
     */
    #[On('setRoleForm')]
    public function setRoleForm(?int $id = null)
    {
        $this->resetValidation();
        $this->reset(['roleId', 'name', 'permissions']);

        if ($id) {
            $role = Role::with('permissions')->find($id);

            if ($role) {
                $this->roleId = $role->id;
                $this->name = $role->name;
                $this->permissions = $role->permissions->pluck('name')->toArray();
            }
        }
    }

    public function togglePermissionGroup(string $group, bool $checked)
    {
        $groupPermissions = $this->groupedPermissions[$group] ?? [];

        if ($checked) {
            $this->permissions = array_values(array_unique([...$this->permissions, ...$groupPermissions]));
        } else {
            $this->permissions = array_values(array_diff($this->permissions, $groupPermissions));
        }
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:45', Rule::unique('roles', 'name')->ignore($this->roleId)],
            'permissions' => 'array',
        ];
    }

    private function attributes(): array
    {
        return [
            'name' => 'Nama Peran',
            'permissions' => 'Hak Akses',
        ];
    }

    public function updated($attribute)
    {
        if ($attribute === 'name') {
            $this->validateOnly($attribute, $this->rules(), [], $this->attributes());
        }
    }

    public function save()
    {
        $this->validate($this->rules(), [], $this->attributes());

        try {
            if ($this->roleId) {
                RoleAndPermissionRepository::update($this->roleId, $this->name, $this->permissions);
                $this->alert('success', 'Berhasil memperbaharui peran.');
            } else {
                RoleAndPermissionRepository::create($this->name, $this->permissions);
                $this->alert('success', 'Berhasil menambahkan peran.');
            }

            $this->dispatch('refreshRoles');
            $this->dispatch('toggle-role-form-modal');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('pages.role-and-permissions.form');
    }
}
