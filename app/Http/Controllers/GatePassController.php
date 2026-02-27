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
        $gatePasses = GatePass::with(['client', 'project', 'vehicle', 'metalType', 'transaction', 'sourceUnit', 'destinationUnit'])
            ->when(request('search'), function ($query, $search) {
                $query->where('gate_pass_number', 'like', "%{$search}%")
                    ->orWhere('driver_name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('registration_number', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('gate_passes.index', compact('gatePasses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $projects = \App\Models\Project::where('status', 'active')->orderBy('name')->get();
        $vehicles = Vehicle::getCached();
        $metalTypes = MetalType::getCached();

        $gpNumber = 'GP-' . date('Ymd') . '-' . str_pad(GatePass::where('date', '>=', now()->startOfDay())->where('date', '<=', now()->endOfDay())->count() + 1, 4, '0', STR_PAD_LEFT);

        $transportRate = Setting::get('rate_per_km', 0);
        $crusherLat = Setting::get('crusher_latitude', 0);
        $crusherLon = Setting::get('crusher_longitude', 0);
        $defaultRoundTrip = (bool) Setting::get('default_round_trip', false);
        $destinations = DeliveryDestination::getCached();
        $operationalUnits = \App\Models\OperationalUnit::getActive();

        return view('gate_passes.create', compact('clients', 'projects', 'vehicles', 'metalTypes', 'gpNumber', 'transportRate', 'crusherLat', 'crusherLon', 'destinations', 'defaultRoundTrip', 'operationalUnits'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->has('status')) {
            $request->merge(['status' => \App\Enums\GatePassStatus::COMPLETED->value]);
        }

        $status = $request->input('status');

        // Base Rules
        $rules = [
            'gate_pass_number' => 'required|unique:gate_passes',
            'date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'manual_vehicle_number' => 'nullable|required_without:vehicle_id|string|max:20',
            'activity_type' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\ActivityType::class)],
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\GatePassStatus::class)],
            'source_unit_id' => 'required|exists:operational_units,id',
            'destination_unit_id' => 'required|exists:operational_units,id',
            'trips' => 'required|integer|min:1',
            'destination_type' => 'nullable|string', // Used for conditional logic
            'remarks' => 'nullable|string',
            'delivery_location' => 'nullable|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'transport_is_billable' => 'nullable|boolean',
            'diesel_qty' => 'nullable|numeric|min:0',
            'diesel_amount' => 'nullable|numeric|min:0',
            'rate_per_ton' => 'nullable|numeric',
            'metal_type_id' => 'nullable|exists:metal_types,id',
            'net_weight' => 'nullable|numeric',
        ];

        // Strict validation for Sales
        if ($request->input('activity_type') === \App\Enums\ActivityType::SALES->value) {
            $rules['net_weight'] = 'required|numeric|gt:0';
            $rules['rate_per_ton'] = 'required|numeric|gt:0';
            $rules['metal_type_id'] = 'required|exists:metal_types,id';
            $rules['client_id'] = 'nullable|required_if:destination_type,registered|exists:clients,id';
            $rules['manual_customer_name'] = 'nullable|required_if:destination_type,regular|string|max:255';
        }
        // Validation for other Completed passes
        elseif ($status === \App\Enums\GatePassStatus::COMPLETED->value) {
            $rules['net_weight'] = 'required|numeric|min:0';
            $rules['metal_type_id'] = 'required|exists:metal_types,id';
        }

        $validated = $request->validate($rules);

        // Check if date is closed
        \App\Services\DayClosureService::checkAllowed($validated['date']);

        // Handle Manual Vehicle
        if (empty($validated['vehicle_id']) && !empty($request->input('manual_vehicle_number'))) {
            $vehicle = Vehicle::firstOrCreate(
                ['registration_number' => $request->input('manual_vehicle_number')],
                ['is_active' => true, 'transport_multiplier' => 1.0] // Default defaults
            );
            $validated['vehicle_id'] = $vehicle->id;
        }

        if (empty($validated['vehicle_id'])) {
            return back()->withErrors(['vehicle_id' => 'Please select a vehicle or enter one manually.'])->withInput();
        }

        // 1. Enforce Weight Logic
        $validated['gross_weight'] = floatval($validated['gross_weight'] ?? 0);
        $validated['tare_weight'] = floatval($validated['tare_weight'] ?? 0);

        // Auto-calculate Net Weight (Server ignores client input for safety)
        if ($validated['gross_weight'] > 0 && $validated['tare_weight'] > 0) {
            $validated['net_weight'] = max(0, $validated['gross_weight'] - $validated['tare_weight']);
        } else {
            $validated['net_weight'] = floatval($validated['net_weight'] ?? 0);
        }

        // 2. Enforce Financial Logic (if Completed)
        if ($status === \App\Enums\GatePassStatus::COMPLETED->value) {
            $qty = ($request->input('loading_quantity') > 0) ? $request->input('loading_quantity') : $validated['net_weight'];

            // Fetch rate from MetalType if not provided in request (since we removed it from UI)
            $rate = $request->input('rate_per_ton');
            if ($rate === null && !empty($validated['metal_type_id'])) {
                $metalType = \App\Models\MetalType::find($validated['metal_type_id']);
                $rate = $metalType ? $metalType->unit_price : 0;
            }
            $rate = floatval($rate ?? 0);

            $diesel = floatval($request->input('diesel_amount', 0));
            $validated['diesel_qty'] = floatval($request->input('diesel_qty', 0));
            $transport = 0;

            if ($request->boolean('transport_is_billable')) {
                $transport = floatval($request->input('transport_cost', 0));
            }

            // Total = (Qty * Rate) + Diesel + Transport
            $calculatedTotal = ($qty * $rate) + $diesel + $transport;

            // Override for internal movement
            if ($validated['activity_type'] === \App\Enums\ActivityType::INTERNAL_MOVEMENT->value) {
                $calculatedTotal = 0;
            }

            // Override for internal projects
            if (!empty($validated['project_id'])) {
                $project = \App\Models\Project::find($validated['project_id']);
                if ($project && $project->is_internal) {
                    $calculatedTotal = 0;
                }
            }

            // Override with server-calculated entries
            $validated['loading_quantity'] = $qty;
            $validated['rate_per_ton'] = $rate;
            $validated['diesel_amount'] = $diesel;
            $validated['transport_cost'] = floatval($request->input('transport_cost', 0));
            $validated['total_amount'] = round($calculatedTotal, 2);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            $gatePass = GatePass::create($validated);

            if ($gatePass->status === \App\Enums\GatePassStatus::COMPLETED && ($gatePass->client_id || $gatePass->manual_customer_name)) {
                \App\Events\GatePassCompleted::dispatch($gatePass);
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
        $gatePass = $gate_pass->load(['client', 'project', 'vehicle', 'metalType', 'transaction', 'sourceUnit', 'destinationUnit']);
        return view('gate_passes.show', compact('gatePass'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GatePass $gate_pass)
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();
        $vehicles = Vehicle::where('is_active', true)->get();
        $metalTypes = MetalType::all();
        $transportRate = Setting::get('rate_per_km', 0);
        $crusherLat = Setting::get('crusher_latitude', 0);
        $crusherLon = Setting::get('crusher_longitude', 0);
        $destinations = DeliveryDestination::orderBy('name')->get();
        $operationalUnits = \App\Models\OperationalUnit::getActive();

        $gatePass = $gate_pass;
        return view('gate_passes.edit', compact('gatePass', 'gate_pass', 'clients', 'projects', 'vehicles', 'metalTypes', 'transportRate', 'crusherLat', 'crusherLon', 'destinations', 'operationalUnits'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $gate_pass = GatePass::findOrFail($id);

        if (!$request->has('status')) {
            $request->merge(['status' => $gate_pass->status instanceof \App\Enums\GatePassStatus ? $gate_pass->status->value : $gate_pass->status]);
        }

        $rules = [
            'date' => 'required|date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'manual_vehicle_number' => 'nullable|required_without:vehicle_id|string|max:20',
            'activity_type' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\ActivityType::class)],
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\GatePassStatus::class)],
            'source_unit_id' => 'required|exists:operational_units,id',
            'destination_unit_id' => 'required|exists:operational_units,id',
            'trips' => 'required|integer|min:1',
            'destination_type' => 'nullable|string',
            'remarks' => 'nullable|string',
            'delivery_location' => 'nullable|string|max:255',
            'distance_km' => 'nullable|numeric|min:0',
            'transport_cost' => 'nullable|numeric|min:0',
            'transport_is_billable' => 'nullable|boolean',
            'diesel_qty' => 'nullable|numeric|min:0',
            'diesel_amount' => 'nullable|numeric|min:0',
            'rate_per_ton' => 'nullable|numeric',
            'metal_type_id' => 'nullable|exists:metal_types,id',
            'net_weight' => 'nullable|numeric',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'manual_customer_name' => 'nullable|string|max:255',
        ];

        // Strict validation for Sales
        if ($request->input('activity_type') === \App\Enums\ActivityType::SALES->value) {
            $rules['net_weight'] = 'required|numeric|gt:0';
            $rules['rate_per_ton'] = 'required|numeric|gt:0';
            $rules['metal_type_id'] = 'required|exists:metal_types,id';
            $rules['client_id'] = 'nullable|required_if:destination_type,registered|exists:clients,id';
            $rules['manual_customer_name'] = 'nullable|required_if:destination_type,regular|string|max:255';
        }
        // Validation for other Completed passes
        elseif ($request->input('status') === \App\Enums\GatePassStatus::COMPLETED->value) {
            $rules['net_weight'] = 'required|numeric|min:0';
            $rules['metal_type_id'] = 'required|exists:metal_types,id';
        }

        $validated = $request->validate($rules);

        // Check if new date is closed
        \App\Services\DayClosureService::checkAllowed($validated['date']);
        // Check if original date was closed (prevent editing closed records)
        \App\Services\DayClosureService::checkAllowed($gate_pass->date);

        // Handle Manual Vehicle
        if (empty($validated['vehicle_id']) && !empty($request->input('manual_vehicle_number'))) {
            $vehicle = Vehicle::firstOrCreate(
                ['registration_number' => $request->input('manual_vehicle_number')],
                ['is_active' => true, 'transport_multiplier' => 1.0] // Default defaults
            );
            $validated['vehicle_id'] = $vehicle->id;
        }

        if (empty($validated['vehicle_id'])) {
            return back()->withErrors(['vehicle_id' => 'Please select a vehicle or enter one manually.'])->withInput();
        }

        // 1. Enforce Weight Logic
        $validated['gross_weight'] = floatval($validated['gross_weight'] ?? 0);
        $validated['tare_weight'] = floatval($validated['tare_weight'] ?? 0);

        if ($validated['gross_weight'] > 0 && $validated['tare_weight'] > 0) {
            $validated['net_weight'] = max(0, $validated['gross_weight'] - $validated['tare_weight']);
        } else {
            $validated['net_weight'] = floatval($validated['net_weight'] ?? 0);
        }

        // 2. Enforce Financial Logic (if Completed)
        // Note: partial updates might be tricky if fields are missing, but validation requires them for 'completed'
        if ($validated['status'] === \App\Enums\GatePassStatus::COMPLETED->value) {
            $qty = ($validated['loading_quantity'] ?? 0) > 0 ? $validated['loading_quantity'] : $validated['net_weight'];

            // Fetch rate from MetalType if not provided in request (since we removed it from UI)
            $rate = $request->input('rate_per_ton');
            if ($rate === null && !empty($validated['metal_type_id'])) {
                $metalType = \App\Models\MetalType::find($validated['metal_type_id']);
                $rate = $metalType ? $metalType->unit_price : ($gate_pass->rate_per_ton ?? 0);
            }
            $rate = floatval($rate ?? 0);

            $diesel = floatval($request->input('diesel_amount', $gate_pass->diesel_amount ?? 0));
            $validated['diesel_qty'] = floatval($request->input('diesel_qty', $gate_pass->diesel_qty ?? 0));
            $transport = 0;

            if ($request->boolean('transport_is_billable')) {
                $transport = floatval($request->input('transport_cost', 0));
            }

            // Total = (Qty * Rate) + Diesel + Transport
            $calculatedTotal = ($qty * $rate) + $diesel + $transport;

            // Override for internal movement
            if ($validated['activity_type'] === \App\Enums\ActivityType::INTERNAL_MOVEMENT->value) {
                $calculatedTotal = 0;
            }

            // Override for internal projects
            if (!empty($validated['project_id'])) {
                $project = \App\Models\Project::find($validated['project_id']);
                if ($project && $project->is_internal) {
                    $calculatedTotal = 0;
                }
            }

            // Override with server-calculated entries
            $validated['loading_quantity'] = $qty;
            $validated['rate_per_ton'] = $rate;
            $validated['diesel_amount'] = $diesel;
            $validated['transport_cost'] = floatval($request->input('transport_cost', 0));
            $validated['total_amount'] = round($calculatedTotal, 2);
        }

        // Check if editing a completed pass
        if ($gate_pass->status === \App\Enums\GatePassStatus::COMPLETED->value && !$gate_pass->wasChanged('status')) {
            $validated['remarks'] = ($validated['remarks'] ?? '') . " [Edited on " . now()->toDateTimeString() . "]";
        }

        // Restrict Cancellation to Admin
        if ($validated['status'] === \App\Enums\GatePassStatus::CANCELLED->value && $gate_pass->status !== \App\Enums\GatePassStatus::CANCELLED->value && !auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Only Admins can cancel a Gate Pass.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($gate_pass, $validated, $request) {
            $gate_pass->update($validated);

            if ($gate_pass->status === \App\Enums\GatePassStatus::COMPLETED->value && ($gate_pass->client_id || $gate_pass->manual_customer_name)) {
                \App\Events\GatePassCompleted::dispatch($gate_pass);
            } elseif ($gate_pass->status === \App\Enums\GatePassStatus::CANCELLED->value) {
                \App\Events\GatePassCancelled::dispatch($gate_pass);
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
        $baseQuery = GatePass::where('date', '>=', \Carbon\Carbon::parse($date)->startOfDay())
            ->where('date', '<=', \Carbon\Carbon::parse($date)->endOfDay())
            ->where('status', \App\Enums\GatePassStatus::COMPLETED->value);

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
            ->where('date', '>=', \Carbon\Carbon::parse($date)->startOfDay())
            ->where('date', '<=', \Carbon\Carbon::parse($date)->endOfDay())
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
        $summary['avg_cost_per_cft'] = $summary['total_volume'] > 0 ? $summary['total_cost'] / $summary['total_volume'] : 0;
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
                $row->cost_per_cft = $row->total_qty > 0 ? $row->total_cost / $row->total_qty : 0;
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

    public function searchLocation(Request $request)
    {
        $query = $request->input('q');
        if (empty($query) || strlen($query) < 3) {
            return response()->json([]);
        }

        // Cache key based on query
        $cacheKey = 'geo_search_' . md5(strtolower($query));

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 86400, function () use ($query) { // 24 hours cache
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'User-Agent' => config('app.name') . ' (' . config('app.url') . ')'
                ])->get('https://nominatim.openstreetmap.org/search', [
                            'format' => 'json',
                            'q' => $query,
                            'limit' => 50,
                            'countrycodes' => 'in',
                            'addressdetails' => 1
                        ]);

                if ($response->successful()) {
                    return $response->json();
                }
                return [];
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Geocoding failed: ' . $e->getMessage());
                return [];
            }
        });
    }
}
