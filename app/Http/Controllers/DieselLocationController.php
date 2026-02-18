<?php

namespace App\Http\Controllers;

use App\Models\DieselLocation;
use Illuminate\Http\Request;

class DieselLocationController extends Controller
{
    public function index()
    {
        $locations = DieselLocation::latest()->get();
        return view('diesel.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:diesel_locations',
        ]);

        DieselLocation::create($validated);

        return back()->with('success', 'Location added successfully.');
    }

    public function update(Request $request, DieselLocation $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:diesel_locations,name,' . $location->id,
        ]);

        $location->update([
            'name' => $validated['name'],
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Location updated successfully.');
    }

    public function destroy(DieselLocation $location)
    {
        if ($location->dieselEntries()->exists()) {
            $location->update(['is_active' => false]);
            return back()->with('error', 'Location cannot be deleted as it has entries. It has been deactivated instead.');
        }

        $location->delete();
        return back()->with('success', 'Location deleted successfully.');
    }
}
