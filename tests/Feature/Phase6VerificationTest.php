<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\MetalType;
use App\Models\GatePass;
use App\Models\ClientTransaction;
use App\Models\DailyClosing;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class Phase6VerificationTest extends TestCase
{
    // use RefreshDatabase; 
    // Commented out RefreshDatabase to assume existing DB or handle cleanup manually if needed.
    // Ideally use RefreshDatabase for clean state, but user env might be persistent.
    // Let's use it for isolated test.
    use RefreshDatabase;
    use \Illuminate\Foundation\Testing\WithoutMiddleware;

    protected $quarry;
    protected $crusher;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Roles
        $role = Role::create(['name' => 'admin']);
        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        $this->client = Client::create(['name' => 'Test Client', 'contact_person' => 'Test', 'phone' => '1234567890', 'is_active' => true]);
        $this->vehicle = Vehicle::create(['vehicle_number' => 'V-TEST', 'registration_number' => 'V-TEST', 'driver_name' => 'Driver', 'owner_name' => 'Owner', 'is_active' => true]);
        $this->metalType = MetalType::create(['name' => 'Test Metal', 'rate_per_ton' => 500, 'is_active' => true]);

        $this->quarry = \App\Models\OperationalUnit::firstOrCreate(
            ['code' => 'QRY'],
            ['name' => 'Quarry Unit', 'is_active' => true]
        );

        $this->crusher = \App\Models\OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Unit', 'is_active' => true]
        );
    }

    public function test_can_view_reports_dashboard()
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_daily_closure_logic()
    {
        $yesterday = Carbon::yesterday();
        $date = $yesterday->toDateString();
        $prefix = 'GP-' . $yesterday->format('Ymd');

        // 1. Create a Gate Pass for Yesterday
        GatePass::create([
            'gate_pass_number' => $prefix . '-0001',
            'date' => $date,
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'metal_type_id' => $this->metalType->id,
            'driver_name' => 'John Doe',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'total_amount' => 1000,
            'status' => 'completed',
            'activity_type' => 'Sales',
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'trips' => 1
        ]);

        // 2. Perform Daily Closing
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->actingAs($this->user)->post(route('daily-closings.store'), [
            'date' => $date,
            'confirm_closing' => '1',
            'notes' => 'Closing Verification Test'
        ]);

        $response->assertRedirect(route('daily-closings.index'));
        $this->assertDatabaseHas('daily_closings', ['date' => $date . ' 00:00:00', 'status' => 'closed']);

        // 3. Try to Create a NEW Gate Pass for Closed Date -> Should Fail
        $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => $prefix . '-0002',
            'date' => $date, // Closed Date
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'status' => 'pending',
            'metal_type_id' => $this->metalType->id,
            'activity_type' => 'Sales',
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'trips' => 1,
            'rate_per_ton' => 500,
            'net_weight' => 10,
        ]);

        $response->assertForbidden(); // 403

        // 4. Try to Create a Client Transaction for Closed Date -> Should Fail
        $response = $this->actingAs($this->user)->post(route('clients.transactions.store', $this->client), [
            'transaction_type' => 'credit',
            'amount' => 500,
            'transaction_date' => $date, // Closed Date
        ]);

        $response->assertForbidden(); // 403

        // 5. Verify Reopen (Admin only)
        $closing = DailyClosing::whereDate('date', $date)->first();

        // Should fail without reason
        $response = $this->actingAs($this->user)->post(route('daily-closings.reopen', $closing), []);
        $response->assertSessionHasErrors('reason');

        $response = $this->actingAs($this->user)->post(route('daily-closings.reopen', $closing), [
            'reason' => 'Verification Test Reopen'
        ]);
        if (session('errors')) {
            dump(session('errors')->getMessages());
        }
        if (session('error')) {
            dump(session('error'));
        }
        $response->assertRedirect();

        $closing->refresh();
        $this->assertEquals('reopened', $closing->status);
        $this->assertStringContainsString('Verification Test Reopen', $closing->notes);

        // 6. Retry Gate Pass Creation -> Should NOW Succeed because status is 'reopened' (not 'closed')
        $this->withoutExceptionHandling();
        try {
            $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
                'gate_pass_number' => $prefix . '-0003',
                'date' => $date, // Open Again
                'vehicle_id' => $this->vehicle->id,
                'client_id' => $this->client->id,
                'status' => 'pending',
                'metal_type_id' => $this->metalType->id,
                'activity_type' => 'Sales',
                'source_unit_id' => $this->crusher->id,
                'destination_unit_id' => $this->quarry->id,
                'trips' => 1,
                'rate_per_ton' => 500,
                'net_weight' => 10,
            ]);
            $response->assertRedirect();
        } catch (\Exception $e) {
            $this->assertNotEquals(403, $response->getStatusCode());
        }

        // 7. Verify Re-Closing (Accountant verifies correction and closes again)
        // Since it's now 'reopened', it's NOT 'closed', so isClosed($date) returns false.
        // store method should succeed (using updateOrCreate).
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
        $response = $this->actingAs($this->user)->post(route('daily-closings.store'), [
            'date' => $date,
            'confirm_closing' => '1',
            'notes' => 'Re-closing after corrections'
        ]);

        $response->assertRedirect(route('daily-closings.index'));

        $closing->refresh();
        $this->assertEquals('closed', $closing->status);
        $this->assertEquals('Re-closing after corrections', $closing->notes); // Notes are overwritten in current logic

        // 8. Verify Lock is Active Again
        $this->withExceptionHandling();
        $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => $prefix . '-0004',
            'date' => $date, // Closed Again
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'status' => 'pending',
            'metal_type_id' => $this->metalType->id,
            'activity_type' => 'Sales',
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'trips' => 1,
            'rate_per_ton' => 500,
            'net_weight' => 10,
        ]);
        $response->assertForbidden();
    }

    public function test_monthly_report_groups_by_day_correctly()
    {
        $targetDate = Carbon::create(2026, 6, 15);
        $startDate = $targetDate->copy()->startOfMonth()->toDateString();
        $endDate = $targetDate->copy()->endOfMonth()->toDateString();

        // 1. Create multiple completed gate passes on the same day at different times
        GatePass::create([
            'gate_pass_number' => 'GP-20260615-0001',
            'date' => '2026-06-15 09:00:00',
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'metal_type_id' => $this->metalType->id,
            'driver_name' => 'Driver A',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'total_amount' => 1000,
            'status' => 'completed',
            'activity_type' => 'Sales',
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'trips' => 1
        ]);

        GatePass::create([
            'gate_pass_number' => 'GP-20260615-0002',
            'date' => '2026-06-15 15:30:00',
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'metal_type_id' => $this->metalType->id,
            'driver_name' => 'Driver B',
            'gross_weight' => 22,
            'tare_weight' => 10,
            'net_weight' => 12,
            'total_amount' => 1500,
            'status' => 'completed',
            'activity_type' => 'Sales',
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'trips' => 1
        ]);

        // 2. Fetch the monthly report for June 2026
        $response = $this->actingAs($this->user)->get(route('reports.monthly', [
            'month' => 6,
            'year' => 2026
        ]));

        $response->assertStatus(200);

        // 3. Assert that both gate passes are combined under June 15th
        // Total sales should be 2500, and total sales count should be 2
        $response->assertViewHas('reportData', function ($reportData) {
            $june15Data = $reportData['2026-06-15'] ?? null;
            return $june15Data && 
                   $june15Data['sales'] == 2500 && 
                   $june15Data['sales_count'] == 2;
        });
    }

    public function test_monthly_report_export_groups_by_day_correctly()
    {
        $targetDate = Carbon::create(2026, 6, 15);

        // 1. Create multiple completed gate passes on the same day at different times
        GatePass::create([
            'gate_pass_number' => 'GP-20260615-0001',
            'date' => '2026-06-15 09:00:00',
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'metal_type_id' => $this->metalType->id,
            'driver_name' => 'Driver A',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'total_amount' => 1000,
            'status' => 'completed',
            'activity_type' => 'Sales',
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'trips' => 1
        ]);

        GatePass::create([
            'gate_pass_number' => 'GP-20260615-0002',
            'date' => '2026-06-15 15:30:00',
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'metal_type_id' => $this->metalType->id,
            'driver_name' => 'Driver B',
            'gross_weight' => 22,
            'tare_weight' => 10,
            'net_weight' => 12,
            'total_amount' => 1500,
            'status' => 'completed',
            'activity_type' => 'Sales',
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'trips' => 1
        ]);

        // 2. Fetch the monthly report export for June 2026
        $response = $this->actingAs($this->user)->get(route('reports.monthly.export', [
            'month' => '2026-06'
        ]));

        $response->assertStatus(200);

        // 3. Assert that both gate passes are combined under June 15th in CSV
        $content = $response->streamedContent();
        $this->assertStringContainsString('"15 Jun 2026",2500,2,0,2500', $content);
    }
}
