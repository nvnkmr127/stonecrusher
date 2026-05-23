<?php

namespace Tests\Feature;

use App\Models\ContractorAdvance;
use App\Models\DailyClosing;
use App\Models\DieselEntry;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\OperationalUnit;
use App\Models\QuarryBlast;
use App\Models\QuarryBlastingMaterialUsed;
use App\Models\QuarryDrillingLog;
use App\Models\QuarryLabourSheet;
use App\Models\QuarrySecondaryBlasting;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Vehicle;
use App\Services\Quarry\QuarryContractorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuarryCostingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $quarry;
    protected $contractor;
    protected $vehicle;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();

        // Create Quarry Unit
        $this->quarry = OperationalUnit::firstOrCreate(
            ['code' => 'QRY'],
            ['name' => 'Quarry Unit', 'is_active' => true]
        );

        // Create Contractor (Vendor)
        $this->contractor = Vendor::create([
            'name' => 'Apex Drilling Rig',
            'contact_person' => 'Bob Builder',
            'phone' => '9988776655',
            'is_active' => true,
        ]);

        // Create Vehicle
        $this->vehicle = Vehicle::create([
            'registration_number' => 'TS09EX9999',
            'type' => 'Rig Truck',
            'model' => 'Volvo',
            'is_active' => true,
        ]);

        // Set default rates
        Setting::set('default_diesel_rate', 100.00);

        $this->service = new QuarryContractorService();
    }

    /**
     * Test contractor advance tracking.
     */
    public function test_can_log_contractor_advances_and_check_balance(): void
    {
        // Pay 50000 advance
        ContractorAdvance::create([
            'vendor_id' => $this->contractor->id,
            'date' => '2026-05-10',
            'amount' => 50000.00,
            'payment_mode' => 'bank',
            'recorded_by' => $this->admin->id,
        ]);

        $this->assertEquals(50000.00, $this->service->getAdvanceBalance($this->contractor->id));
    }

    /**
     * Test drilling logs creation with diesel deductions and advances.
     */
    public function test_store_drilling_log_calculates_and_syncs(): void
    {
        // 1. Issue diesel to contractor vehicle
        $diesel1 = DieselEntry::create([
            'date' => '2026-05-12',
            'vehicle_id' => $this->vehicle->id,
            'operational_unit_id' => $this->quarry->id,
            'liters' => 50, // 50 * 100 = 5000 cost
            'driver_name' => 'Rig Operator',
            'vendor_id' => $this->contractor->id,
            'work_type' => 'Drilling',
        ]);

        // 2. Pay advance
        ContractorAdvance::create([
            'vendor_id' => $this->contractor->id,
            'date' => '2026-05-10',
            'amount' => 10000.00,
            'recorded_by' => $this->admin->id,
        ]);

        // 3. Store Drilling Log
        $logData = [
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->contractor->id,
            'date' => '2026-05-14',
            'no_of_holes' => 10,
            'total_feet' => 400.00,
            'rate_per_foot' => 50.00, // gross = 400 * 50 = 20000
            'advance_deduction_amount' => 8000.00,
            'recorded_by' => $this->admin->id,
        ];

        $log = $this->service->storeDrillingLog($logData, [$diesel1->id]);

        // Assert calculations
        // gross: 20000
        // diesel deduction: 5000
        // advance deduction: 8000
        // net: 20000 - 5000 - 8000 = 7000
        $this->assertEquals(20000.00, $log->gross_amount);
        $this->assertEquals(5000.00, $log->diesel_deduction_amount);
        $this->assertEquals(8000.00, $log->advance_deduction_amount);
        $this->assertEquals(7000.00, $log->net_amount);

        // Assert diesel record status updated
        $diesel1->refresh();
        $this->assertTrue($diesel1->is_deducted);
        $this->assertEquals('drilling', $diesel1->deducted_at_invoice_type);
        $this->assertEquals($log->id, $diesel1->deducted_at_invoice_id);

        // Assert advance balance adjusted: 10000 - 8000 = 2000
        $this->assertEquals(2000.00, $this->service->getAdvanceBalance($this->contractor->id));

        // Assert operational record sync
        $this->assertDatabaseHas('operational_records', [
            'quarry_drilling_log_id' => $log->id,
            'operational_unit_id' => $this->quarry->id,
            'amount' => 7000.00,
        ]);
    }

    /**
     * Test blasting logs with materials.
     */
    public function test_blasting_material_syncs_to_operational_records(): void
    {
        $blast = QuarryBlast::create([
            'operational_unit_id' => $this->quarry->id,
            'date' => '2026-05-15',
            'blast_number' => 'BL-01',
            'holes_blasted' => 5,
            'recorded_by' => $this->admin->id,
        ]);

        // Assert initial sync amount is 0
        $this->assertDatabaseHas('operational_records', [
            'quarry_blast_id' => $blast->id,
            'amount' => 0.00,
        ]);

        // Add blasting material used
        QuarryBlastingMaterialUsed::create([
            'quarry_blast_id' => $blast->id,
            'vendor_id' => $this->contractor->id,
            'material_type' => 'explosives_kg',
            'quantity' => 100.00,
            'rate' => 80.00, // cost = 8000.00
            'amount' => 8000.00,
        ]);

        // Assert that aggregate sync re-fired and amount is updated to 8000
        $this->assertDatabaseHas('operational_records', [
            'quarry_blast_id' => $blast->id,
            'amount' => 8000.00,
        ]);

        // Add another material
        QuarryBlastingMaterialUsed::create([
            'quarry_blast_id' => $blast->id,
            'vendor_id' => $this->contractor->id,
            'material_type' => 'detonators_electric',
            'quantity' => 5.00,
            'rate' => 200.00, // cost = 1000.00
            'amount' => 1000.00,
        ]);

        // Total should now be 9000
        $this->assertDatabaseHas('operational_records', [
            'quarry_blast_id' => $blast->id,
            'amount' => 9000.00,
        ]);
    }

    /**
     * Test secondary blasting log with diesel deduction.
     */
    public function test_store_secondary_blasting_calculates_and_syncs(): void
    {
        $diesel = DieselEntry::create([
            'date' => '2026-05-12',
            'vehicle_id' => $this->vehicle->id,
            'operational_unit_id' => $this->quarry->id,
            'liters' => 20, // 20 * 100 = 2000 cost
            'driver_name' => 'Operator',
            'vendor_id' => $this->contractor->id,
            'work_type' => 'Secondary Blasting',
        ]);

        $data = [
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->contractor->id,
            'date' => '2026-05-15',
            'no_of_holes' => 15,
            'amount' => 12000.00,
            'recorded_by' => $this->admin->id,
        ];

        $sb = $this->service->storeSecondaryBlasting($data, [$diesel->id]);

        $this->assertEquals(12000.00, $sb->amount);
        $this->assertEquals(2000.00, $sb->diesel_deduction_amount);
        $this->assertEquals(10000.00, $sb->net_amount);

        // Assert operational record synced
        $this->assertDatabaseHas('operational_records', [
            'quarry_secondary_blasting_id' => $sb->id,
            'amount' => 10000.00,
        ]);
    }

    /**
     * Test contractor labour sheets.
     */
    public function test_store_labour_sheet_calculates_and_syncs(): void
    {
        ContractorAdvance::create([
            'vendor_id' => $this->contractor->id,
            'date' => '2026-05-10',
            'amount' => 5000.00,
            'recorded_by' => $this->admin->id,
        ]);

        $data = [
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->contractor->id,
            'date' => '2026-05-15',
            'no_of_workers' => 20,
            'rate_per_worker' => 450.00, // gross = 9000
            'advance_deduction_amount' => 4000.00,
            'recorded_by' => $this->admin->id,
        ];

        $sheet = $this->service->storeLabourSheet($data);

        $this->assertEquals(9000.00, $sheet->gross_amount);
        $this->assertEquals(4000.00, $sheet->advance_deduction_amount);
        $this->assertEquals(5000.00, $sheet->net_amount);

        // Assert operational record synced
        $this->assertDatabaseHas('operational_records', [
            'quarry_labour_sheet_id' => $sheet->id,
            'amount' => 5000.00,
        ]);
    }

    /**
     * Test DayLock block constraints.
     */
    public function test_day_lock_blocks_quarry_costing(): void
    {
        // Close day 2026-05-15
        DailyClosing::create([
            'date' => '2026-05-15',
            'status' => 'closed',
            'total_sales' => 0,
            'total_cash' => 0,
            'total_expenses' => 0,
            'closed_by_user_id' => $this->admin->id,
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->service->storeLabourSheet([
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->contractor->id,
            'date' => '2026-05-15',
            'no_of_workers' => 10,
            'rate_per_worker' => 400.00,
            'recorded_by' => $this->admin->id,
        ]);
    }
}
