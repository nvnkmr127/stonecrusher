<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientTransaction;
use Illuminate\Http\Request;


class ClientTransactionController extends Controller
{
    public function create(Client $client)
    {
        $availableCredit = $client->credit_limit > 0 ? $client->credit_limit + $client->balance : 0;
        return view('clients.transactions.create', compact('client', 'availableCredit'));
    }

    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'transaction_type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => ['nullable', \Illuminate\Validation\Rule::enum(\App\Enums\PaymentMode::class)],
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string',
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['transaction_date']);

        $client->transactions()->create($validated);

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
            'payment_mode' => ['nullable', \Illuminate\Validation\Rule::enum(\App\Enums\PaymentMode::class)],
            'reference_number' => 'nullable|string',
            'description' => 'nullable|string',
            'edit_reason' => 'required|string|min:5', // Mandatory reason
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['transaction_date']);
        \App\Services\DayClosureService::checkAllowed($transaction->transaction_date);

        $transaction->update([
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'payment_mode' => $validated['payment_mode'],
            'reference_number' => $validated['reference_number'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('clients.show', $client)->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Client $client, ClientTransaction $transaction)
    {
        \App\Services\DayClosureService::checkAllowed($transaction->transaction_date);

        $transaction->delete();

        return redirect()->route('clients.show', $client)->with('success', 'Transaction deleted successfully. Balance updated.');
    }
}
