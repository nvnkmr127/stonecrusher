<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DieselLocation;

class DieselLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = ['Stone Crusher', 'Quarry'];

        foreach ($locations as $name) {
            DieselLocation::firstOrCreate(['name' => $name]);
        }
    }
}
