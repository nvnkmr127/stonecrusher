<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->addMonth()->endOfMonth()->toDateString());

        $query = VehicleMaintenance::with('vehicle')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $maintenances = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $totalCost = (clone $query)->sum('cost');

        $perVehicle = (clone $query)
            ->select('vehicle_id', DB::raw('SUM(cost) as total_cost'))
            ->groupBy('vehicle_id')
            ->with('vehicle')
            ->get();

        $ongoingMaintenance = VehicleMaintenance::with('vehicle')
            ->where('status', 'In Progress')
            ->latest()
            ->get();

        $vehicles = Vehicle::getCached();
        $types = ['Routine Service', 'Major Repair', 'Tire Change', 'Oil Change', 'Insurance', 'Breakdown', 'Other'];
        $statuses = ['Pending', 'In Progress', 'Completed'];

        return view('vehicles.maintenance.index', compact(
            'maintenances',
            'ongoingMaintenance',
            'vehicles',
            'types',
            'statuses',
            'totalCost',
            'perVehicle',
            'startDate',
            'endDate'
        ));
    }

    public function create()
    {
        $vehicles = Vehicle::getCached();
        $types = ['Routine Service', 'Major Repair', 'Tire Change', 'Oil Change', 'Insurance', 'Breakdown', 'Other'];
        $statuses = ['Pending', 'In Progress', 'Completed'];
        return view('vehicles.maintenance.create', compact('vehicles', 'types', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:date',
            'type' => 'required|string',
            'status' => 'required|string|in:Pending,In Progress,Completed',
            'cost' => 'required|numeric|min:0',
            'odometer_reading' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'workshop_name' => 'nullable|string|max:255',
            'performed_by' => 'nullable|string|max:255',
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['date']);

        $maintenance = VehicleMaintenance::create($validated);

        // Sync vehicle status
        $vehicle = Vehicle::find($validated['vehicle_id']);
        if ($validated['status'] === 'In Progress') {
            $vehicle->update(['operational_status' => 'Under Maintenance']);
        } elseif ($validated['status'] === 'Completed') {
            $vehicle->update(['operational_status' => 'Operational']);
        }

        return redirect()->route('vehicle-maintenance.index')->with('success', 'Maintenance record added successfully.');
    }

    public function edit(VehicleMaintenance $vehicle_maintenance)
    {
        $vehicles = Vehicle::getCached();
        $types = ['Routine Service', 'Major Repair', 'Tire Change', 'Oil Change', 'Insurance', 'Breakdown', 'Other'];
        $statuses = ['Pending', 'In Progress', 'Completed'];
        return view('vehicles.maintenance.edit', compact('vehicle_maintenance', 'vehicles', 'types', 'statuses'));
    }

    public function update(Request $request, VehicleMaintenance $vehicle_maintenance)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:date',
            'type' => 'required|string',
            'status' => 'required|string|in:Pending,In Progress,Completed',
            'cost' => 'required|numeric|min:0',
            'odometer_reading' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'workshop_name' => 'nullable|string|max:255',
            'performed_by' => 'nullable|string|max:255',
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['date']);
        \App\Services\DayClosureService::checkAllowed($vehicle_maintenance->date);

        $vehicle_maintenance->update($validated);

        // Sync vehicle status
        $vehicle = Vehicle::find($validated['vehicle_id']);
        if ($validated['status'] === 'In Progress') {
            $vehicle->update(['operational_status' => 'Under Maintenance']);
        } elseif ($validated['status'] === 'Completed') {
            $vehicle->update(['operational_status' => 'Operational']);
        }

        return redirect()->route('vehicle-maintenance.index')->with('success', 'Maintenance record updated successfully.');
    }

    public function destroy(VehicleMaintenance $vehicle_maintenance)
    {
        \App\Services\DayClosureService::checkAllowed($vehicle_maintenance->date);

        $vehicle_maintenance->delete();

        return redirect()->route('vehicle-maintenance.index')->with('success', 'Maintenance record deleted successfully.');
    }

    public function markComplete(VehicleMaintenance $vehicle_maintenance)
    {
        $vehicle_maintenance->update([
            'status' => 'Completed',
            'completion_date' => now()
        ]);

        $vehicle_maintenance->vehicle->update(['operational_status' => 'Operational']);

        return back()->with('success', 'Maintenance marked as completed and vehicle is now Operational.');
    }
}
