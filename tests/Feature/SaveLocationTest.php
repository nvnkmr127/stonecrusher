<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\MetalType;
use App\Models\DeliveryDestination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_location_flow()
    {
        $this->withoutMiddleware();

        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $client = Client::create(['name' => 'Test Client', 'balance' => 0]);
        $vehicle = Vehicle::create(['registration_number' => 'TEST-LOC', 'is_active' => true]);
        $metal = MetalType::create(['name' => 'Test Metal', 'unit_price' => 100]);

        // 1. Create Gate Pass with "Save Location"
        $response = $this->actingAs($user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-LOC-1',
            'date' => now(),
            'status' => 'pending',
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'driver_name' => 'Driver 1',
            'delivery_location' => 'New Site A',
            'distance_km' => 50,
            'dest_lat' => 12.34,
            'dest_lon' => 56.78,
            'save_location' => 1
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('delivery_destinations', [
            'name' => 'New Site A',
            'distance_km' => 50,
            'latitude' => 12.34
        ]);

        // 2. Check if it appears in list (controller fetch)
        $response = $this->actingAs($user)->get(route('gate-passes.create'));
        $response->assertSee('New Site A'); // Should be in the JSON or Datalist
    }
}
