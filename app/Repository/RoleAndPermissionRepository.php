<?php

namespace App\Repository;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleAndPermissionInterface {}

class RoleAndPermissionRepository implements RoleAndPermissionInterface
{
    private function mapping(Role $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
            'permissions_count' => $role->permissions->count(),
            'users_count' => $role->users()->count(),
            'created_at' => $role->created_at,
        ];
    }

    public static function getAll(?string $search = null, int $limit = 10): array | LengthAwarePaginator
    {
        $result = Role::with('permissions')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->limit($limit);

        return tap($result->paginate($limit), function ($paginatedInstance) {
            return $paginatedInstance->getCollection()->transform(function ($value) {
                return (new self)->mapping($value);
            });
        });
    }

    public static function getRole(int $id): ?array
    {
        $role = Role::with('permissions')->find($id);

        return $role ? (new self)->mapping($role) : null;
    }

    /**
     * Mengelompokkan seluruh permission berdasarkan kata pertama nama permission
     * (mis. "patient show" & "patient recap" -> grup "patient").
     */
    public static function getGroupedPermissions(): array
    {
        return Permission::all()
            ->groupBy(fn($permission) => explode(' ', $permission->name)[0])
            ->map(fn($group) => $group->pluck('name')->toArray())
            ->toArray();
    }

    public static function create(string $name, array $permissions = []): Role
    {
        $role = Role::create(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public static function update(int $id, string $name, array $permissions = []): Role
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $name]);
        $role->syncPermissions($permissions);

        return $role;
    }

    public static function delete(int $id): void
    {
        Role::findOrFail($id)->delete();
    }
}
