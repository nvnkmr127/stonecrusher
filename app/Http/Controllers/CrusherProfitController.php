<?php

namespace App\Http\Controllers;

use App\Models\OperationalUnit;
use App\Services\Crusher\CrusherProfitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CrusherProfitController extends Controller
{
    protected CrusherProfitService $profitService;

    public function __construct(CrusherProfitService $profitService)
    {
        $this->profitService = $profitService;
    }

    /**
     * Get profitability breakdown for a specific date range.
     */
    public function getProfitability(Request $request, OperationalUnit $unit): JsonResponse
    {
        // Enforce that the operational unit is classified as a crusher
        if ($unit->code !== 'CRS' && (isset($unit->type) && $unit->type !== 'crusher')) {
            return response()->json([
                'error' => 'Profitability calculations are only supported for Crusher units.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'force_refresh' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->profitService->getProfitability($unit->id, $startDate, $endDate, $forceRefresh);

        return response()->json($data);
    }

    /**
     * Get month-by-month profit breakdown for a year.
     */
    public function getMonthlySummary(Request $request, OperationalUnit $unit): JsonResponse
    {
        if ($unit->code !== 'CRS' && (isset($unit->type) && $unit->type !== 'crusher')) {
            return response()->json([
                'error' => 'Monthly profit breakdowns are only supported for Crusher units.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2020|max:2050',
            'force_refresh' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $year = (int) $request->input('year', Carbon::now()->year);
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->profitService->getMonthlyProfitSummary($unit->id, $year, $forceRefresh);

        return response()->json($data);
    }
}
