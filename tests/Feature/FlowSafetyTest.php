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

    /** @test */
    public function gate_pass_number_generation_excludes_soft_deleted_passes()
    {
        $client = Client::create(['name' => 'GP Test Client', 'is_active' => true, 'credit_limit' => 50000]);
        $vehicle = Vehicle::create(['registration_number' => 'KA-01-GP-0001', 'is_active' => true, 'model' => 'Tata', 'cft' => 1]);
        $metal = MetalType::create(['name' => 'GP Metal', 'rate_per_ton' => 400, 'is_active' => true]);

        // Create completed Gate Pass and soft delete it
        $gp = GatePass::create([
            'gate_pass_number' => 'GP-20260418-0010',
            'date' => '2026-04-18 10:00:00',
            'vehicle_id' => $vehicle->id,
            'client_id' => $client->id,
            'status' => GatePassStatus::COMPLETED->value,
            'metal_type_id' => $metal->id,
            'driver_name' => 'Driver GP',
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
        
        $gp->delete();

        // Check endpoint
        $response = $this->actingAs($this->user)->get('/gate-passes/next-number?date=2026-04-18');
        
        $response->assertStatus(200);
        $response->assertJson([
            'next_number' => 'GP-20260418-0011'
        ]);
    }

    /** @test */
    public function regular_sale_payment_toggle_logic()
    {
        $vehicle = Vehicle::create(['registration_number' => 'KA-01-GP-9999', 'is_active' => true, 'model' => 'Tata', 'cft' => 10]);
        $metal = MetalType::create(['name' => 'GP Metal Regular', 'rate_per_ton' => 300, 'is_active' => true]);

        $quarry = \App\Models\OperationalUnit::firstOrCreate(
            ['code' => 'QRY'],
            ['name' => 'Quarry Unit', 'is_active' => true]
        );

        $crusher = \App\Models\OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Unit', 'is_active' => true]
        );

        // 1. Create a regular sale with payment_status 'paid'
        $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-' . now()->format('Ymd') . '-888',
            'date' => now()->toDateString(),
            'vehicle_id' => $vehicle->id,
            'destination_type' => 'regular',
            'status' => GatePassStatus::COMPLETED->value,
            'metal_type_id' => $metal->id,
            'driver_name' => 'Driver Reg',
            'net_weight' => 10,
            'rate_per_ton' => 300,
            'activity_type' => \App\Enums\ActivityType::SALES->value,
            'source_unit_id' => $crusher->id,
            'destination_unit_id' => $quarry->id,
            'trips' => 1,
            'payment_status' => 'paid',
            'manual_customer_name' => 'Cash Customer',
        ]);

        $response->assertRedirect(route('gate-passes.index'));
        
        $gp1 = GatePass::where('gate_pass_number', 'GP-' . now()->format('Ymd') . '-888')->firstOrFail();
        $this->assertEquals(3000.00, $gp1->total_amount);
        $this->assertEquals(3000.00, $gp1->paid_amount);
        $this->assertEquals('paid', $gp1->payment_status);

        // 2. Create a regular sale with payment_status 'pending' (Unpaid)
        $response = $this->actingAs($this->user)->post(route('gate-passes.store'), [
            'gate_pass_number' => 'GP-' . now()->format('Ymd') . '-889',
            'date' => now()->toDateString(),
            'vehicle_id' => $vehicle->id,
            'destination_type' => 'regular',
            'status' => GatePassStatus::COMPLETED->value,
            'metal_type_id' => $metal->id,
            'driver_name' => 'Driver Reg 2',
            'net_weight' => 10,
            'rate_per_ton' => 300,
            'activity_type' => \App\Enums\ActivityType::SALES->value,
            'source_unit_id' => $crusher->id,
            'destination_unit_id' => $quarry->id,
            'trips' => 1,
            'payment_status' => 'pending',
            'manual_customer_name' => 'Cash Customer 2',
        ]);

        $response->assertRedirect(route('gate-passes.index'));

        $gp2 = GatePass::where('gate_pass_number', 'GP-' . now()->format('Ymd') . '-889')->firstOrFail();
        $this->assertEquals(3000.00, $gp2->total_amount);
        $this->assertEquals(0.00, $gp2->paid_amount);
        $this->assertEquals('pending', $gp2->payment_status);

        // 3. Update GP2 to 'paid'
        $response = $this->actingAs($this->user)->put(route('gate-passes.update', $gp2->id), [
            'date' => now()->toDateString(),
            'gate_pass_number' => $gp2->gate_pass_number,
            'vehicle_id' => $gp2->vehicle_id,
            'destination_type' => 'regular',
            'status' => GatePassStatus::COMPLETED->value,
            'metal_type_id' => $gp2->metal_type_id,
            'driver_name' => $gp2->driver_name,
            'net_weight' => $gp2->net_weight,
            'rate_per_ton' => $gp2->rate_per_ton,
            'activity_type' => $gp2->activity_type->value,
            'source_unit_id' => $gp2->source_unit_id,
            'destination_unit_id' => $gp2->destination_unit_id,
            'trips' => 1,
            'payment_status' => 'paid',
            'manual_customer_name' => 'Cash Customer 2 Edited',
        ]);

        $response->assertRedirect(route('gate-passes.index'));

        $gp2->refresh();
        $this->assertEquals(3000.00, $gp2->paid_amount);
        $this->assertEquals('paid', $gp2->payment_status);

        // 4. Update GP1 back to 'pending'
        $response = $this->actingAs($this->user)->put(route('gate-passes.update', $gp1->id), [
            'date' => now()->toDateString(),
            'gate_pass_number' => $gp1->gate_pass_number,
            'vehicle_id' => $gp1->vehicle_id,
            'destination_type' => 'regular',
            'status' => GatePassStatus::COMPLETED->value,
            'metal_type_id' => $gp1->metal_type_id,
            'driver_name' => $gp1->driver_name,
            'net_weight' => $gp1->net_weight,
            'rate_per_ton' => $gp1->rate_per_ton,
            'activity_type' => $gp1->activity_type->value,
            'source_unit_id' => $gp1->source_unit_id,
            'destination_unit_id' => $gp1->destination_unit_id,
            'trips' => 1,
            'payment_status' => 'pending',
            'manual_customer_name' => 'Cash Customer Edited',
        ]);

        $response->assertRedirect(route('gate-passes.index'));

        $gp1->refresh();
        $this->assertEquals(0.00, $gp1->paid_amount);
        $this->assertEquals('pending', $gp1->payment_status);
    }
}

