<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Menambahkan permission 'outpatient report' untuk laporan pasien rawat jalan
     * dan memberikan permission ini kepada role yang memiliki akses ke modul rawat jalan.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name' => 'outpatient report',
            'guard_name' => 'web',
        ]);

        $roles = Role::whereHas('permissions', function ($q) {
            $q->whereIn('name', ['outpatient show', 'outpatient recap']);
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
        $permission = Permission::where('name', 'outpatient report')->first();
        if ($permission) {
            $permission->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
