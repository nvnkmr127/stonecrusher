<?php

namespace App\Http\Controllers;

use App\Models\OperationalUnit;
use App\Models\OperationalTag;
use App\Models\OperationalRecord;
use App\Services\DayClosureService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperationalRecordController extends Controller
{
    private const PER_PAGE = 15;

    /**
     * Display Quarry operations.
     */
    public function quarryIndex(Request $request)
    {
        $unit = OperationalUnit::where('code', 'QRY')->firstOrFail();
        
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $records = OperationalRecord::with('tag')
            ->where('operational_unit_id', $unit->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $tags = OperationalTag::where('operational_unit_id', $unit->id)->orderBy('name')->get();

        // Calculate totals for Quarry
        $summary = OperationalRecord::where('operational_records.operational_unit_id', $unit->id)
            ->whereBetween('operational_records.date', [$startDate, $endDate])
            ->join('operational_tags', 'operational_records.operational_tag_id', '=', 'operational_tags.id')
            ->selectRaw('operational_tags.type, SUM(operational_records.amount) as total')
            ->groupBy('operational_tags.type')
            ->pluck('total', 'type');

        $totalRevenue = $summary->get('revenue', 0);
        $totalExpense = $summary->get('expense', 0);
        $netProfitLoss = $totalRevenue - $totalExpense;

        return view('operations.quarry', compact(
            'unit', 'records', 'tags', 'month', 'year', 
            'totalRevenue', 'totalExpense', 'netProfitLoss'
        ));
    }

    /**
     * Display Crusher operations.
     */
    public function crusherIndex(Request $request)
    {
        $unit = OperationalUnit::where('code', 'CRS')->firstOrFail();
        
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $records = OperationalRecord::with('tag')
            ->where('operational_unit_id', $unit->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $tags = OperationalTag::where('operational_unit_id', $unit->id)->orderBy('name')->get();

        // Calculate totals for Crusher
        $summary = OperationalRecord::where('operational_records.operational_unit_id', $unit->id)
            ->whereBetween('operational_records.date', [$startDate, $endDate])
            ->join('operational_tags', 'operational_records.operational_tag_id', '=', 'operational_tags.id')
            ->selectRaw('operational_tags.type, SUM(operational_records.amount) as total')
            ->groupBy('operational_tags.type')
            ->pluck('total', 'type');

        $totalRevenue = $summary->get('revenue', 0);
        $totalExpense = $summary->get('expense', 0);
        $netProfitLoss = $totalRevenue - $totalExpense;

        return view('operations.crusher', compact(
            'unit', 'records', 'tags', 'month', 'year', 
            'totalRevenue', 'totalExpense', 'netProfitLoss'
        ));
    }

    /**
     * Store a newly created operational record.
     */
    public function storeRecord(Request $request, OperationalUnit $unit)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'operational_tag_id' => 'required|exists:operational_tags,id',
            'quantity' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        DayClosureService::checkAllowed($validated['date']);

        $record = new OperationalRecord($validated);
        $record->operational_unit_id = $unit->id;
        $record->save();

        return $this->redirectToUnitIndex($unit, $record->date, $record)
            ->with('success', 'Operational record added successfully.');
    }

    /**
     * Update the specified operational record.
     */
    public function updateRecord(Request $request, OperationalRecord $record)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'operational_tag_id' => 'required|exists:operational_tags,id',
            'quantity' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        DayClosureService::checkAllowed($validated['date']);
        DayClosureService::checkAllowed($record->date);

        $record->update($validated);

        $record->refresh();

        return $this->redirectToUnitIndex($record->operationalUnit, $record->date, $record)
            ->with('success', 'Operational record updated successfully.');
    }

    /**
     * Remove the specified operational record.
     */
    public function destroyRecord(OperationalRecord $record)
    {
        DayClosureService::checkAllowed($record->date);

        $unit = $record->operationalUnit;
        $date = $record->date;
        $record->delete();

        return $this->redirectToUnitIndex($unit, $date)
            ->with('success', 'Operational record deleted successfully.');
    }

    /**
     * Store a new custom tag.
     */
    public function storeTag(Request $request, OperationalUnit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:expense,revenue',
        ]);

        // Check uniqueness for this unit
        $exists = OperationalTag::where('operational_unit_id', $unit->id)
            ->where('name', $validated['name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'This tag name already exists for this unit.']);
        }

        $tag = new OperationalTag($validated);
        $tag->operational_unit_id = $unit->id;
        $tag->save();

        return back()->with('success', 'Operational tag added successfully.');
    }

    /**
     * Remove the specified tag.
     */
    public function destroyTag(OperationalTag $tag)
    {
        // Check if the tag is used in any records
        $inUse = OperationalRecord::where('operational_tag_id', $tag->id)->exists();

        if ($inUse) {
            return back()->withErrors(['error' => 'This tag cannot be deleted because it is in use by operational records.']);
        }

        $tag->delete();

        return back()->with('success', 'Operational tag deleted successfully.');
    }

    private function indexRouteNameForUnit(OperationalUnit $unit): string
    {
        if ($unit->code === 'QRY') {
            return 'quarry.index';
        }

        return 'crusher.index';
    }

    private function redirectToUnitIndex(OperationalUnit $unit, Carbon $date, ?OperationalRecord $recordToReveal = null)
    {
        $month = $date->month;
        $year = $date->year;

        $params = [
            'month' => $month,
            'year' => $year,
        ];

        if ($recordToReveal) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

            $beforeCount = OperationalRecord::query()
                ->where('operational_unit_id', $unit->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where(function ($query) use ($recordToReveal) {
                    $query
                        ->where('date', '>', $recordToReveal->date->toDateString())
                        ->orWhere(function ($q) use ($recordToReveal) {
                            $q->where('date', $recordToReveal->date->toDateString())
                                ->where(function ($q2) use ($recordToReveal) {
                                    $q2->where('created_at', '>', $recordToReveal->created_at)
                                        ->orWhere(function ($q3) use ($recordToReveal) {
                                            $q3->where('created_at', $recordToReveal->created_at)
                                                ->where('id', '>', $recordToReveal->id);
                                        });
                                });
                        });
                })
                ->count();

            $params['page'] = intdiv($beforeCount, self::PER_PAGE) + 1;
        }

        return redirect()->route($this->indexRouteNameForUnit($unit), $params);
    }
}
