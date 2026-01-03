<?php

namespace App\Http\Controllers;

use App\Models\GatePass;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\MetalType;
use Illuminate\Http\Request;

class GatePassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gatePasses = GatePass::with(['client', 'vehicle', 'metalType', 'transaction'])
            ->when(request('search'), function ($query, $search) {
                $query->where('gate_pass_number', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('vehicle_number', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('gate_passes.index', compact('gatePasses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('is_active', true)->get();
        $vehicles = Vehicle::where('is_active', true)->get();
        $metalTypes = MetalType::where('is_active', true)->get();

        $gpNumber = 'GP-' . date('Ymd') . '-' . str_pad(GatePass::whereDate('date', now()->format('Y-m-d'))->count() + 1, 4, '0', STR_PAD_LEFT);

        return view('gate_passes.create', compact('clients', 'vehicles', 'metalTypes', 'gpNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $status = $request->input('status', 'pending');

        $rules = [
            'gate_pass_number' => 'required|unique:gate_passes',
            'date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'client_id' => 'required|exists:clients,id', // Based on use case, client is selected
            'status' => 'required|in:pending,completed,cancelled',
            'remarks' => 'nullable|string',
        ];

        // Conditional Validation
        if ($status === 'completed') {
            $rules['metal_type_id'] = 'required|exists:metal_types,id';
            $rules['driver_name'] = 'required|string|max:255';
            $rules['gross_weight'] = 'required|numeric|min:0';
            $rules['tare_weight'] = 'required|numeric|min:0';
            $rules['net_weight'] = 'required|numeric|min:0';
        } else {
            // For pending, these are optional
            $rules['metal_type_id'] = 'nullable|exists:metal_types,id';
            $rules['driver_name'] = 'nullable|string|max:255';
            $rules['gross_weight'] = 'nullable|numeric|min:0';
            $rules['tare_weight'] = 'nullable|numeric|min:0';
            $rules['net_weight'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Ensure defaults strictly if null
        $validated['gross_weight'] = $validated['gross_weight'] ?? 0;
        $validated['tare_weight'] = $validated['tare_weight'] ?? 0;
        $validated['net_weight'] = $validated['net_weight'] ?? 0;

        GatePass::create($validated);

        return redirect()->route('gate_passes.index')->with('success', 'Gate Pass created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GatePass $gatePass)
    {
        $clients = Client::where('is_active', true)->get();
        $vehicles = Vehicle::where('is_active', true)->get();
        $metalTypes = MetalType::all();

        return view('gate_passes.edit', compact('gatePass', 'clients', 'vehicles', 'metalTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GatePass $gatePass, \App\Services\SalesService $salesService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'client_id' => 'nullable|exists:clients,id',
            'metal_type_id' => 'required|exists:metal_types,id',
            'driver_name' => 'required|string|max:255',
            'gross_weight' => 'nullable|numeric|min:0',
            'tare_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'loading_quantity' => 'nullable|numeric|min:0',
            'rate_per_ton' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'diesel_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,completed,cancelled',
            'remarks' => 'nullable|string',
        ]);

        // Default values
        $validated['gross_weight'] = $validated['gross_weight'] ?? 0;
        $validated['tare_weight'] = $validated['tare_weight'] ?? 0;
        $validated['net_weight'] = $validated['net_weight'] ?? 0;
        $validated['loading_quantity'] = $validated['loading_quantity'] ?? 0;

        // Check if editing a completed pass
        if ($gatePass->status === 'completed' && !$gatePass->wasChanged('status')) {
            // If already completed and staying completed, this is an Edit.
            // Requirement: Mandatory audit log.
            // We'll require 'correction_reason' in comments or strict validation.
            // Simplest: Check if critical fields changed.
            // For now, just append "Edited by User on Date" to remarks.

            $validated['remarks'] = $validated['remarks'] . " [Edited on " . now()->toDateTimeString() . "]";
        }

        // Restrict Cancellation to Admin
        if ($validated['status'] === 'cancelled' && $gatePass->status !== 'cancelled' && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Only Admins can cancel a Gate Pass.');
        }

        $gatePass->update($validated);

        if ($gatePass->status === 'completed' && $gatePass->client_id) {
            $salesService->createOrUpdateTransaction($gatePass);
        } elseif ($gatePass->status === 'cancelled') {
            $salesService->cancelTransaction($gatePass);
        }

        return redirect()->route('gate_passes.index')->with('success', 'Gate Pass updated successfully.');
    }

    public function dailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Base Query
        $baseQuery = GatePass::whereDate('date', $date)->where('status', 'completed');

        // Overall Summary
        $summary = [
            'total_sales' => (clone $baseQuery)->sum('total_amount'),
            'total_diesel' => (clone $baseQuery)->sum('diesel_amount'),
            'total_paid' => (clone $baseQuery)->sum('paid_amount'),
            'total_loads' => (clone $baseQuery)->count(),
        ];
        $summary['outstanding'] = $summary['total_sales'] - $summary['total_paid'];

        // Metal-wise Breakdown
        $metalStats = GatePass::with('metalType')
            ->whereDate('date', $date)
            ->where('status', 'completed')
            ->select(
                'metal_type_id',
                \DB::raw('SUM(loading_quantity) as total_cft'),
                \DB::raw('SUM(net_weight) as total_tons'),
                \DB::raw('SUM(total_amount) as total_amount'),
                \DB::raw('COUNT(*) as count')
            )
            ->groupBy('metal_type_id')
            ->get();

        return view('gate_passes.daily_report', compact('summary', 'metalStats', 'date'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function recordPayment(Request $request, GatePass $gatePass, \App\Services\SalesService $salesService)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'payment_mode' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $salesService->recordPayment(
            $gatePass,
            $validated['amount'],
            $validated['date'],
            $validated['payment_mode'],
            $validated['remarks'] ?? ''
        );

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function destroy(GatePass $gatePass)
    {
        // Check if there is a transaction and maybe prevent delete or delete transaction?
        if ($gatePass->transaction) {
            $gatePass->transaction->delete();
        }

        $gatePass->delete();

        return redirect()->route('gate_passes.index')->with('success', 'Gate Pass deleted successfully.');
    }
}
