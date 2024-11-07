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
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'patient show']);
        Permission::create(['name' => 'patient recap']);
        Permission::create(['name' => 'registered-patient show']);
        Permission::create(['name' => 'registered-patient recap']);
        Permission::create(['name' => 'medical-personnel show']);
        Permission::create(['name' => 'medical-non-personnel show']);
        Permission::create(['name' => 'outpatient show']);
        Permission::create(['name' => 'outpatient recap']);
        Permission::create(['name' => 'inpatient show']);
        Permission::create(['name' => 'inpatient recap']);
        Permission::create(['name' => 'emergency show']);
        Permission::create(['name' => 'emergency recap']);
        Permission::create(['name' => 'room show']);
        Permission::create(['name' => 'room recap']);
        Permission::create(['name' => 'polyclinic show']);
        Permission::create(['name' => 'polyclinic recap']);
        Permission::create(['name' => 'laboratory show']);
        Permission::create(['name' => 'laboratory recap']);
        Permission::create(['name' => 'radiology show']);
        Permission::create(['name' => 'radiology recap']);
        Permission::create(['name' => 'pharmacy show']);
        Permission::create(['name' => 'pharmacy recap']);
        Permission::create(['name' => 'icd icd10 show']);
        Permission::create(['name' => 'icd icd9 show']);
        Permission::create(['name' => 'icd recap']);
        Permission::create(['name' => 'nutrition show']);
        Permission::create(['name' => 'human_resource medical_personnel show']);
        Permission::create(['name' => 'human_resource nonmedica_personnel show']);
        Permission::create(['name' => 'birth show']);
        Permission::create(['name' => 'birth recap']);
        Permission::create(['name' => 'death show']);
        Permission::create(['name' => 'death recap']);
        Permission::create(['name' => 'users']);
        Permission::create(['name' => 'role_and_permissions']);
        Permission::create(['name' => 'api']);
        Permission::create(['name' => 'configuration']);

        Role::create(['name' => 'Puskesad'])
            ->givePermissionTo(Permission::whereNotIn(
                'name',
                [
                    'users',
                    'configuration'
                ]
            )
                ->get());

        Role::create(['name' => 'Staf'])
            ->givePermissionTo(Permission::whereNotIn(
                'name',
                [
                    'users',
                    'configuration'
                ]
            )
                ->get());

        Role::create(['name' => 'Manajemen'])
            ->givePermissionTo(Permission::whereNotIn(
                'name',
                [
                    'users',
                    'configuration'
                ]
            )
                ->get());

        Role::create(['name' => 'Administrator'])
            ->givePermissionTo(Permission::whereNotIn(
                'name',
                [
                    'users',
                    'configuration'
                ]
            )
                ->get());

        Role::create(['name' => 'Superadmin'])
            ->givePermissionTo(Permission::all());
    }
}
