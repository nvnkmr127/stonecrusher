<?php

namespace App\Http\Controllers;

use App\Models\MetalType;
use Illuminate\Http\Request;

class MetalTypeController extends Controller
{
    public function index()
    {
        $metalTypes = MetalType::latest()->paginate(15);
        return view('metal-types.index', compact('metalTypes'));
    }

    public function create()
    {
        return view('metal-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:metal_types',
            'description' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        MetalType::create($validated);

        return redirect()->route('metal-types.index')->with('success', 'Metal type created successfully!');
    }

    public function edit(MetalType $metalType)
    {
        return view('metal-types.edit', compact('metalType'));
    }

    public function update(Request $request, MetalType $metalType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:metal_types,name,' . $metalType->id,
            'description' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $metalType->update($validated);

        return redirect()->route('metal-types.index')->with('success', 'Metal type updated successfully!');
    }

    public function destroy(MetalType $metalType)
    {
        $metalType->delete();
        return redirect()->route('metal-types.index')->with('success', 'Metal type deleted successfully!');
    }
}
