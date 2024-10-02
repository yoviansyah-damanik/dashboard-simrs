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
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'show patients']);
        Permission::create(['name' => 'recap patients']);
        Permission::create(['name' => 'show registration']);
        Permission::create(['name' => 'recap registration']);
        Permission::create(['name' => 'show outpatients']);
        Permission::create(['name' => 'recap outpatients']);
        Permission::create(['name' => 'show inpatients']);
        Permission::create(['name' => 'recap inpatients']);
        Permission::create(['name' => 'show emergency']);
        Permission::create(['name' => 'recap emergency']);
        Permission::create(['name' => 'show room']);
        Permission::create(['name' => 'show polyclinic']);
        Permission::create(['name' => 'show laboratory']);
        Permission::create(['name' => 'show radiology']);
        Permission::create(['name' => 'show pharmacy']);
        Permission::create(['name' => 'show icd']);
        Permission::create(['name' => 'show nutrition']);
        Permission::create(['name' => 'show human_resource doctor']);
        Permission::create(['name' => 'show human_resource nurse_midwife']);
        Permission::create(['name' => 'show human_resource else']);
        Permission::create(['name' => 'show birth']);
        Permission::create(['name' => 'show death']);

        // or may be done by chaining
        Role::create(['name' => 'Puskesad'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'Kepala Rumah Sakit'])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => 'Staf Rumah Sakit']);

        Role::create(['name' => 'IT'])
            ->givePermissionTo(Permission::all());
    }
}
