<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\MetalType;
use App\Models\GatePass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportBillableTest extends TestCase
{
    use RefreshDatabase;

    public function test_transport_billable_logic()
    {
        $this->withoutMiddleware();

        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $client = Client::create(['name' => 'Test Client', 'balance' => 0]);
        $vehicle = Vehicle::create(['registration_number' => 'TEST-BILL', 'is_active' => true]);
        $metal = MetalType::create(['name' => 'Test Metal', 'unit_price' => 100]); // 100 per unit

        // Scenario 1: Not Billable
        // Qty: 10, Rate: 100 => 1000. Transport: 50. Total should be 1000.
        $response = $this->actingAs($user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-BILL-1',
            'date' => now(),
            'status' => 'completed',
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'metal_type_id' => $metal->id,
            'driver_name' => 'Driver 1',
            'loading_quantity' => 10,
            'rate_per_ton' => 100,
            'diesel_amount' => 0,
            'distance_km' => 5, // 10 per km => 50
            'transport_cost' => 50,
            'transport_is_billable' => 0,
            'total_amount' => 1000, // Frontend sends this, validated by logic
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10
        ]);

        $gatePass1 = GatePass::where('gate_pass_number', 'GP-BILL-1')->first();
        $this->assertEquals(1000, $gatePass1->total_amount);
        $this->assertFalse((bool) $gatePass1->transport_is_billable);
        // Transaction check
        $this->assertDatabaseHas('client_transactions', [
            'gate_pass_id' => $gatePass1->id,
            'amount' => 1000
        ]);

        // Scenario 2: Billable
        // Qty: 10, Rate: 100 => 1000. Transport: 50. Total should be 1050.
        $response = $this->actingAs($user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-BILL-2',
            'date' => now(),
            'status' => 'completed',
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'metal_type_id' => $metal->id,
            'driver_name' => 'Driver 1',
            'loading_quantity' => 10,
            'rate_per_ton' => 100,
            'diesel_amount' => 0,
            'distance_km' => 5,
            'transport_cost' => 50,
            'transport_is_billable' => 1,
            'total_amount' => 1050,
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10
        ]);

        $gatePass2 = GatePass::where('gate_pass_number', 'GP-BILL-2')->first();
        $this->assertEquals(1050, $gatePass2->total_amount);
        $this->assertTrue((bool) $gatePass2->transport_is_billable);

        $this->assertDatabaseHas('client_transactions', [
            'gate_pass_id' => $gatePass2->id,
            'amount' => 1050
        ]);
    }
}
