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
                'username' => 'yoviansyah25',
                'name' => 'Yoviansyah Rizki Pratama, S.Kom',
                'email' => 'yoviansyahrizkypratama@gmail.com',
                'password' => bcrypt('dAmaniK2511'),
            ]
        )->assignRole('IT');

        User::create(
            [
                'username' => 'puskesad2024',
                'name' => 'IT Puskesad',
                'email' => 'puskesad@gmail.com',
                'password' => bcrypt('@Puskesad2024'),
            ]
        )->assignRole('Puskesad');

        User::create(
            [
                'username' => 'stafrumkit2024',
                'name' => 'Staf Rumkit',
                'email' => 'rumkittnipsp@gmail.com',
                'password' => bcrypt('@Staf2024'),
            ]
        )->assignRole('Staf Rumah Sakit');

        User::create(
            [
                'username' => 'rumkittnipsp2024',
                'name' => 'dr. Rio Heryanto Gunawan, Sp. THT-KL',
                'email' => 'heryantoriogunawan@gmail.com',
                'password' => bcrypt('@Rumkittnipsp2024'),
            ]
        )->assignRole('Kepala Rumah Sakit');
    }
}
