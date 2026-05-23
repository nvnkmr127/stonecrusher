<?php

namespace Tests\Feature;

use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\OperationalUnit;
use App\Models\User;
use App\Services\Crusher\CrusherProfitService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CrusherProfitEngineTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $crusher;
    protected $service;

    protected $tagRevenue;
    protected $tagDiesel;
    protected $tagElectricity;
    protected $tagOther;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear Spatie cached permissions
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Create roles
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Create Crusher Unit
        $this->crusher = OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Plant', 'is_active' => true]
        );

        // Seed default tags
        $this->tagRevenue = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Metal Sale',
            'type' => 'revenue',
        ]);

        $this->tagDiesel = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Diesel Used',
            'type' => 'expense',
        ]);

        $this->tagElectricity = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Electricity',
            'type' => 'expense',
        ]);

        $this->tagOther = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Repairs & Maintenance',
            'type' => 'expense',
        ]);

        $this->service = new CrusherProfitService();
    }

    /**
     * Test the basic profitability calculation logic for a date range.
     */
    public function test_profitability_calculation_for_date_range(): void
    {
        // 1. Log Sales Revenue (Metal Sale)
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagRevenue->id,
            'date' => '2026-05-10',
            'amount' => 15000.00,
        ]);

        // 2. Log Diesel Expense
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagDiesel->id,
            'date' => '2026-05-11',
            'amount' => 3000.00,
        ]);

        // 3. Log Electricity Expense
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagElectricity->id,
            'date' => '2026-05-12',
            'amount' => 4500.00,
        ]);

        // 4. Log Other Expense
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagOther->id,
            'date' => '2026-05-12',
            'amount' => 1500.00,
        ]);

        $result = $this->service->getProfitability($this->crusher->id, '2026-05-01', '2026-05-31');

        $this->assertEquals(15000.00, $result['metal_sales']);
        $this->assertEquals(3000.00, $result['diesel']);
        $this->assertEquals(4500.00, $result['electricity']);
        $this->assertEquals(1500.00, $result['other_expenses']);
        $this->assertEquals(9000.00, $result['total_expenses']);
        $this->assertEquals(6000.00, $result['profit']);
    }

    /**
     * Test caching and cache invalidation.
     */
    public function test_caching_and_invalidation_strategy(): void
    {
        // 1. Create a record
        $record = OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagRevenue->id,
            'date' => '2026-05-10',
            'amount' => 10000.00,
        ]);

        $cacheKey = "crusher:profit:{$this->crusher->id}:2026-05-01:2026-05-31";

        // Query once to load in cache
        $result1 = $this->service->getProfitability($this->crusher->id, '2026-05-01', '2026-05-31');
        $this->assertEquals(10000.00, $result1['metal_sales']);
        $this->assertTrue(Cache::has($cacheKey));

        // 2. Modify record (updates amount to 12000)
        $record->update(['amount' => 12000.00]);

        // Assert cache was cleared automatically by observer
        $this->assertFalse(Cache::has($cacheKey));

        // Query again, should retrieve updated value
        $result2 = $this->service->getProfitability($this->crusher->id, '2026-05-01', '2026-05-31');
        $this->assertEquals(12000.00, $result2['metal_sales']);
    }

    /**
     * Test monthly profit summaries for a year.
     */
    public function test_monthly_summary_aggregation_groups_correctly(): void
    {
        // Jan - 5000 Sales
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagRevenue->id,
            'date' => '2026-01-15',
            'amount' => 5000.00,
        ]);

        // Jan - 1000 Diesel
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagDiesel->id,
            'date' => '2026-01-20',
            'amount' => 1000.00,
        ]);

        // Feb - 8000 Sales
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagRevenue->id,
            'date' => '2026-02-10',
            'amount' => 8000.00,
        ]);

        $summary = $this->service->getMonthlyProfitSummary($this->crusher->id, 2026);

        $this->assertCount(12, $summary['monthly_breakdown']);

        // January (index 0)
        $this->assertEquals('01', $summary['monthly_breakdown'][0]['month']);
        $this->assertEquals(5000.00, $summary['monthly_breakdown'][0]['metal_sales']);
        $this->assertEquals(1000.00, $summary['monthly_breakdown'][0]['diesel']);
        $this->assertEquals(4000.00, $summary['monthly_breakdown'][0]['profit']);

        // February (index 1)
        $this->assertEquals('02', $summary['monthly_breakdown'][1]['month']);
        $this->assertEquals(8000.00, $summary['monthly_breakdown'][1]['metal_sales']);
        $this->assertEquals(8000.00, $summary['monthly_breakdown'][1]['profit']);

        // March (index 2) - should be 0
        $this->assertEquals('03', $summary['monthly_breakdown'][2]['month']);
        $this->assertEquals(0.00, $summary['monthly_breakdown'][2]['metal_sales']);
        $this->assertEquals(0.00, $summary['monthly_breakdown'][2]['profit']);
    }

    /**
     * Test the API endpoint validations and output structures.
     */
    public function test_api_endpoints_return_correct_response(): void
    {
        // Log basic record
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagRevenue->id,
            'date' => '2026-05-15',
            'amount' => 2000.00,
        ]);

        // 1. Test Profitability API
        $response = $this->actingAs($this->admin)
            ->getJson(route('api.crusher.profitability', [
                'unit' => $this->crusher->id,
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-31'
            ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'unit_id' => $this->crusher->id,
            'metal_sales' => 2000.00,
            'profit' => 2000.00,
        ]);

        // 2. Test Invalid parameters fail validation
        $invalidResponse = $this->actingAs($this->admin)
            ->getJson(route('api.crusher.profitability', [
                'unit' => $this->crusher->id,
                'start_date' => 'invalid-date',
                'end_date' => '2026-05-31'
            ]));

        $invalidResponse->assertStatus(422);

        // 3. Test Monthly Summary API
        $summaryResponse = $this->actingAs($this->admin)
            ->getJson(route('api.crusher.monthly-summary', [
                'unit' => $this->crusher->id,
                'year' => 2026
            ]));

        $summaryResponse->assertStatus(200);
        $summaryResponse->assertJsonStructure([
            'unit_id',
            'year',
            'monthly_breakdown' => [
                '*' => [
                    'month',
                    'month_name',
                    'metal_sales',
                    'diesel',
                    'electricity',
                    'other_expenses',
                    'total_expenses',
                    'profit',
                ]
            ]
        ]);
    }
}
