<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Menambahkan permission 'registered-patient report' dan 'operation-schedule show' secara idempoten
     * (untuk halaman Laporan Kunjungan dan Pengunjung serta Jadwal Operasi), lalu memberikannya ke role
     * yang sudah memiliki permission modul terkait. Migration ini aman dijalankan ulang di lingkungan mana pun.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $additions = [
            'registered-patient report' => 'registered-patient recap',
            'operation-schedule show' => 'inpatient show',
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

        Permission::whereIn('name', ['registered-patient report', 'operation-schedule show'])->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
