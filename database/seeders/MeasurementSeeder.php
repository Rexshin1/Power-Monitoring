<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Measurement::create([
            'voltage' => 220.5,
            'current' => 10.2,
            'power' => 2240,
            'energy' => 1024.5,
            'power_factor' => 0.98,
            'frequency' => 50.0,
        ]);
    }
}
