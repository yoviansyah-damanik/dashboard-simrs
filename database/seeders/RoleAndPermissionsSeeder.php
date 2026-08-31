<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Idempoten: aman dijalankan berulang kali tanpa error meskipun sebagian
     * permission/role sudah ada di database.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
