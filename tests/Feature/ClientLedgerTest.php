<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientTransaction;
use App\Models\GatePass;
use App\Models\MetalType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\OperationalUnit;
use App\Enums\GatePassStatus;
use App\Enums\ActivityType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientLedgerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $client;
    protected $vehicle;
    protected $unit;
    protected $metal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->client = Client::create([
            'name' => 'Ledger Client',
            'is_active' => true,
        ]);

        $this->vehicle = Vehicle::create([
            'registration_number' => 'TS09Q1234',
            'type' => 'Truck',
            'is_active' => true,
        ]);

        $this->unit = OperationalUnit::firstOrCreate(
            ['code' => 'CRS'],
            ['name' => 'Crusher Unit', 'is_active' => true]
        );

        $this->metal = MetalType::create([
            'name' => '40mm Metal',
            'is_active' => true,
        ]);
    }

    /**
     * Test show client page renders correctly and computes running balance properly.
     */
    public function test_client_ledger_computes_accurate_running_balance(): void
    {
        // 1. Transaction 1: Credit (Payment) - 5,000 (Advance)
        $tx1 = ClientTransaction::create([
            'client_id' => $this->client->id,
            'transaction_type' => 'credit',
            'amount' => 5000.00,
            'transaction_date' => '2026-05-01',
            'payment_mode' => \App\Enums\PaymentMode::CASH,
            'description' => 'Advance payment',
        ]);

        // 2. Transaction 2: Debit (Sale) - 2,000. Vehicle linked.
        $gp1 = GatePass::create([
            'gate_pass_number' => 'GP-101',
            'date' => '2026-05-02 10:00:00',
            'client_id' => $this->client->id,
            'vehicle_id' => $this->vehicle->id,
            'source_unit_id' => $this->unit->id,
            'metal_type_id' => $this->metal->id,
            'rate_per_ton' => 10,
            'loading_quantity' => 200,
            'total_amount' => 2000.00,
            'status' => GatePassStatus::COMPLETED,
            'activity_type' => ActivityType::SALES,
        ]);

        $tx2 = ClientTransaction::create([
            'client_id' => $this->client->id,
            'gate_pass_id' => $gp1->id,
            'transaction_type' => 'debit',
            'amount' => 2000.00,
            'transaction_date' => '2026-05-02',
            'description' => 'Gate pass sale',
        ]);

        // 3. Transaction 3: Debit (Sale) - 4,000.
        $gp2 = GatePass::create([
            'gate_pass_number' => 'GP-102',
            'date' => '2026-05-03 11:00:00',
            'client_id' => $this->client->id,
            'vehicle_id' => $this->vehicle->id,
            'source_unit_id' => $this->unit->id,
            'metal_type_id' => $this->metal->id,
            'rate_per_ton' => 20,
            'loading_quantity' => 200,
            'total_amount' => 4000.00,
            'status' => GatePassStatus::COMPLETED,
            'activity_type' => ActivityType::SALES,
        ]);

        $tx3 = ClientTransaction::create([
            'client_id' => $this->client->id,
            'gate_pass_id' => $gp2->id,
            'transaction_type' => 'debit',
            'amount' => 4000.00,
            'transaction_date' => '2026-05-03',
            'description' => 'Gate pass sale 2',
        ]);

        // Let's get the show page
        $response = $this->actingAs($this->admin)
            ->get(route('clients.show', $this->client));

        $response->assertStatus(200);
        $response->assertSee('Transaction Ledger');
        $response->assertSee('TS09Q1234'); // Vehicle registration number
        $response->assertSee('200.00'); // Quantity

        // Verify running balances from controller query output
        $txs = $response->viewData('transactions');
        $this->assertCount(3, $txs);

        // Sort is descending by transaction_date then ID
        // Top one is tx3 (2026-05-03)
        // tx1 balance: +5000 (Advance)
        // tx2 balance: +5000 - 2000 = +3000 (Advance)
        // tx3 balance: +3000 - 4000 = -1000 (Due)
        $this->assertEquals(-1000.00, $txs[0]->running_balance);
        $this->assertEquals(3000.00, $txs[1]->running_balance);
        $this->assertEquals(5000.00, $txs[2]->running_balance);
    }

    /**
     * Test ledger filtering by transaction type and vehicle.
     */
    public function test_client_ledger_filtering(): void
    {
        $gp = GatePass::create([
            'gate_pass_number' => 'GP-201',
            'date' => '2026-05-02 10:00:00',
            'client_id' => $this->client->id,
            'vehicle_id' => $this->vehicle->id,
            'source_unit_id' => $this->unit->id,
            'metal_type_id' => $this->metal->id,
            'rate_per_ton' => 10,
            'loading_quantity' => 100,
            'total_amount' => 1000.00,
            'status' => GatePassStatus::COMPLETED,
            'activity_type' => ActivityType::SALES,
        ]);

        $debitTx = ClientTransaction::create([
            'client_id' => $this->client->id,
            'gate_pass_id' => $gp->id,
            'transaction_type' => 'debit',
            'amount' => 1000.00,
            'transaction_date' => '2026-05-02',
            'description' => 'Sale transaction',
        ]);

        $creditTx = ClientTransaction::create([
            'client_id' => $this->client->id,
            'transaction_type' => 'credit',
            'amount' => 2000.00,
            'transaction_date' => '2026-05-05',
            'payment_mode' => \App\Enums\PaymentMode::CASH,
            'description' => 'Payment transaction',
        ]);

        // Filter by type: debit
        $response = $this->actingAs($this->admin)
            ->get(route('clients.show', [
                'client' => $this->client->id,
                'type' => 'debit',
            ]));

        $response->assertStatus(200);
        $txs = $response->viewData('transactions');
        $this->assertCount(1, $txs);
        $this->assertEquals('debit', $txs[0]->transaction_type);

        // Filter by vehicle_id
        $vehicle2 = Vehicle::create([
            'registration_number' => 'TS10AA1234',
            'type' => 'Dumper',
            'is_active' => true,
        ]);

        $response2 = $this->actingAs($this->admin)
            ->get(route('clients.show', [
                'client' => $this->client->id,
                'vehicle_id' => $vehicle2->id,
            ]));

        $response2->assertStatus(200);
        $txs2 = $response2->viewData('transactions');
        $this->assertCount(0, $txs2);
    }
}
