<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Permission 'operation-schedule recap' (untuk halaman Rekap Operasi) sudah ada di
     * seeder/route/sidebar tapi belum tentu tercipta di database yang sudah pernah di-migrate
     * sebelumnya. Migration ini idempoten — aman dijalankan ulang di lingkungan mana pun.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name' => 'operation-schedule recap',
            'guard_name' => 'web',
        ]);

        $roles = Role::whereHas('permissions', fn($q) => $q->where('name', 'operation-schedule show'))->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void {}
};
