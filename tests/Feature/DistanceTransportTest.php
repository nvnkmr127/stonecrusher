<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\GatePass;
use App\Models\MetalType;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DistanceTransportTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed necessary data
        Setting::set('rate_per_km', 10);

        // Create admin role if not exists (RefreshDatabase might wipe it)
        Role::create(['name' => 'admin']);
    }

    public function test_can_create_gate_pass_with_distance_and_transport_cost()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $client = Client::create(['name' => 'Test Client', 'phone' => '1234567890', 'address' => 'Test Address']);
        $vehicle = Vehicle::create(['registration_number' => 'TS01AB1234', 'type' => 'Truck', 'model' => 'Ashok Leyland']);
        $metalType = MetalType::create(['name' => 'Test Metal', 'unit_price' => 100]);

        $response = $this->actingAs($user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-TEST-001',
            'date' => now(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'status' => 'pending',
            'delivery_location' => 'Test Location',
            'distance_km' => 50,
            'transport_cost' => 500, // 50 * 10
        ]);

        $response->assertRedirect(route('gate-passes.index'));

        $this->assertDatabaseHas('gate_passes', [
            'gate_pass_number' => 'GP-TEST-001',
            'delivery_location' => 'Test Location',
            'distance_km' => 50,
            'transport_cost' => 500,
        ]);
    }

    public function test_can_update_gate_pass_transport_details()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $client = Client::create(['name' => 'Test Client 2', 'phone' => '0987654321', 'address' => 'Test Address 2']);
        $vehicle = Vehicle::create(['registration_number' => 'TS02CD5678', 'type' => 'Truck', 'model' => 'Tata']);
        $metalType = MetalType::create(['name' => 'Test Metal 2', 'unit_price' => 200]);

        $gatePass = GatePass::create([
            'gate_pass_number' => 'GP-TEST-UPDATE',
            'date' => now(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'metal_type_id' => $metalType->id,
            'driver_name' => 'Original Driver',
            'gross_weight' => 10,
            'tare_weight' => 5,
            'net_weight' => 5,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->put(route('gate-passes.update', $gatePass), [
            'date' => now(),
            'vehicle_id' => $gatePass->vehicle_id,
            'client_id' => $gatePass->client_id,
            'metal_type_id' => $metalType->id,
            'driver_name' => 'Driver Name',
            'status' => 'pending',
            'delivery_location' => 'Updated Location',
            'distance_km' => 100,
            'transport_cost' => 1000,
        ]);

        $response->assertRedirect(route('gate-passes.index'));

        $this->assertDatabaseHas('gate_passes', [
            'id' => $gatePass->id,
            'driver_name' => 'Driver Name',
            'delivery_location' => 'Updated Location',
            'distance_km' => 100,
            'transport_cost' => 1000,
        ]);
    }
}
