<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create(
            [
                'username' => 'it_rumkit',
                'name' => 'IT Rumkit Tk. IV Padangsidimpuan',
                'email' => 'it_rumkittnipsp@gmail.com',
                'password' => bcrypt('password'),
                'as' => 'Superadmin'
            ]
        )->assignRole('Superadmin');

        User::create(
            [
                'username' => 'yoviansyah25',
                'name' => 'Yoviansyah Rizki Pratama, S.Kom',
                'email' => 'yoviansyahrizkypratama@gmail.com',
                'password' => bcrypt('password'),
                'as' => 'IT'
            ]
        )->assignRole('Administrator');

        User::create(
            [
                'username' => 'puskesad2024',
                'name' => 'IT Puskesad',
                'email' => 'puskesad@gmail.com',
                'password' => bcrypt('Puskesad2024@'),
                'as' => 'IT Puskesad'
            ]
        )->assignRole('Puskesad');

        User::create(
            [
                'username' => 'stafrumkit2024',
                'name' => 'Staf Rumkit',
                'email' => 'rumkittnipsp@gmail.com',
                'password' => bcrypt('password'),
                'as' => 'Staf Tuud'
            ]
        )->assignRole('Staf');

        User::create(
            [
                'username' => 'rumkittnipsp2024',
                'name' => 'dr. Rio Heryanto Gunawan, Sp. THT-KL',
                'email' => 'heryantoriogunawan@gmail.com',
                'password' => bcrypt('password'),
                'as' => 'Kepala Rumah Sakit'
            ]
        )->assignRole('Manajemen');
    }
}
