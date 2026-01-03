<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_advance_increases_client_balance()
    {
        $client = \App\Models\Client::create([
            'name' => 'Test Client',
            'is_active' => true,
        ]);

        // Add Advance (Credit)
        $client->transactions()->create([
            'transaction_type' => 'credit',
            'amount' => 1000,
            'transaction_date' => now(),
            'payment_mode' => 'Cash',
        ]);

        $this->assertEquals(1000, $client->refresh()->balance);
    }

    public function test_sale_decreases_client_balance()
    {
        $client = \App\Models\Client::create([
            'name' => 'Test Client',
            'is_active' => true,
        ]);

        // Initial Balance 0
        $this->assertEquals(0, $client->balance);

        // Add Sale (Debit)
        $client->transactions()->create([
            'transaction_type' => 'debit',
            'amount' => 500,
            'transaction_date' => now(),
        ]);

        // Balance should be -500 (Due)
        $this->assertEquals(-500, $client->refresh()->balance);
    }

    public function test_advance_is_applied_to_sale_correctly()
    {
        $client = \App\Models\Client::create([
            'name' => 'Test Client',
            'is_active' => true,
        ]);

        // 1. Advance of 2000
        $client->transactions()->create([
            'transaction_type' => 'credit',
            'amount' => 2000,
            'transaction_date' => now(),
        ]);

        $this->assertEquals(2000, $client->refresh()->balance);

        // 2. Sale of 500 (Partial Apply)
        $client->transactions()->create([
            'transaction_type' => 'debit',
            'amount' => 500,
            'transaction_date' => now(),
        ]);

        // Remaining Balance should be 1500
        $this->assertEquals(1500, $client->refresh()->balance);

        // 3. Sale of 2000 (Exceeds remaining advance)
        $client->transactions()->create([
            'transaction_type' => 'debit',
            'amount' => 2000,
            'transaction_date' => now(),
        ]);

        // Balance should be 1500 - 2000 = -500 (Due)
        $this->assertEquals(-500, $client->refresh()->balance);
    }
}
