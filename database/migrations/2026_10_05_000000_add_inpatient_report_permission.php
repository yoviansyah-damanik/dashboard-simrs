<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Menambahkan permission 'inpatient report' untuk laporan pasien rawat inap
     * dan memberikan permission ini kepada role yang memiliki akses ke modul rawat inap.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name' => 'inpatient report',
            'guard_name' => 'web',
        ]);

        $roles = Role::whereHas('permissions', function ($q) {
            $q->whereIn('name', ['inpatient show', 'inpatient recap']);
        })->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }

        $superadmin = Role::where('name', 'Superadmin')->first();
        if ($superadmin) {
            $superadmin->givePermissionTo($permission);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $permission = Permission::where('name', 'inpatient report')->first();
        if ($permission) {
            $permission->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
