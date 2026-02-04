<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\PowerSource;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Location if none exists
        if (Location::count() == 0) {
            Location::create([
                'name' => 'Gedung Pusat',
                'code' => 'GP-01',
                'floor' => 1,
                'power_category' => 'B1',
                'tariff_per_kwh' => 1444,
                'installed_power' => 2200
            ]);
            $this->command->info('Gedung Pusat seeded.');
        }

        // Create Power Source if none exists
        if (PowerSource::count() == 0) {
            PowerSource::create([
                'name' => 'PLN Utama 1',
                'area' => 'Gedung Pusat',
                'type' => 'grid',
                'nominal_voltage' => 220,
                'cost_per_kwh' => 1444,
                'is_active' => true
            ]);
            $this->command->info('Power Source seeded.');
        }
    }
}
