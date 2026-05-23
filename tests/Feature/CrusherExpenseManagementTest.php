<?php

namespace Tests\Feature;

use App\Models\CrusherExpense;
use App\Models\DailyClosing;
use App\Models\GatePass;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\OperationalUnit;
use App\Models\User;
use App\Models\Vendor;
use App\Services\Crusher\CrusherReportingService;
use App\Services\DayClosureService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CrusherExpenseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $crusher;
    protected $vendor;
    protected $vehicle;
    protected $metalType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create();

        // Create Operational Unit (Crusher)
        $this->crusher = OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Unit', 'is_active' => true]
        );

        // Create a Vendor
        $this->vendor = Vendor::create([
            'name' => 'Power Grid Corp',
            'contact_person' => 'John Doe',
            'phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create Vehicle
        $this->vehicle = \App\Models\Vehicle::create([
            'registration_number' => 'TS01AB1234',
            'type' => 'Truck',
            'model' => 'Tata',
            'is_active' => true,
        ]);

        // Create Metal Type
        $this->metalType = \App\Models\MetalType::create([
            'name' => 'Test Metal',
            'unit_price' => 100.00,
        ]);
    }

    /**
     * Test vendor creation and database storage.
     */
    public function test_can_create_vendor(): void
    {
        $this->assertDatabaseHas('vendors', [
            'id' => $this->vendor->id,
            'name' => 'Power Grid Corp',
        ]);
    }

    /**
     * Test that saving a crusher expense automatically triggers the observer
     * and inserts/updates an OperationalRecord.
     */
    public function test_saved_observer_syncs_to_operational_records(): void
    {
        $date = Carbon::now()->format('Y-m-d');

        // Create crusher expense
        $expense = CrusherExpense::create([
            'operational_unit_id' => $this->crusher->id,
            'vendor_id' => $this->vendor->id,
            'date' => $date,
            'category' => 'electricity',
            'amount' => 1500.00,
            'quantity' => 150,
            'rate' => 10,
            'payment_mode' => 'bank',
            'invoice_number' => 'INV-123',
            'recorded_by' => $this->admin->id,
        ]);

        // Assert crusher_expenses table has it
        $this->assertDatabaseHas('crusher_expenses', [
            'id' => $expense->id,
            'amount' => 1500.00,
            'category' => 'electricity',
        ]);

        // Assert operational_records has a synced record
        $this->assertDatabaseHas('operational_records', [
            'crusher_expense_id' => $expense->id,
            'operational_unit_id' => $this->crusher->id,
            'amount' => 1500.00,
            'quantity' => 150.00,
            'rate' => 10.00,
        ]);

        // Verify correct tag assignment ('Electricity')
        $tag = OperationalTag::where('operational_unit_id', $this->crusher->id)
            ->where('name', 'Electricity')
            ->first();

        $this->assertNotNull($tag);
        $this->assertEquals('expense', $tag->type);

        $this->assertDatabaseHas('operational_records', [
            'crusher_expense_id' => $expense->id,
            'operational_tag_id' => $tag->id,
        ]);

        // Update the expense amount and quantity
        $expense->update([
            'amount' => 1800.00,
            'quantity' => 180,
        ]);

        // Verify the operational record synced the updates
        $this->assertDatabaseHas('operational_records', [
            'crusher_expense_id' => $expense->id,
            'amount' => 1800.00,
            'quantity' => 180.00,
        ]);
    }

    /**
     * Test that deleting a crusher expense automatically deletes the synced OperationalRecord.
     */
    public function test_deleted_observer_removes_operational_record(): void
    {
        $date = Carbon::now()->format('Y-m-d');

        $expense = CrusherExpense::create([
            'operational_unit_id' => $this->crusher->id,
            'vendor_id' => $this->vendor->id,
            'date' => $date,
            'category' => 'maintenance',
            'amount' => 2000.00,
            'payment_mode' => 'cash',
            'recorded_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('operational_records', [
            'crusher_expense_id' => $expense->id,
        ]);

        // Soft delete the expense
        $expense->delete();

        // Synced operational record should be deleted completely
        $this->assertDatabaseMissing('operational_records', [
            'crusher_expense_id' => $expense->id,
        ]);
    }

    /**
     * Test that validation rules block inputs for closed days (Day Closure).
     */
    public function test_validation_fails_for_closed_date(): void
    {
        $closedDate = '2026-05-15';

        // Close the day
        DailyClosing::create([
            'date' => $closedDate,
            'status' => 'closed',
            'total_sales' => 0,
            'total_cash' => 0,
            'total_expenses' => 0,
            'closed_by_user_id' => $this->admin->id,
        ]);

        $rules = CrusherReportingService::getValidationRules();

        $data = [
            'operational_unit_id' => $this->crusher->id,
            'vendor_id' => $this->vendor->id,
            'date' => $closedDate,
            'category' => 'diesel',
            'amount' => 500.00,
            'payment_mode' => 'cash',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('date', $validator->errors()->toArray());
    }

    /**
     * Test reporting service calculations.
     */
    public function test_reporting_service_summary_calculation(): void
    {
        $startDate = '2026-05-01';
        $endDate = '2026-05-31';

        // Log some expenses
        CrusherExpense::create([
            'operational_unit_id' => $this->crusher->id,
            'date' => '2026-05-10',
            'category' => 'diesel',
            'amount' => 1000.00,
            'payment_mode' => 'cash',
            'recorded_by' => $this->admin->id,
        ]);

        CrusherExpense::create([
            'operational_unit_id' => $this->crusher->id,
            'date' => '2026-05-12',
            'category' => 'electricity',
            'amount' => 3000.00,
            'payment_mode' => 'upi',
            'recorded_by' => $this->admin->id,
        ]);

        // Create a fake metal sale (GatePass)
        GatePass::create([
            'gate_pass_number' => 'GP-001',
            'date' => '2026-05-15',
            'source_unit_id' => $this->crusher->id,
            'vehicle_id' => $this->vehicle->id,
            'metal_type_id' => $this->metalType->id,
            'driver_name' => 'Test Driver',
            'activity_type' => \App\Enums\ActivityType::SALES->value,
            'status' => \App\Enums\GatePassStatus::COMPLETED->value,
            'total_amount' => 8000.00,
            'paid_amount' => 8000.00,
        ]);

        $service = new CrusherReportingService();
        $summary = $service->getCrusherSummary($this->crusher->id, $startDate, $endDate);

        $this->assertEquals(8000.00, $summary['revenue']);
        $this->assertEquals(1000.00, $summary['expenses']['diesel']);
        $this->assertEquals(3000.00, $summary['expenses']['electricity']);
        $this->assertEquals(4000.00, $summary['total_expense']);
        $this->assertEquals(4000.00, $summary['net_profit']);
    }

    /**
     * Test vendor statement generation.
     */
    public function test_reporting_service_vendor_statement(): void
    {
        $startDate = '2026-05-01';
        $endDate = '2026-05-31';

        CrusherExpense::create([
            'operational_unit_id' => $this->crusher->id,
            'vendor_id' => $this->vendor->id,
            'date' => '2026-05-10',
            'category' => 'electricity',
            'amount' => 1200.00,
            'payment_mode' => 'on_account',
            'recorded_by' => $this->admin->id,
        ]);

        CrusherExpense::create([
            'operational_unit_id' => $this->crusher->id,
            'vendor_id' => $this->vendor->id,
            'date' => '2026-05-20',
            'category' => 'electricity',
            'amount' => 800.00,
            'payment_mode' => 'bank',
            'recorded_by' => $this->admin->id,
        ]);

        $service = new CrusherReportingService();
        $statement = $service->getVendorStatement($this->vendor->id, $startDate, $endDate);

        $this->assertEquals($this->vendor->id, $statement['vendor']->id);
        $this->assertCount(2, $statement['transactions']);
        $this->assertEquals(2000.00, $statement['summary']['total_invoiced']);
        $this->assertEquals(1200.00, $statement['summary']['added_to_outstanding']);
        $this->assertEquals(800.00, $statement['summary']['paid_immediately']);
    }
}
