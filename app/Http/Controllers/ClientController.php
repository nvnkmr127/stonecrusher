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
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Global Stats (unfiltered for overall portfolio health)
        $total_clients = Client::count();
        $active_clients = Client::where('is_active', true)->count();

        // Calculate global financial summary (Portfolio wide)
        $financials = Client::withSum(['transactions as total_credit' => function ($query) {
            $query->where('transaction_type', 'credit');
        }], 'amount')
        ->withSum(['transactions as total_debit' => function ($query) {
            $query->where('transaction_type', 'debit');
        }], 'amount')
        ->get(['id']); // Only need IDs to fetch relations

        $total_receivable = 0;
        $total_advance = 0;

        foreach ($financials as $f) {
            $balance = ($f->total_debit ?? 0) - ($f->total_credit ?? 0);
            if ($balance > 0) {
                $total_receivable += $balance;
            } elseif ($balance < 0) {
                $total_advance += abs($balance);
            }
        }

        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $clients = (clone $query)->withSum(['transactions as total_credit' => function ($query) {
            $query->where('transaction_type', 'credit');
        }], 'amount')
        ->withSum(['transactions as total_debit' => function ($query) {
            $query->where('transaction_type', 'debit');
        }], 'amount')
        ->withSum(['gatePasses as current_month_bill' => function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->whereBetween('date', [$currentMonthStart, $currentMonthEnd]);
        }], 'total_amount')
        ->withSum(['transactions as current_month_paid' => function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->where('transaction_type', 'credit')
                ->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd]);
        }], 'amount')
        ->latest()
        ->paginate(15)
        ->withQueryString();

        $summary = [
            'total' => $total_clients,
            'active' => $active_clients,
            'receivable' => $total_receivable,
            'advance' => $total_advance,
        ];

        return view('clients.index', compact('clients', 'summary'));
    }

    public function show(Request $request, Client $client)
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $startOfMonth = \Carbon\Carbon::parse($selectedMonth)->startOfMonth();
        $endOfMonth = \Carbon\Carbon::parse($selectedMonth)->endOfMonth();

        // Transaction Query
        $query = $client->transactions()
            ->with(['gatePass.vehicle'])
            ->select('client_transactions.*')
            ->selectRaw("
                (
                    SELECT COALESCE(SUM(CASE WHEN ct2.transaction_type = 'credit' THEN ct2.amount ELSE -ct2.amount END), 0)
                    FROM client_transactions as ct2
                    WHERE ct2.client_id = client_transactions.client_id
                      AND (
                        ct2.transaction_date < client_transactions.transaction_date
                        OR (
                          ct2.transaction_date = client_transactions.transaction_date 
                          AND ct2.id <= client_transactions.id
                        )
                      )
                ) as running_balance
            ");

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('type')) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->filled('vehicle_id')) {
            $query->whereHas('gatePass', function ($q) use ($request) {
                $q->where('vehicle_id', $request->vehicle_id);
            });
        }

        $transactions = $query->paginate(20, ['*'], 'tx_page')->withQueryString();

        // Gate Pass Query
        $gatePasses = $client->gatePasses()
            ->with(['vehicle', 'metalType'])
            ->latest('date')
            ->paginate(15, ['*'], 'gp_page')
            ->withQueryString();

        // Overall Stats
        $totalTrips = $client->gatePasses()->count();
        $totalCft = $client->gatePasses()->sum('net_weight');

        // Monthly Stats (Filtered Month)
        $monthlyStats = [
            'trips' => $client->gatePasses()->whereBetween('date', [$startOfMonth, $endOfMonth])->count(),
            'quantity' => $client->gatePasses()->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('net_weight'),
            'bill' => $client->gatePasses()->whereBetween('date', [$startOfMonth, $endOfMonth])->sum('total_amount'),
            'paid' => $client->transactions()->where('transaction_type', 'credit')->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('amount'),
        ];

        // Current Month Stats (Static)
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $thisMonthStats = [
            'trips' => $client->gatePasses()->whereBetween('date', [$currentMonthStart, $currentMonthEnd])->count(),
            'quantity' => $client->gatePasses()->whereBetween('date', [$currentMonthStart, $currentMonthEnd])->sum('net_weight'),
            'bill' => $client->gatePasses()->whereBetween('date', [$currentMonthStart, $currentMonthEnd])->sum('total_amount'),
            'paid' => $client->transactions()->where('transaction_type', 'credit')->whereBetween('transaction_date', [$currentMonthStart, $currentMonthEnd])->sum('amount'),
        ];

        // Month List for Filter
        $monthList = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthList[$date->format('Y-m')] = $date->format('F Y');
        }

        $vehicles = \App\Models\Vehicle::orderBy('registration_number')->get();

        return view('clients.show', compact('client', 'transactions', 'gatePasses', 'totalTrips', 'totalCft', 'monthlyStats', 'thisMonthStats', 'selectedMonth', 'monthList', 'vehicles'));
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
