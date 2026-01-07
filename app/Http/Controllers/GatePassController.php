<?php

namespace App\Http\Controllers;

use App\Models\GatePass;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\MetalType;
use App\Models\Setting;
use App\Models\DeliveryDestination;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $vehicles = Vehicle::getCached();
        $metalTypes = MetalType::getCached();

        $gpNumber = 'GP-' . date('Ymd') . '-' . str_pad(GatePass::whereDate('date', now()->format('Y-m-d'))->count() + 1, 4, '0', STR_PAD_LEFT);

        $transportRate = Setting::get('rate_per_km', 0);
        $crusherLat = Setting::get('crusher_latitude', 0);
        $crusherLon = Setting::get('crusher_longitude', 0);
        $defaultRoundTrip = (bool) Setting::get('default_round_trip', false);
        $destinations = DeliveryDestination::getCached();

        return view('gate_passes.create', compact('clients', 'vehicles', 'metalTypes', 'gpNumber', 'transportRate', 'crusherLat', 'crusherLon', 'destinations', 'defaultRoundTrip'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, \App\Services\SalesService $salesService)
    {
        $status = $request->input('status', \App\Enums\GatePassStatus::PENDING->value);

        $rules = [
            'gate_pass_number' => 'required|unique:gate_passes',
            'date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'client_id' => 'required|exists:clients,id',
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\GatePassStatus::class)],
            'remarks' => 'nullable|string',
            'delivery_location' => 'nullable|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'transport_is_billable' => 'nullable|boolean',
        ];

        // Conditional Validation
        if ($status === \App\Enums\GatePassStatus::COMPLETED->value) {
            $rules['metal_type_id'] = 'required|exists:metal_types,id';
            $rules['driver_name'] = 'required|string|max:255';
            $rules['gross_weight'] = 'required|numeric|min:0';
            $rules['tare_weight'] = 'required|numeric|min:0';
            $rules['net_weight'] = 'required|numeric|min:0';
            $rules['total_amount'] = 'required|numeric|min:0';
        } else {
            // For pending, these are optional
            $rules['metal_type_id'] = 'nullable|exists:metal_types,id';
            $rules['driver_name'] = 'nullable|string|max:255';
            $rules['gross_weight'] = 'nullable|numeric|min:0';
            $rules['tare_weight'] = 'nullable|numeric|min:0';
            $rules['net_weight'] = 'nullable|numeric|min:0';
            $rules['total_amount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // Check if date is closed
        \App\Services\DayClosureService::checkAllowed($validated['date']);

        // Ensure defaults strictly if null
        $validated['gross_weight'] = $validated['gross_weight'] ?? 0;
        $validated['tare_weight'] = $validated['tare_weight'] ?? 0;
        $validated['net_weight'] = $validated['net_weight'] ?? 0;

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request, $salesService) {
            $gatePass = GatePass::create($validated);

            if ($gatePass->status === \App\Enums\GatePassStatus::COMPLETED && $gatePass->client_id) {
                $salesService->createOrUpdateTransaction($gatePass);
            }

            if ($request->boolean('save_location') && !empty($validated['delivery_location'])) {
                DeliveryDestination::firstOrCreate(
                    ['name' => $validated['delivery_location']],
                    [
                        'latitude' => $request->input('dest_lat'),
                        'longitude' => $request->input('dest_lon'),
                        'distance_km' => $validated['distance_km'] ?? 0
                    ]
                );
            }

            return redirect()->route('gate-passes.index')->with('success', 'Gate Pass created successfully.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(GatePass $gate_pass)
    {
        return redirect()->route('gate-passes.edit', $gate_pass);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GatePass $gate_pass)
    {
        $clients = Client::where('is_active', true)->get();
        $vehicles = Vehicle::where('is_active', true)->get();
        $metalTypes = MetalType::all();
        $transportRate = Setting::get('rate_per_km', 0);
        $crusherLat = Setting::get('crusher_latitude', 0);
        $crusherLon = Setting::get('crusher_longitude', 0);
        $destinations = DeliveryDestination::orderBy('name')->get();

        return view('gate_passes.edit', compact('gate_pass', 'clients', 'vehicles', 'metalTypes', 'transportRate', 'crusherLat', 'crusherLon', 'destinations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, \App\Services\SalesService $salesService)
    {
        $gate_pass = GatePass::findOrFail($id);

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
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\GatePassStatus::class)],
            'remarks' => 'nullable|string',
            'delivery_location' => 'nullable|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'transport_is_billable' => 'nullable|boolean',
        ]);

        // Check if new date is closed
        \App\Services\DayClosureService::checkAllowed($validated['date']);
        // Check if original date was closed (prevent editing closed records)
        \App\Services\DayClosureService::checkAllowed($gate_pass->date);

        // Default values
        $validated['gross_weight'] = $validated['gross_weight'] ?? 0;
        $validated['tare_weight'] = $validated['tare_weight'] ?? 0;
        $validated['net_weight'] = $validated['net_weight'] ?? 0;
        $validated['loading_quantity'] = $validated['loading_quantity'] ?? 0;

        // Check if editing a completed pass
        if ($gate_pass->status === \App\Enums\GatePassStatus::COMPLETED->value && !$gate_pass->wasChanged('status')) {
            $validated['remarks'] = $validated['remarks'] . " [Edited on " . now()->toDateTimeString() . "]";
        }

        // Restrict Cancellation to Admin
        if ($validated['status'] === \App\Enums\GatePassStatus::CANCELLED->value && $gate_pass->status !== \App\Enums\GatePassStatus::CANCELLED->value && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Only Admins can cancel a Gate Pass.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($gate_pass, $validated, $salesService, $request) {
            $gate_pass->update($validated);

            if ($gate_pass->status === \App\Enums\GatePassStatus::COMPLETED->value && $gate_pass->client_id) {
                $salesService->createOrUpdateTransaction($gate_pass);
            } elseif ($gate_pass->status === \App\Enums\GatePassStatus::CANCELLED->value) {
                $salesService->cancelTransaction($gate_pass);
            }

            if ($request->boolean('save_location') && !empty($validated['delivery_location'])) {
                DeliveryDestination::firstOrCreate(
                    ['name' => $validated['delivery_location']],
                    [
                        'latitude' => $request->input('dest_lat'),
                        'longitude' => $request->input('dest_lon'),
                        'distance_km' => $validated['distance_km'] ?? 0
                    ]
                );
            }

            return redirect()->route('gate-passes.index')->with('success', 'Gate Pass updated successfully.');
        });
    }

    public function dailyReport(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        // Base Query
        $baseQuery = GatePass::whereDate('date', $date)->where('status', \App\Enums\GatePassStatus::COMPLETED->value);

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
            ->where('status', \App\Enums\GatePassStatus::COMPLETED->value)
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
    public function recordPayment(Request $request, GatePass $gate_pass, \App\Services\SalesService $salesService)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'payment_mode' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\PaymentMode::class)],
            'remarks' => 'nullable|string',
        ]);

        // Check payment date
        \App\Services\DayClosureService::checkAllowed($validated['date']);

        $salesService->recordPayment(
            $gate_pass,
            $validated['amount'],
            $validated['date'],
            $validated['payment_mode'],
            $validated['remarks'] ?? ''
        );

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function destroy(GatePass $gate_pass)
    {
        \App\Services\DayClosureService::checkAllowed($gate_pass->date);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($gate_pass) {
            // Check if there is a transaction and maybe prevent delete or delete transaction?
            if ($gate_pass->transaction) {
                $gate_pass->transaction->delete();
            }

            $gate_pass->delete();

            return redirect()->route('gate-passes.index')->with('success', 'Gate Pass deleted successfully.');
        });
    }

    public function calculator(Request $request, \App\Services\DistanceService $distanceService)
    {
        $defaultRate = Setting::get('rate_per_km', 10);
        $crusherLat = Setting::get('crusher_latitude', 0);
        $crusherLon = Setting::get('crusher_longitude', 0);

        $distance = 0;
        $cost = 0;

        if ($request->has(['lat', 'lon'])) {
            $destLat = $request->input('lat');
            $destLon = $request->input('lon');
            $roundTrip = $request->boolean('round_trip');
            $multiplier = $request->input('multiplier', 1.0);

            $distance = $distanceService->calculateDistance($crusherLat, $crusherLon, $destLat, $destLon);

            // Cost = Distance * Rate * Multiplier * (RoundTrip ? 2 : 1)
            $rtFactor = $roundTrip ? 2 : 1;
            $cost = $distance * $defaultRate * $multiplier * $rtFactor;

            if ($request->wantsJson()) {
                return response()->json([
                    'distance' => $distance,
                    'cost' => $cost,
                    'rate' => $defaultRate
                ]);
            }
        }

        $vehicles = Vehicle::where('is_active', true)->select('id', 'registration_number', 'model', 'transport_multiplier')->get();
        return view('gate_passes.calculator', compact('defaultRate', 'crusherLat', 'crusherLon', 'vehicles'));
    }

    public function distanceReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $query = GatePass::whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', \App\Enums\GatePassStatus::COMPLETED->value);

        // Overall Summary
        $summary = [
            'total_trips' => (clone $query)->count(),
            'total_distance' => (clone $query)->sum('distance_km'),
            'total_cost' => (clone $query)->sum('transport_cost'),
            'total_sales' => (clone $query)->sum('total_amount'), // For Cost vs Sales ratio
            'total_volume' => (clone $query)->sum('loading_quantity'),
        ];

        // Efficiency Metrics
        $summary['avg_cost_per_km'] = $summary['total_distance'] > 0 ? $summary['total_cost'] / $summary['total_distance'] : 0;
        $summary['avg_cost_per_ton'] = $summary['total_volume'] > 0 ? $summary['total_cost'] / $summary['total_volume'] : 0;
        $summary['cost_to_sales_ratio'] = $summary['total_sales'] > 0 ? ($summary['total_cost'] / $summary['total_sales']) * 100 : 0;

        // Location-wise Breakdown with efficiency
        $reportData = (clone $query)
            ->select(
                'delivery_location',
                \DB::raw('COUNT(*) as trip_count'),
                \DB::raw('SUM(distance_km) as total_distance'),
                \DB::raw('SUM(transport_cost) as total_cost'),
                \DB::raw('SUM(loading_quantity) as total_qty')
            )
            ->groupBy('delivery_location')
            ->orderByDesc('total_cost')
            ->get()
            ->map(function ($row) {
                $row->cost_per_km = $row->total_distance > 0 ? $row->total_cost / $row->total_distance : 0;
                $row->cost_per_ton = $row->total_qty > 0 ? $row->total_cost / $row->total_qty : 0;
                return $row;
            });

        // 4. Distance Range Analysis (Using DB Raw for efficiency)
        // Ranges: 0-10, 10-50, 50-100, 100+
        // Note: SQLite/MySQL syntax compatible CASE WHEN
        $rangeStats = (clone $query)
            ->selectRaw("
                CASE 
                    WHEN distance_km < 10 THEN 'Short (< 10 km)'
                    WHEN distance_km >= 10 AND distance_km < 50 THEN 'Medium (10 - 50 km)'
                    WHEN distance_km >= 50 AND distance_km < 100 THEN 'Long (50 - 100 km)'
                    ELSE 'Very Long (> 100 km)'
                END as range_label
            ")
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(transport_cost) as total_cost')
            ->selectRaw('SUM(distance_km) as total_dist')
            ->groupBy('range_label')
            ->get()
            ->map(function ($row) {
                $row->avg_cost_per_km = $row->total_dist > 0 ? $row->total_cost / $row->total_dist : 0;
                return $row;
            });

        return view('gate_passes.distance_report', compact('summary', 'reportData', 'rangeStats', 'startDate', 'endDate'));
    }

    public function exportDistanceReport(Request $request, \App\Services\ReportExportService $exportService)
    {
        return $exportService->exportDistanceReport($request);
    }
}
