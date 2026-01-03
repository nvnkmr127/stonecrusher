<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientTransaction;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ClientTransactionController extends Controller
{
    public function create(Client $client)
    {
        return view('clients.transactions.create', compact('client'));
    }

    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|in:Cash,Bank Transfer,UPI,Check,Other',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string',
        ]);

        $transaction = $client->transactions()->create($validated);

        // Log Activity
        ActivityLog::log(
            auth()->id(),
            'client_transaction',
            "Recorded {$validated['transaction_type']} of {$validated['amount']} for Client: {$client->name}. Description: {$validated['description']}"
        );

        return redirect()->route('clients.show', $client)->with('success', 'Transaction recorded successfully!');
    }

    public function edit(Client $client, ClientTransaction $transaction)
    {
        return view('clients.transactions.edit', compact('client', 'transaction'));
    }

    public function update(Request $request, Client $client, ClientTransaction $transaction)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'payment_mode' => 'nullable|string|in:Cash,Bank Transfer,UPI,Check,Other',
            'reference_number' => 'nullable|string',
            'description' => 'nullable|string',
            'edit_reason' => 'required|string|min:5', // Mandatory reason
        ]);

        $oldAmount = $transaction->amount;

        $transaction->update([
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'payment_mode' => $validated['payment_mode'],
            'reference_number' => $validated['reference_number'],
            'description' => $validated['description'],
        ]);

        // Log Activity
        ActivityLog::log(
            auth()->id(),
            'client_transaction_update',
            "Updated Transaction #{$transaction->id} for {$client->name}. Old Amount: {$oldAmount}, New Amount: {$validated['amount']}. Reason: {$validated['edit_reason']}"
        );

        return redirect()->route('clients.show', $client)->with('success', 'Transaction updated successfully!');
    }
}
