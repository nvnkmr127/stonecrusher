<?php

namespace App\Http\Controllers;

use App\Models\DieselEntry;
use App\Models\Vehicle;
use App\Models\OperationalUnit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DieselEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $query = DieselEntry::with(['vehicle', 'operationalUnit'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('operational_unit_id')) {
            $query->where('operational_unit_id', $request->operational_unit_id);
        }

        $dieselEntries = $query->latest('date')->paginate(20)->withQueryString();

        // Summaries
        $totalDiesel = (clone $query)->sum('liters');

        // Total per vehicle
        $perVehicle = (clone $query)
            ->select('vehicle_id', DB::raw('SUM(liters) as total_liters'))
            ->groupBy('vehicle_id')
            ->with('vehicle')
            ->get();

        // Total per location
        $perLocation = (clone $query)
            ->select('operational_unit_id', DB::raw('SUM(liters) as total_liters'))
            ->groupBy('operational_unit_id')
            ->with('operationalUnit')
            ->get();

        // Daily summary for the range
        $dailySummary = (clone $query)
            ->select('date', DB::raw('SUM(liters) as daily_total'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Monthly consumption for the current year
        $monthlyConsumption = DieselEntry::select(
            DB::raw("strftime('%m', date) as month"),
            DB::raw('SUM(liters) as total_liters')
        )
            ->whereYear('date', date('Y'))
            ->groupBy('month')
            ->get()
            ->pluck('total_liters', 'month');

        $vehicles = Vehicle::getCached();
        $locations = OperationalUnit::getActive();

        return view('diesel.index', compact(
            'dieselEntries',
            'totalDiesel',
            'perVehicle',
            'perLocation',
            'dailySummary',
            'monthlyConsumption',
            'vehicles',
            'locations',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vehicles = Vehicle::getCached();
        $locations = OperationalUnit::getActive();
        return view('diesel.create', compact('vehicles', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'operational_unit_id' => 'required|exists:operational_units,id',
            'gate_pass_id' => 'nullable|exists:gate_passes,id',
            'liters' => 'required|numeric|min:0.01',
            'work_type' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['date']);

        DieselEntry::create($validated);

        return redirect()->route('diesel.index')->with('success', 'Diesel issue recorded successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DieselEntry $diesel)
    {
        $vehicles = Vehicle::getCached();
        $locations = OperationalUnit::getActive();
        return view('diesel.edit', compact('diesel', 'vehicles', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DieselEntry $diesel)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'operational_unit_id' => 'required|exists:operational_units,id',
            'gate_pass_id' => 'nullable|exists:gate_passes,id',
            'liters' => 'required|numeric|min:0.01',
            'work_type' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['date']);
        \App\Services\DayClosureService::checkAllowed($diesel->date);

        $diesel->update($validated);

        return redirect()->route('diesel.index')->with('success', 'Diesel issue updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DieselEntry $diesel)
    {
        \App\Services\DayClosureService::checkAllowed($diesel->date);

        $diesel->delete();

        return redirect()->route('diesel.index')->with('success', 'Diesel entry deleted successfully.');
    }
}
