<?php
 
namespace Tests\Feature;
 
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\OperationalUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
 
class AdminDashboardExpansionTest extends TestCase
{
    use RefreshDatabase;
 
    public function test_admin_dashboard_renders_profitability_widgets_and_charts(): void
    {
        Role::create(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
 
        $quarry = OperationalUnit::where('code', 'QRY')->first();
        $crusher = OperationalUnit::where('code', 'CRS')->first();
 
        $salesTag = OperationalTag::where('operational_unit_id', $crusher->id)->where('name', 'Metal Sale')->first() 
            ?? OperationalTag::create(['operational_unit_id' => $crusher->id, 'name' => 'Metal Sale', 'type' => 'revenue']);
        $dieselTag = OperationalTag::where('operational_unit_id', $crusher->id)->where('name', 'Diesel Used')->first()
            ?? OperationalTag::create(['operational_unit_id' => $crusher->id, 'name' => 'Diesel Used', 'type' => 'expense']);
        $labourTag = OperationalTag::firstOrCreate(['operational_unit_id' => $quarry->id, 'name' => 'Labour', 'type' => 'expense']);
 
        OperationalRecord::create([
            'operational_unit_id' => $crusher->id,
            'operational_tag_id' => $salesTag->id,
            'date' => now()->startOfMonth()->addDays(1)->toDateString(),
            'amount' => 10000,
        ]);
        OperationalRecord::create([
            'operational_unit_id' => $crusher->id,
            'operational_tag_id' => $dieselTag->id,
            'date' => now()->startOfMonth()->addDays(2)->toDateString(),
            'amount' => 2500,
        ]);
        OperationalRecord::create([
            'operational_unit_id' => $quarry->id,
            'operational_tag_id' => $labourTag->id,
            'date' => now()->startOfMonth()->addDays(2)->toDateString(),
            'amount' => 1500,
        ]);
 
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Crusher Profit');
        $response->assertSee('Quarry Expense');
        $response->assertSee('Net Profit');
        $response->assertSee('Monthly P&L', false);
    }
}
