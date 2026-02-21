<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('operationalUnit')->where('is_owned', true)->latest()->paginate(15);
        return view('vehicles.index', compact('vehicles'));
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load([
            'operationalUnit',
            'maintenances' => fn($q) => $q->latest()->limit(5),
            'dieselEntries' => fn($q) => $q->latest()->limit(5),
            'gatePasses' => fn($q) => $q->latest()->limit(5)
        ]);

        return view('vehicles.show', compact('vehicle'));
    }

    public function create()
    {
        $locations = \App\Models\OperationalUnit::getActive();
        return view('vehicles.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->merge(['is_owned' => true]);

        $validated = $request->validate([
            'registration_number' => 'required|string|max:255|unique:vehicles',
            'type' => 'nullable|string|max:255',
            'operational_unit_id' => 'nullable|exists:operational_units,id',
            'model' => 'nullable|string|max:255',
            'transport_multiplier' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_owned' => 'boolean',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully!');
    }

    public function edit(Vehicle $vehicle)
    {
        $locations = \App\Models\OperationalUnit::getActive();
        return view('vehicles.edit', compact('vehicle', 'locations'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->merge(['is_owned' => true]);

        $validated = $request->validate([
            'registration_number' => 'required|string|max:255|unique:vehicles,registration_number,' . $vehicle->id,
            'type' => 'nullable|string|max:255',
            'operational_unit_id' => 'nullable|exists:operational_units,id',
            'model' => 'nullable|string|max:255',
            'transport_multiplier' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_owned' => 'boolean',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully!');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully!');
    }
}
