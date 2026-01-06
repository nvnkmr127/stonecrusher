<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\GatePass;
use App\Models\Vehicle;
use App\Models\Client;
use App\Models\MetalType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_distance_report_aggregates_data_correctly()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $client = Client::create(['name' => 'Test Client', 'balance' => 0]);
        $vehicle = Vehicle::create([
            'registration_number' => 'KA-01-1234',
            'model' => 'Tata',
            'type' => 'Truck',
            'transport_multiplier' => 1,
            'is_active' => true
        ]);
        $metalType = MetalType::create(['name' => 'Sand', 'rate' => 100]);

        // Pass 1: Location A, 10km, 100 cost
        GatePass::create([
            'gate_pass_number' => 'GP-RPT-1',
            'date' => now(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'metal_type_id' => $metalType->id,
            'status' => 'completed',
            'driver_name' => 'Driver 1',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'delivery_location' => 'Location A',
            'distance_km' => 10,
            'transport_cost' => 100,
            'total_amount' => 1100
        ]);

        // Pass 2: Location A, 20km, 200 cost
        GatePass::create([
            'gate_pass_number' => 'GP-RPT-2',
            'date' => now(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'metal_type_id' => $metalType->id,
            'status' => 'completed',
            'driver_name' => 'Driver 2',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'delivery_location' => 'Location A',
            'distance_km' => 20,
            'transport_cost' => 200,
            'total_amount' => 1200
        ]);

        // Pass 3: Location B, 50km, 500 cost
        GatePass::create([
            'gate_pass_number' => 'GP-RPT-3',
            'date' => now(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'metal_type_id' => $metalType->id,
            'status' => 'completed',
            'driver_name' => 'Driver 3',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'delivery_location' => 'Location B',
            'distance_km' => 50,
            'transport_cost' => 500,
            'total_amount' => 1500
        ]);

        // Pass 4: Pending (Should be ignored)
        GatePass::create([
            'gate_pass_number' => 'GP-RPT-4',
            'date' => now(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'status' => 'pending',
            'delivery_location' => 'Location A',
            'distance_km' => 10,
            'transport_cost' => 100
        ]);

        $response = $this->actingAs($user)->get(route('gate-passes.distance-report'));

        $response->assertStatus(200);

        // Check Summary Data
        $response->assertViewHas('summary', function ($summary) {
            return $summary['total_trips'] === 3 &&
                $summary['total_distance'] == 80 && // 10 + 20 + 50
                $summary['total_cost'] == 800;      // 100 + 200 + 500
        });

        // Check Breakdown Data
        $response->assertViewHas('reportData', function ($data) {
            $locA = $data->firstWhere('delivery_location', 'Location A');
            $locB = $data->firstWhere('delivery_location', 'Location B');

            return $locA && $locA->trip_count === 2 && $locA->total_distance == 30 && $locA->total_cost == 300 &&
                $locB && $locB->trip_count === 1 && $locB->total_distance == 50 && $locB->total_cost == 500;
        });
    }
}
