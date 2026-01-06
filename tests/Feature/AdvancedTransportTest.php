<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedTransportTest extends TestCase
{
    use RefreshDatabase;

    public function test_advanced_calculator_logic()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Setup Settings
        Setting::set('rate_per_km', 10);
        Setting::set('crusher_latitude', 0);
        Setting::set('crusher_longitude', 0);

        // Setup Vehicle with Multiplier
        $vehicle = Vehicle::create([
            'registration_number' => 'TEST-123',
            'transport_multiplier' => 1.5,
            'is_active' => true
        ]);

        // Test Calculation via API with Multiplier and Round Trip
        $response = $this->actingAs($user)->getJson(route('gate-passes.calculator', [
            'lat' => 1, // approx 111km from 0,0
            'lon' => 0,
            'json' => 1,
            'multiplier' => 1.5,
            'round_trip' => 1
        ]));

        $response->assertStatus(200);
        $data = $response->json();

        $distance = $data['distance'];
        $rate = 10;
        $multiplier = 1.5;
        $roundTrip = 2;

        // Tolerance for distance calculation
        $expectedCost = $distance * $rate * $multiplier * $roundTrip;

        $this->assertEquals($expectedCost, $data['cost']);
    }
}
