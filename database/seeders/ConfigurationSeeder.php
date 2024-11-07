<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            ['attribute' => 'WITH_INACTIVE_POLYCLINIC', 'value' => false],
            ['attribute' => 'WITH_INACTIVE_PERSON_RESPONSIBILITY', 'value' => false],
            ['attribute' => 'WITH_INACTIVE_DOCTOR', 'value' => false],
            ['attribute' => 'WITH_INACTIVE_ROOM', 'value' => false],
            ['attribute' => 'WITH_INACTIVE_WARD', 'value' => false],
        ];

        foreach ($configs as $config)
            Configuration::create([
                'attribute' => $config['attribute'],
                'value' => $config['value'],
            ]);
    }
}
