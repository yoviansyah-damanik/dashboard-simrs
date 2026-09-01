<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Menambahkan permission 'operation-schedule recap' secara idempoten (untuk halaman Rekap
     * Jadwal Operasi) yang sebelumnya terlewat dari migration sync permission. Migration ini
     * aman dijalankan ulang di lingkungan mana pun.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $additions = [
            'operation-schedule recap' => 'operation-schedule show',
        ];

        foreach ($additions as $newPermission => $referencePermission) {
            $permission = Permission::firstOrCreate([
                'name' => $newPermission,
                'guard_name' => 'web',
            ]);

            $roles = Role::whereHas('permissions', fn($q) => $q->where('name', $referencePermission))->get();

            foreach ($roles as $role) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::whereIn('name', ['operation-schedule recap'])->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
