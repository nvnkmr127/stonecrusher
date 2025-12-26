<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'company_name' => 'Stone Crusher ERP',
            'currency_symbol' => '₹',
            'financial_year' => '2024-2025',
            'crusher_latitude' => '0.0',
            'crusher_longitude' => '0.0',
            'default_diesel_rate' => '100.00',
            'rate_per_km' => '10.00',
            'date_format' => 'd/m/Y',
            'google_maps_api_key' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
