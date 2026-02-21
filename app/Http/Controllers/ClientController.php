<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;


class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $clients = $query->withSum([
            'transactions as total_credit' => function ($query) {
                $query->where('transaction_type', 'credit');
            }
        ], 'amount')
            ->withSum([
                'transactions as total_debit' => function ($query) {
                    $query->where('transaction_type', 'debit');
                }
            ], 'amount')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function show(Request $request, Client $client)
    {
        $query = $client->transactions();

        if ($request->has(['start_date', 'end_date']) && $request->start_date && $request->end_date) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->latest('transaction_date')->paginate(20, ['*'], 'tx_page')->withQueryString();

        $gatePasses = $client->gatePasses()
            ->with(['vehicle', 'metalType'])
            ->latest('date')
            ->paginate(15, ['*'], 'gp_page');

        $totalTrips = $client->gatePasses()->count();
        $totalCft = $client->gatePasses()->sum('net_weight');

        return view('clients.show', compact('client', 'transactions', 'gatePasses', 'totalTrips', 'totalCft'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clients,name',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $client = Client::create($validated);



        return redirect()->route('clients.index')->with('success', 'Client created successfully!');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:clients,name,' . $client->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $client->update($validated);



        return redirect()->route('clients.index')->with('success', 'Client updated successfully!');
    }

    public function destroy(Client $client)
    {
        $name = $client->name;
        $client->delete();



        return redirect()->route('clients.index')->with('success', 'Client deleted successfully!');
    }
}
