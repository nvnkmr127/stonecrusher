<?php

namespace Tests\Feature;

use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\OperationalUnit;
use App\Models\User;
use App\Services\Finance\ProfitLossService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProfitLossEngineTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $crusher;
    protected $quarry;
    protected $service;

    protected $tagSales;
    protected $tagDieselCrs;
    protected $tagDieselQry;
    protected $tagLabourCrs;
    protected $tagLabourQry;
    protected $tagElectricity;
    protected $tagBorewells;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->crusher = OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Plant', 'is_active' => true]
        );

        $this->quarry = OperationalUnit::firstOrCreate(
            ['code' => 'QRY'],
            ['name' => 'Quarry Plant', 'is_active' => true]
        );

        // Tags
        $this->tagSales = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Metal Sale',
            'type' => 'revenue',
        ]);

        $this->tagDieselCrs = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Diesel Used',
            'type' => 'expense',
        ]);

        $this->tagDieselQry = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->quarry->id,
            'name' => 'Diesel Used',
            'type' => 'expense',
        ]);

        $this->tagLabourCrs = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Labour',
            'type' => 'expense',
        ]);

        $this->tagLabourQry = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->quarry->id,
            'name' => 'Labour',
            'type' => 'expense',
        ]);

        $this->tagElectricity = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Electricity',
            'type' => 'expense',
        ]);

        $this->tagBorewells = OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->quarry->id,
            'name' => 'Borewells',
            'type' => 'expense',
        ]);

        $this->service = new ProfitLossService();
    }

    /**
     * Test profit and loss calculation logic.
     */
    public function test_profit_loss_calculation(): void
    {
        // 1. Sales (100,000)
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagSales->id,
            'date' => '2026-05-10',
            'amount' => 100000.00,
        ]);

        // 2. Diesel Crusher (15,000)
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagDieselCrs->id,
            'date' => '2026-05-11',
            'amount' => 15000.00,
        ]);

        // 3. Diesel Quarry (10,000)
        OperationalRecord::create([
            'operational_unit_id' => $this->quarry->id,
            'operational_tag_id' => $this->tagDieselQry->id,
            'date' => '2026-05-11',
            'amount' => 10000.00,
        ]);

        // 4. Labour Crusher (12,000)
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagLabourCrs->id,
            'date' => '2026-05-12',
            'amount' => 12000.00,
        ]);

        // 5. Labour Quarry (8,000)
        OperationalRecord::create([
            'operational_unit_id' => $this->quarry->id,
            'operational_tag_id' => $this->tagLabourQry->id,
            'date' => '2026-05-12',
            'amount' => 8000.00,
        ]);

        // 6. Crusher Expense (Electricity: 20,000)
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagElectricity->id,
            'date' => '2026-05-13',
            'amount' => 20000.00,
        ]);

        // 7. Quarry Expense (Borewells: 15,000)
        OperationalRecord::create([
            'operational_unit_id' => $this->quarry->id,
            'operational_tag_id' => $this->tagBorewells->id,
            'date' => '2026-05-14',
            'amount' => 15000.00,
        ]);

        $res = $this->service->getProfitLossBreakdown('2026-05-01', '2026-05-31');

        $this->assertEquals(100000.00, $res['sales']);
        $this->assertEquals(25000.00, $res['diesel']); // 15000 + 10000
        $this->assertEquals(20000.00, $res['labour']); // 12000 + 8000
        $this->assertEquals(20000.00, $res['crusher_expense']); // Electricity
        $this->assertEquals(15000.00, $res['quarry_expense']); // Borewells
        $this->assertEquals(20000.00, $res['net_profit']); // 100k - 25k - 20k - 20k - 15k = 20k
    }

    /**
     * Test caching and cache invalidation.
     */
    public function test_caching_and_invalidation(): void
    {
        $record = OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagSales->id,
            'date' => '2026-05-10',
            'amount' => 50000.00,
        ]);

        $cacheKey = "finance:profit_loss:breakdown:2026-05-01:2026-05-31";

        $res1 = $this->service->getProfitLossBreakdown('2026-05-01', '2026-05-31');
        $this->assertEquals(50000.00, $res1['sales']);
        $this->assertTrue(Cache::has($cacheKey));

        // Update record should clear cache via observer
        $record->update(['amount' => 60000.00]);
        $this->assertFalse(Cache::has($cacheKey));

        $res2 = $this->service->getProfitLossBreakdown('2026-05-01', '2026-05-31');
        $this->assertEquals(60000.00, $res2['sales']);
    }

    /**
     * Test monthly grouping and summaries.
     */
    public function test_monthly_summary_aggregation(): void
    {
        // Jan Sales
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagSales->id,
            'date' => '2026-01-15',
            'amount' => 40000.00,
        ]);

        // Feb Sales
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagSales->id,
            'date' => '2026-02-15',
            'amount' => 50000.00,
        ]);

        $res = $this->service->getMonthlyProfitLossSummary(2026);

        $this->assertCount(12, $res['monthly_breakdown']);
        $this->assertEquals('01', $res['monthly_breakdown'][0]['month']);
        $this->assertEquals(40000.00, $res['monthly_breakdown'][0]['sales']);
        $this->assertEquals('02', $res['monthly_breakdown'][1]['month']);
        $this->assertEquals(50000.00, $res['monthly_breakdown'][1]['sales']);
    }

    /**
     * Test API Endpoints and authorization.
     */
    public function test_api_endpoints_return_correct_response(): void
    {
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagSales->id,
            'date' => '2026-05-10',
            'amount' => 30000.00,
        ]);

        // 1. Get Profit Loss Breakdown JSON
        $response = $this->actingAs($this->admin)
            ->getJson(route('api.finance.profit-loss', [
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-31',
            ]));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'sales' => '30000.00',
            'net_profit' => '30000.00',
        ]);

        // 2. Get Monthly summary
        $monthlyResponse = $this->actingAs($this->admin)
            ->getJson(route('api.finance.profit-loss.monthly', [
                'year' => 2026,
            ]));

        $monthlyResponse->assertStatus(200);
        $monthlyResponse->assertJsonStructure([
            'year',
            'monthly_breakdown' => [
                '*' => [
                    'month',
                    'month_name',
                    'sales',
                    'crusher_expense',
                    'quarry_expense',
                    'labour',
                    'diesel',
                    'other_expense',
                    'net_profit'
                ]
            ]
        ]);
    }

    /**
     * Test Export endpoints.
     */
    public function test_export_endpoints(): void
    {
        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $this->tagSales->id,
            'date' => '2026-05-10',
            'amount' => 30000.00,
        ]);

        // 1. CSV Range Export
        $csvRangeResponse = $this->actingAs($this->admin)
            ->get(route('api.finance.profit-loss.export', [
                'type' => 'range',
                'format' => 'csv',
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-31',
            ]));

        $csvRangeResponse->assertStatus(200);
        $csvRangeResponse->assertHeader('Content-Disposition', 'attachment; filename=profit_loss_report_2026-05-01_2026-05-31.csv');

        // 2. CSV Monthly Export
        $csvMonthlyResponse = $this->actingAs($this->admin)
            ->get(route('api.finance.profit-loss.export', [
                'type' => 'monthly',
                'format' => 'csv',
                'year' => 2026,
            ]));

        $csvMonthlyResponse->assertStatus(200);
        $csvMonthlyResponse->assertHeader('Content-Disposition', 'attachment; filename=profit_loss_monthly_report_2026.csv');

        // 3. PDF Range Export
        $pdfRangeResponse = $this->actingAs($this->admin)
            ->get(route('api.finance.profit-loss.export', [
                'type' => 'range',
                'format' => 'pdf',
                'start_date' => '2026-05-01',
                'end_date' => '2026-05-31',
            ]));

        $pdfRangeResponse->assertStatus(200);
        $pdfRangeResponse->assertHeader('Content-Type', 'application/pdf');

        // 4. PDF Monthly Export
        $pdfMonthlyResponse = $this->actingAs($this->admin)
            ->get(route('api.finance.profit-loss.export', [
                'type' => 'monthly',
                'format' => 'pdf',
                'year' => 2026,
            ]));

        $pdfMonthlyResponse->assertStatus(200);
        $pdfMonthlyResponse->assertHeader('Content-Type', 'application/pdf');
    }
}
