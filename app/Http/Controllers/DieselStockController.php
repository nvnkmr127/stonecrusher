<?php

namespace App\Http\Controllers;

use App\Models\DieselStock;
use App\Models\OperationalUnit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DieselStockController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $stocks = DieselStock::with('operationalUnit')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->paginate(31)
            ->withQueryString();

        return view('diesel_stocks.index', compact('stocks', 'startDate', 'endDate'));
    }

    public function create(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $locations = OperationalUnit::getActive();

        // Try to get opening balance from previous day
        $prevClosing = DieselStock::where('date', '<', $date)
            ->orderBy('date', 'desc')
            ->value('closing_liters') ?? 0;

        return view('diesel_stocks.create', compact('locations', 'date', 'prevClosing'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:diesel_stocks,date',
            'opening_liters' => 'required|numeric|min:0',
            'purchased_liters' => 'required|numeric|min:0',
            'closing_liters' => 'required|numeric|min:0',
            'operational_unit_id' => 'nullable|exists:operational_units,id',
            'remarks' => 'nullable|string',
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['date']);

        DieselStock::create($validated);

        return redirect()->route('diesel-stocks.index')->with('success', 'Diesel stock recorded.');
    }

    public function edit(DieselStock $dieselStock)
    {
        $locations = OperationalUnit::getActive();
        return view('diesel_stocks.edit', compact('dieselStock', 'locations'));
    }

    public function update(Request $request, DieselStock $dieselStock)
    {
        $validated = $request->validate([
            'date' => 'required|date|unique:diesel_stocks,date,' . $dieselStock->id,
            'opening_liters' => 'required|numeric|min:0',
            'purchased_liters' => 'required|numeric|min:0',
            'closing_liters' => 'required|numeric|min:0',
            'operational_unit_id' => 'nullable|exists:operational_units,id',
            'remarks' => 'nullable|string',
        ]);

        \App\Services\DayClosureService::checkAllowed($validated['date']);
        \App\Services\DayClosureService::checkAllowed($dieselStock->date);

        $dieselStock->update($validated);

        return redirect()->route('diesel-stocks.index')->with('success', 'Diesel stock updated.');
    }

    public function destroy(DieselStock $dieselStock)
    {
        \App\Services\DayClosureService::checkAllowed($dieselStock->date);
        $dieselStock->delete();
        return redirect()->route('diesel-stocks.index')->with('success', 'Diesel stock deleted.');
    }
}
