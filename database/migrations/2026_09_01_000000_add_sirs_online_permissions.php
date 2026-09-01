<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Menambahkan permission modul SIRS Online (RL 1-5) secara idempoten, lalu memberikannya
     * ke role yang sudah punya permission modul terkait. Aman dijalankan ulang di lingkungan mana pun.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = [
            'sirs.dashboard',
            'sirs.rl1',
            'sirs.rl2',
            'sirs.rl3_bulanan',
            'sirs.rl3_tahunan',
            'sirs.rl4',
            'sirs.rl5',
        ];

        $permissions = collect($permissionNames)->map(
            fn($name) => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
        );

        $roles = Role::whereHas('permissions', fn($q) => $q->where('name', 'inpatient show'))->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }

        $itRumkit = User::where('username', 'it_rumkit')->first();
        if ($itRumkit) {
            $itRumkit->givePermissionTo($permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void {}
};
