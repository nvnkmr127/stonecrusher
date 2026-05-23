<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\OperationalUnit;
use App\Models\OperationalTag;
use App\Models\OperationalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;
use Carbon\Carbon;

class OperationalRecordsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $quarry;
    protected $crusher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create roles
        Role::create(['name' => 'admin']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Create Operational Units if not exists
        $this->quarry = OperationalUnit::firstOrCreate(
            ['code' => 'QRY'],
            ['name' => 'Quarry Unit']
        );

        $this->crusher = OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Unit']
        );

        // Seed tags since refresh database wipes them
        OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->quarry->id,
            'name' => 'Borewells',
            'type' => 'expense'
        ]);

        OperationalTag::firstOrCreate([
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Metal Sale',
            'type' => 'revenue'
        ]);
    }

    public function test_can_access_quarry_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('quarry.index'));

        $response->assertStatus(200);
        $response->assertSee('Quarry Operations');
        $response->assertSee('Borewells');
    }

    public function test_can_access_crusher_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('crusher.index'));

        $response->assertStatus(200);
        $response->assertSee('Crusher Operations');
        $response->assertSee('Metal Sale');
    }

    public function test_can_create_custom_tag()
    {
        $response = $this->actingAs($this->admin)->post(route('operations.tags.store', $this->quarry), [
            'name' => 'Subcontractor A',
            'type' => 'expense'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('operational_tags', [
            'operational_unit_id' => $this->quarry->id,
            'name' => 'Subcontractor A',
            'type' => 'expense'
        ]);
    }

    public function test_can_create_operational_record()
    {
        $tag = OperationalTag::where('operational_unit_id', $this->quarry->id)->first();

        $response = $this->actingAs($this->admin)->post(route('operations.records.store', $this->quarry), [
            'date' => Carbon::now()->format('Y-m-d'),
            'operational_tag_id' => $tag->id,
            'quantity' => 10,
            'rate' => 150.50,
            'amount' => 1505.00,
            'remarks' => 'Test Quarry Record'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('operational_records', [
            'operational_unit_id' => $this->quarry->id,
            'operational_tag_id' => $tag->id,
            'amount' => 1505.00,
            'remarks' => 'Test Quarry Record'
        ]);
    }

    public function test_can_update_operational_record()
    {
        $tag = OperationalTag::where('operational_unit_id', $this->quarry->id)->first();
        $record = OperationalRecord::create([
            'operational_unit_id' => $this->quarry->id,
            'operational_tag_id' => $tag->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'amount' => 500.00,
            'remarks' => 'Initial'
        ]);

        $response = $this->actingAs($this->admin)->put(route('operations.records.update', $record), [
            'date' => Carbon::now()->format('Y-m-d'),
            'operational_tag_id' => $tag->id,
            'amount' => 1200.00,
            'remarks' => 'Updated Remarks'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('operational_records', [
            'id' => $record->id,
            'amount' => 1200.00,
            'remarks' => 'Updated Remarks'
        ]);
    }

    public function test_can_delete_operational_record()
    {
        $tag = OperationalTag::where('operational_unit_id', $this->quarry->id)->first();
        $record = OperationalRecord::create([
            'operational_unit_id' => $this->quarry->id,
            'operational_tag_id' => $tag->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'amount' => 500.00
        ]);

        $response = $this->actingAs($this->admin)->delete(route('operations.records.destroy', $record));

        $response->assertRedirect();
        $this->assertDatabaseMissing('operational_records', [
            'id' => $record->id
        ]);
    }

    public function test_quarry_and_crusher_p_and_l_report()
    {
        $qTag = OperationalTag::where('operational_unit_id', $this->quarry->id)->where('type', 'expense')->first();
        $cTag = OperationalTag::where('operational_unit_id', $this->crusher->id)->where('type', 'revenue')->first();

        // Create records for the current month
        OperationalRecord::create([
            'operational_unit_id' => $this->quarry->id,
            'operational_tag_id' => $qTag->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'amount' => 2000.00
        ]);

        OperationalRecord::create([
            'operational_unit_id' => $this->crusher->id,
            'operational_tag_id' => $cTag->id,
            'date' => Carbon::now()->format('Y-m-d'),
            'amount' => 5000.00
        ]);

        $response = $this->actingAs($this->admin)->get(route('reports.operational-profit-loss', [
            'month' => Carbon::now()->month,
            'year' => Carbon::now()->year
        ]));

        $response->assertStatus(200);
        $response->assertSee('Operational Profit & Loss', false);
        $response->assertSee('Total Quarry Expense');
        $response->assertSee('Total Crusher Revenue');
        // Consolidated net is 5000 (Crusher Revenue) - 2000 (Quarry Expense) = 3000
        $response->assertSee('3,000.00');
    }
}
