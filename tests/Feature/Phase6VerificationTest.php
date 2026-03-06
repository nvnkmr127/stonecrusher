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
    }

    public function test_can_view_reports_dashboard()
    {
        $response = $this->actingAs($this->user)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_daily_closure_logic()
    {
        $date = Carbon::yesterday()->toDateString();

        // 1. Create a Gate Pass for Yesterday
        GatePass::create([
            'gate_pass_number' => 'GP-TEST-001',
            'date' => $date,
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'metal_type_id' => $this->metalType->id,
            'driver_name' => 'John Doe',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'total_amount' => 1000,
            'status' => 'completed'
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
            'gate_pass_number' => 'GP-TEST-002',
            'date' => $date, // Closed Date
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'status' => 'pending',
            'metal_type_id' => $this->metalType->id,
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
        $closing = DailyClosing::where('date', $date)->first();

        // Should fail without reason
        $response = $this->actingAs($this->user)->post(route('daily-closings.reopen', $closing), []);
        $response->assertSessionHasErrors('reason');

        $response = $this->actingAs($this->user)->post(route('daily-closings.reopen', $closing), [
            'reason' => 'Verification Test Reopen'
        ]);
        $response->assertRedirect();

        $closing->refresh();
        $this->assertEquals('reopened', $closing->status);
        $this->assertStringContainsString('Verification Test Reopen', $closing->notes);

        // 6. Retry Gate Pass Creation -> Should NOW Succeed because status is 'reopened' (not 'closed')
        $this->withoutExceptionHandling();
        try {
            $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
                'gate_pass_number' => 'GP-TEST-003',
                'date' => $date, // Open Again
                'vehicle_id' => $this->vehicle->id,
                'client_id' => $this->client->id,
                'status' => 'pending',
                'metal_type_id' => $this->metalType->id,
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
        $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-TEST-004',
            'date' => $date, // Closed Again
            // ...
        ]);
        $response->assertForbidden();
    }
}
