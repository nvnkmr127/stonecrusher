<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\MetalType;
use App\Models\GatePass;
use App\Models\Employee;
use App\Enums\GatePassStatus;
use App\Enums\PaymentMode;
use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class FlowSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        // Create basic roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    /** @test */
    public function gate_pass_uses_enums_for_validation()
    {
        $client = Client::create(['name' => 'Test Client', 'is_active' => true, 'credit_limit' => 10000]);
        $vehicle = Vehicle::create(['registration_number' => 'KA-01-AB-1234', 'is_active' => true, 'model' => 'Tata', 'cft' => 1]);

        $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-' . now()->format('Ymd') . '-001',
            'date' => now()->toDateString(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'status' => 'invalid_status', // Invalid Enum Value
            'activity_type' => \App\Enums\ActivityType::SALES->value,
            'source_unit_id' => 2,
            'destination_unit_id' => 3,
            'rate_per_ton' => 500,
            'trips' => 1,
        ]);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function client_transaction_uses_enums_for_validation()
    {
        $client = Client::create(['name' => 'Test Client 2', 'is_active' => true]);

        $response = $this->actingAs($this->user)->post(route('clients.transactions.store', $client), [
            'transaction_type' => 'credit',
            'amount' => 1000,
            'transaction_date' => now()->toDateString(),
            'payment_mode' => 'InvalidMode', // Invalid Enum
        ]);

        $response->assertSessionHasErrors('payment_mode');
    }

    /** @test */
    public function user_creation_is_atomic()
    {
        $formData = [
            'name' => 'Test User',
            'email' => 'test_atomic@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'manager',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)->post(route('users.store'), $formData);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'test_atomic@example.com']);
        $user = User::where('email', 'test_atomic@example.com')->first();
        $this->assertTrue($user->hasRole('manager'));
    }

    /** @test */
    public function gate_pass_creation_is_successful_with_enums()
    {
        $client = Client::create(['name' => 'Test Client 3', 'is_active' => true, 'credit_limit' => 50000]);
        $vehicle = Vehicle::create(['registration_number' => 'KA-01-XY-9999', 'is_active' => true, 'model' => 'Benz', 'cft' => 1]);
        $metal = MetalType::create(['name' => 'Granite', 'rate_per_ton' => 500, 'is_active' => true]);

        $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-' . now()->format('Ymd') . '-002',
            'date' => now()->toDateString(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'status' => GatePassStatus::COMPLETED->value, // Valid Enum
            'metal_type_id' => $metal->id,
            'driver_name' => 'Driver X',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'total_amount' => 5000,
            'activity_type' => \App\Enums\ActivityType::SALES->value,
            'source_unit_id' => 2,
            'destination_unit_id' => 3,
            'rate_per_ton' => 500,
            'trips' => 1,
        ]);

        $response->assertRedirect(route('gate-passes.index'));
        $this->assertDatabaseHas('gate_passes', ['gate_pass_number' => 'GP-' . now()->format('Ymd') . '-002']);

        // Check transaction creation (side effect)
        $this->assertDatabaseHas('client_transactions', [
            'client_id' => $client->id,
            'amount' => 5000,
            'transaction_type' => 'debit'
        ]);
    }

    /** @test */
    public function attendance_uses_enums_for_validation()
    {
        $employee = Employee::factory()->create();

        $response = $this->actingAs($this->user)->post(route('attendance.store'), [
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'invalid_status', // Invalid Enum Value
        ]);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function attendance_auto_calculates_status_but_allows_enum_override()
    {
        $employee = Employee::factory()->create();

        // Test 1: Auto-calc (Present)
        $this->actingAs($this->user)->post(route('attendance.store'), [
            'employee_id' => $employee->id,
            'date' => now()->subDays(1)->toDateString(),
            'check_in' => '09:00',
            'check_out' => '18:35',
        ]);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => now()->subDays(1)->format('Y-m-d 00:00:00'),
            'status' => AttendanceStatus::PRESENT->value
        ]);

        // Test 2: Enum Override (Leave)
        $this->actingAs($this->user)->post(route('attendance.store'), [
            'employee_id' => $employee->id,
            'date' => now()->subDays(2)->toDateString(),
            'status' => AttendanceStatus::LEAVE->value,
        ]);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => now()->subDays(2)->format('Y-m-d 00:00:00'),
            'status' => AttendanceStatus::LEAVE->value
        ]);
    }

    /** @test */
    public function gate_pass_deletion_removes_linked_transactions()
    {
        $client = Client::create(['name' => 'Delete Test Client', 'is_active' => true, 'credit_limit' => 50000]);
        $vehicle = Vehicle::create(['registration_number' => 'KA-01-DL-8888', 'is_active' => true, 'model' => 'Tata', 'cft' => 1]);
        $metal = MetalType::create(['name' => 'Blue Metal', 'rate_per_ton' => 400, 'is_active' => true]);

        // Create completed Gate Pass
        $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-' . now()->format('Ymd') . '-999',
            'date' => now()->toDateString(),
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'status' => GatePassStatus::COMPLETED->value,
            'metal_type_id' => $metal->id,
            'driver_name' => 'Driver Y',
            'gross_weight' => 20,
            'tare_weight' => 10,
            'net_weight' => 10,
            'total_amount' => 4000,
            'activity_type' => \App\Enums\ActivityType::SALES->value,
            'source_unit_id' => 2,
            'destination_unit_id' => 3,
            'rate_per_ton' => 400,
            'trips' => 1,
        ]);

        $gp = GatePass::where('gate_pass_number', 'GP-' . now()->format('Ymd') . '-999')->firstOrFail();

        // Record a payment against this Gate Pass
        $this->actingAs($this->user)->post(route('gate-passes.payment', $gp), [
            'amount' => 1500,
            'date' => now()->toDateString(),
            'payment_mode' => PaymentMode::CASH->value,
            'remarks' => 'Partial Payment',
        ]);

        // Check database state
        $this->assertDatabaseHas('gate_passes', ['id' => $gp->id]);
        $this->assertDatabaseHas('client_transactions', [
            'gate_pass_id' => $gp->id,
            'transaction_type' => 'debit',
            'amount' => 4000
        ]);
        $this->assertDatabaseHas('client_transactions', [
            'gate_pass_id' => $gp->id,
            'transaction_type' => 'credit',
            'amount' => 1500
        ]);

        // Perform deletion
        $response = $this->actingAs($this->user)->delete(route('gate-passes.destroy', $gp));
        $response->assertRedirect(route('gate-passes.index'));

        // Assert Gate Pass is soft deleted
        $this->assertSoftDeleted('gate_passes', ['id' => $gp->id]);

        // Assert all linked transactions are deleted
        $this->assertDatabaseMissing('client_transactions', ['gate_pass_id' => $gp->id]);
    }
}
