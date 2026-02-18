<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('dieselLocation')->latest()->paginate(15);
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $locations = \App\Models\DieselLocation::getActive();
        return view('vehicles.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|max:255|unique:vehicles',
            'type' => 'nullable|string|max:255',
            'diesel_location_id' => 'nullable|exists:diesel_locations,id',
            'model' => 'nullable|string|max:255',
            'transport_multiplier' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully!');
    }

    public function edit(Vehicle $vehicle)
    {
        $locations = \App\Models\DieselLocation::getActive();
        return view('vehicles.edit', compact('vehicle', 'locations'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|max:255|unique:vehicles,registration_number,' . $vehicle->id,
            'type' => 'nullable|string|max:255',
            'diesel_location_id' => 'nullable|exists:diesel_locations,id',
            'model' => 'nullable|string|max:255',
            'transport_multiplier' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
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
