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
use App\Services\Quarry\QuarryCostService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class QuarryCostEngineTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $quarry;
    protected $vendor;
    protected $vehicle;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Create Quarry Unit
        $this->quarry = OperationalUnit::firstOrCreate(
            ['code' => 'QRY'],
            ['name' => 'Quarry Plant', 'is_active' => true]
        );

        // Create Contractor
        $this->vendor = Vendor::create([
            'name' => 'Drill Contractor',
            'is_active' => true,
        ]);

        // Create Vehicle
        $this->vehicle = Vehicle::create([
            'registration_number' => 'TS08Q1111',
            'type' => 'Compressor',
            'model' => 'Atlas',
            'is_active' => true,
        ]);

        Setting::set('default_diesel_rate', 100.00);

        $this->service = new QuarryCostService();
    }

    /**
     * Test aggregate cost breakdown calculation.
     */
    public function test_cost_breakdown_calculation(): void
    {
        // 1. Drilling Log (gross: 10000, diesel: 2000, net: 8000)
        QuarryDrillingLog::create([
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->vendor->id,
            'date' => '2026-05-10',
            'no_of_holes' => 5,
            'total_feet' => 200,
            'rate_per_foot' => 50,
            'gross_amount' => 10000.00,
            'diesel_deduction_amount' => 2000.00,
            'advance_deduction_amount' => 0.00,
            'net_amount' => 8000.00,
            'recorded_by' => $this->admin->id,
        ]);

        // 2. Blast & Material Used (cost: 5000)
        $blast = QuarryBlast::create([
            'operational_unit_id' => $this->quarry->id,
            'date' => '2026-05-12',
            'blast_number' => 'BL-99',
            'holes_blasted' => 5,
            'recorded_by' => $this->admin->id,
        ]);

        QuarryBlastingMaterialUsed::create([
            'quarry_blast_id' => $blast->id,
            'vendor_id' => $this->vendor->id,
            'material_type' => 'explosives_kg',
            'quantity' => 50,
            'rate' => 100,
            'amount' => 5000.00,
        ]);

        // 3. Internal Diesel Issue (cost: 3000)
        DieselEntry::create([
            'date' => '2026-05-14',
            'vehicle_id' => $this->vehicle->id,
            'operational_unit_id' => $this->quarry->id,
            'liters' => 30, // 30 * 100 = 3000
            'driver_name' => 'Internal Operator',
            'work_type' => 'Excavation',
            'vendor_id' => null, // internal
        ]);

        $breakdown = $this->service->getCostBreakdown($this->quarry->id, '2026-05-01', '2026-05-31');

        $this->assertEquals(8000.00, $breakdown['drilling']['net']);
        $this->assertEquals(5000.00, $breakdown['blasting_materials']);
        $this->assertEquals(3000.00, $breakdown['internal_diesel']['cost']);
        $this->assertEquals(16000.00, $breakdown['totals']['net']);
    }

    /**
     * Test caching and cache invalidation strategies.
     */
    public function test_caching_and_invalidation_strategy(): void
    {
        $log = QuarryDrillingLog::create([
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->vendor->id,
            'date' => '2026-05-10',
            'no_of_holes' => 5,
            'total_feet' => 200,
            'rate_per_foot' => 50,
            'gross_amount' => 10000.00,
            'diesel_deduction_amount' => 2000.00,
            'advance_deduction_amount' => 0.00,
            'net_amount' => 8000.00,
            'recorded_by' => $this->admin->id,
        ]);

        $cacheKey = "quarry:cost:breakdown:{$this->quarry->id}:2026-05-01:2026-05-31";

        // Query once to load cache
        $res1 = $this->service->getCostBreakdown($this->quarry->id, '2026-05-01', '2026-05-31');
        $this->assertEquals(8000.00, $res1['totals']['net']);
        $this->assertTrue(Cache::has($cacheKey));

        // Modify drilling log to change net_amount
        $log->update(['net_amount' => 9000.00]);

        // Assert cache was cleared automatically by observer
        $this->assertFalse(Cache::has($cacheKey));

        // Query again, should retrieve updated value
        $res2 = $this->service->getCostBreakdown($this->quarry->id, '2026-05-01', '2026-05-31');
        $this->assertEquals(9000.00, $res2['totals']['net']);
    }

    /**
     * Test monthly aggregates.
     */
    public function test_monthly_summary_aggregation(): void
    {
        // Jan - Drilling cost 6000
        QuarryDrillingLog::create([
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->vendor->id,
            'date' => '2026-01-10',
            'total_feet' => 100,
            'rate_per_foot' => 60,
            'gross_amount' => 6000.00,
            'diesel_deduction_amount' => 0.00,
            'advance_deduction_amount' => 0.00,
            'net_amount' => 6000.00,
            'recorded_by' => $this->admin->id,
        ]);

        $summary = $this->service->getMonthlySummary($this->quarry->id, 2026);

        $this->assertCount(12, $summary['monthly_breakdown']);
        $this->assertEquals('01', $summary['monthly_breakdown'][0]['month']);
        $this->assertEquals(6000.00, $summary['monthly_breakdown'][0]['drilling']);
        $this->assertEquals(6000.00, $summary['monthly_breakdown'][0]['total']);
    }

    /**
     * Test vendor-wise aggregations.
     */
    public function test_vendor_summary_aggregation(): void
    {
        QuarryDrillingLog::create([
            'operational_unit_id' => $this->quarry->id,
            'vendor_id' => $this->vendor->id,
            'date' => '2026-05-10',
            'total_feet' => 200,
            'rate_per_foot' => 50,
            'gross_amount' => 10000.00,
            'diesel_deduction_amount' => 2000.00,
            'advance_deduction_amount' => 1000.00,
            'net_amount' => 7000.00,
            'recorded_by' => $this->admin->id,
        ]);

        $vendorSummary = $this->service->getVendorSummary($this->quarry->id, '2026-05-01', '2026-05-31');

        $this->assertCount(1, $vendorSummary['vendors_breakdown']);
        $this->assertEquals($this->vendor->id, $vendorSummary['vendors_breakdown'][0]['vendor_id']);
        $this->assertEquals($this->vendor->name, $vendorSummary['vendors_breakdown'][0]['vendor_name']);
        $this->assertEquals(10000.00, $vendorSummary['vendors_breakdown'][0]['gross']);
        $this->assertEquals(2000.00, $vendorSummary['vendors_breakdown'][0]['diesel_deductions']);
        $this->assertEquals(1000.00, $vendorSummary['vendors_breakdown'][0]['advance_deductions']);
        $this->assertEquals(7000.00, $vendorSummary['vendors_breakdown'][0]['net']);
    }

    /**
     * Test API Endpoints.
     */
    public function test_api_endpoints_return_correct_response(): void
    {
        // 1. Cost Breakdown API
        $response = $this->actingAs($this->admin)
            ->getJson(route('api.quarry.cost-breakdown', [
                'unit' => $this->quarry->id,
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-31',
            ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'unit_id',
            'period' => ['start', 'end'],
            'drilling' => ['gross', 'diesel_deduction', 'advance_deduction', 'net'],
            'blasting_materials',
            'secondary_blasting',
            'contractor_labour',
            'internal_diesel',
            'totals' => ['gross', 'diesel_deductions', 'advance_deductions', 'net']
        ]);

        // 2. Daily Summary API
        $dailyResponse = $this->actingAs($this->admin)
            ->getJson(route('api.quarry.daily-summary', [
                'unit' => $this->quarry->id,
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-05',
            ]));

        $dailyResponse->assertStatus(200);
        $dailyResponse->assertJsonCount(5, 'daily_breakdown');

        // 3. Vendor Summary API
        $vendorResponse = $this->actingAs($this->admin)
            ->getJson(route('api.quarry.vendor-summary', [
                'unit' => $this->quarry->id,
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-31',
            ]));

        $vendorResponse->assertStatus(200);
    }
}
