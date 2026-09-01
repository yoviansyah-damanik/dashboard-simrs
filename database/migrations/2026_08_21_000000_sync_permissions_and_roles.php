<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Migration ini menyamakan seluruh permission (mengikuti RoleAndPermissionsSeeder) dan
     * penugasannya ke role secara idempoten, untuk lingkungan yang permission-nya sempat
     * tertinggal (mis. seeding lama sebelum modul baru ditambahkan). Aman dijalankan ulang.
     */
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = [
            'patient show',
            'patient recap',
            'registered-patient show',
            'registered-patient recap',
            'registered-patient report',
            'medical-personnel show',
            'medical-non-personnel show',
            'outpatient show',
            'outpatient recap',
            'inpatient show',
            'inpatient recap',
            'emergency show',
            'emergency recap',
            'operation-schedule show',
            'operation-schedule recap',
            'room show',
            'room recap',
            'polyclinic show',
            'polyclinic recap',
            'laboratory show',
            'laboratory recap',
            'radiology show',
            'radiology recap',
            'pharmacy show',
            'pharmacy recap',
            'icd icd10 show',
            'icd icd9 show',
            'icd recap',
            'nutrition show',
            'financial-report show',
            'patient-report show',
            'human_resource medical_personnel show',
            'human_resource nonmedica_personnel show',
            'birth show',
            'birth recap',
            'death show',
            'death recap',
            'users',
            'role_and_permissions',
            'api',
            'configuration',
        ];

        foreach ($permissionNames as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $allPermissions = Permission::where('guard_name', 'web')->get();
        $exceptUsersAndConfiguration = $allPermissions->whereNotIn('name', ['users', 'configuration']);

        foreach (['Puskesad', 'Staf', 'Manajemen', 'Administrator'] as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->givePermissionTo($exceptUsersAndConfiguration);
        }

        $superadmin = Role::firstOrCreate(['name' => 'Superadmin', 'guard_name' => 'web']);
        $superadmin->givePermissionTo($allPermissions);

        $itRumkit = User::where('username', 'it_rumkit')->first();
        if ($itRumkit) {
            if (!$itRumkit->hasRole('Superadmin')) {
                $itRumkit->assignRole($superadmin);
            }
            $itRumkit->givePermissionTo($allPermissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void {}
};
