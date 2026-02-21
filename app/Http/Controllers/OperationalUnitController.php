<?php

namespace App\Http\Controllers;

use App\Models\OperationalUnit;
use Illuminate\Http\Request;

class OperationalUnitController extends Controller
{
    public function index()
    {
        $locations = OperationalUnit::latest()->get();
        return view('operational_units.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:operational_units',
            'name' => 'required|string|max:255',
        ]);

        OperationalUnit::create($validated);

        return back()->with('success', 'Operational Unit added successfully.');
    }

    public function update(Request $request, OperationalUnit $operationalUnit)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:operational_units,code,' . $operationalUnit->id,
            'name' => 'required|string|max:255',
        ]);

        $operationalUnit->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'Operational Unit updated successfully.');
    }

    public function destroy(OperationalUnit $operationalUnit)
    {
        // Add check if needed
        $operationalUnit->delete();
        return back()->with('success', 'Operational Unit deleted successfully.');
    }
}
