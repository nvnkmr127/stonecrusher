<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\DieselEntry;
use App\Models\GatePass;
use App\Models\MetalType;
use App\Models\OperationalRecord;
use App\Models\OperationalTag;
use App\Models\OperationalUnit;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\DailyClosing;
use App\Enums\GatePassStatus;
use App\Enums\ActivityType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Carbon\Carbon;

class OperationalSyncTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected $admin;
    protected $quarry;
    protected $crusher;
    protected $vehicle;
    protected $client;
    protected $metalType;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'admin']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->quarry = OperationalUnit::firstOrCreate(
            ['code' => 'QRY'],
            ['name' => 'Quarry Unit', 'is_active' => true]
        );

        $this->crusher = OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Unit', 'is_active' => true]
        );

        $this->vehicle = Vehicle::create([
            'registration_number' => 'TS01AB1234',
            'type' => 'Truck',
            'model' => 'Tata',
            'is_active' => true
        ]);

        $this->client = Client::create([
            'name' => 'Test Client',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'is_active' => true
        ]);

        $this->metalType = MetalType::create([
            'name' => 'Test Metal',
            'unit_price' => 100.00
        ]);

        Setting::set('default_diesel_rate', 100.00);
    }

    public function test_creating_diesel_entry_creates_operational_record()
    {
        $dieselEntry = DieselEntry::create([
            'date' => '2026-05-22',
            'vehicle_id' => $this->vehicle->id,
            'operational_unit_id' => $this->quarry->id,
            'liters' => 50,
            'work_type' => 'Excavation',
            'driver_name' => 'John Doe'
        ]);

        // Assert OperationalRecord was created
        $this->assertDatabaseHas('operational_records', [
            'diesel_entry_id' => $dieselEntry->id,
            'operational_unit_id' => $this->quarry->id,
            'quantity' => 50.00,
            'rate' => 100.00,
            'amount' => 5000.00
        ]);

        $record = OperationalRecord::where('diesel_entry_id', $dieselEntry->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals('2026-05-22', $record->date->format('Y-m-d'));

        // Assert tag was created/resolved
        $this->assertDatabaseHas('operational_tags', [
            'operational_unit_id' => $this->quarry->id,
            'name' => 'Diesel Used',
            'type' => 'expense'
        ]);
    }

    public function test_updating_diesel_entry_updates_operational_record()
    {
        $dieselEntry = DieselEntry::create([
            'date' => '2026-05-22',
            'vehicle_id' => $this->vehicle->id,
            'operational_unit_id' => $this->quarry->id,
            'liters' => 50,
            'work_type' => 'Excavation',
            'driver_name' => 'John Doe'
        ]);

        $dieselEntry->update([
            'liters' => 75,
            'date' => '2026-05-23'
        ]);

        $this->assertDatabaseHas('operational_records', [
            'diesel_entry_id' => $dieselEntry->id,
            'quantity' => 75.00,
            'amount' => 7500.00,
        ]);

        $record = OperationalRecord::where('diesel_entry_id', $dieselEntry->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals('2026-05-23', $record->date->format('Y-m-d'));
    }

    public function test_deleting_diesel_entry_deletes_operational_record()
    {
        $dieselEntry = DieselEntry::create([
            'date' => '2026-05-22',
            'vehicle_id' => $this->vehicle->id,
            'operational_unit_id' => $this->quarry->id,
            'liters' => 50,
            'work_type' => 'Excavation',
            'driver_name' => 'John Doe'
        ]);

        $this->assertDatabaseHas('operational_records', [
            'diesel_entry_id' => $dieselEntry->id
        ]);

        $dieselEntry->delete();

        $this->assertDatabaseMissing('operational_records', [
            'diesel_entry_id' => $dieselEntry->id
        ]);
    }

    public function test_creating_completed_gate_pass_sale_creates_operational_record()
    {
        $gatePass = GatePass::create([
            'gate_pass_number' => 'GP-20260522-0001',
            'date' => '2026-05-22',
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'activity_type' => ActivityType::SALES->value,
            'trips' => 1,
            'net_weight' => 10,
            'loading_quantity' => 10,
            'metal_type_id' => $this->metalType->id,
            'rate_per_ton' => 150.00,
            'total_amount' => 1500.00,
            'status' => GatePassStatus::COMPLETED->value
        ]);

        $this->assertDatabaseHas('operational_records', [
            'gate_pass_id' => $gatePass->id,
            'operational_unit_id' => $this->crusher->id,
            'quantity' => 10.00,
            'rate' => 150.00,
            'amount' => 1500.00
        ]);

        $record = OperationalRecord::where('gate_pass_id', $gatePass->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals('2026-05-22', $record->date->format('Y-m-d'));

        $this->assertDatabaseHas('operational_tags', [
            'operational_unit_id' => $this->crusher->id,
            'name' => 'Metal Sale',
            'type' => 'revenue'
        ]);
    }

    public function test_gate_pass_status_or_type_change_deletes_operational_record()
    {
        $gatePass = GatePass::create([
            'gate_pass_number' => 'GP-20260522-0001',
            'date' => '2026-05-22',
            'vehicle_id' => $this->vehicle->id,
            'client_id' => $this->client->id,
            'source_unit_id' => $this->crusher->id,
            'destination_unit_id' => $this->quarry->id,
            'activity_type' => ActivityType::SALES->value,
            'trips' => 1,
            'net_weight' => 10,
            'loading_quantity' => 10,
            'metal_type_id' => $this->metalType->id,
            'rate_per_ton' => 150.00,
            'total_amount' => 1500.00,
            'status' => GatePassStatus::COMPLETED->value
        ]);

        $this->assertDatabaseHas('operational_records', [
            'gate_pass_id' => $gatePass->id
        ]);

        $gatePass->update([
            'status' => GatePassStatus::PENDING->value
        ]);

        $this->assertDatabaseMissing('operational_records', [
            'gate_pass_id' => $gatePass->id
        ]);
    }

    public function test_gate_pass_on_closed_day_throws_exception()
    {
        // Close day 2026-05-22
        DailyClosing::create([
            'date' => '2026-05-22',
            'status' => 'closed',
            'total_sales' => 0,
            'total_cash' => 0,
            'total_expenses' => 0,
            'closed_by_user_id' => $this->admin->id
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        DieselEntry::create([
            'date' => '2026-05-22',
            'vehicle_id' => $this->vehicle->id,
            'operational_unit_id' => $this->quarry->id,
            'liters' => 50,
            'work_type' => 'Excavation',
            'driver_name' => 'John Doe'
        ]);
    }
}
