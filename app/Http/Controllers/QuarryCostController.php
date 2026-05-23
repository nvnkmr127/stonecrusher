<?php

namespace App\Http\Controllers;

use App\Models\OperationalUnit;
use App\Services\Quarry\QuarryCostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class QuarryCostController extends Controller
{
    protected QuarryCostService $costService;

    public function __construct(QuarryCostService $costService)
    {
        $this->costService = $costService;
    }

    /**
     * Helper to validate date range filters.
     */
    protected function validateRange(Request $request): ?JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'force_refresh' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return null;
    }

    /**
     * Get aggregate cost breakdown for a quarry unit.
     */
    public function getCostBreakdown(Request $request, OperationalUnit $unit): JsonResponse
    {
        if ($unit->code !== 'QRY') {
            return response()->json(['error' => 'Cost calculations are only supported for Quarry units.'], 400);
        }

        if ($errorResponse = $this->validateRange($request)) {
            return $errorResponse;
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->costService->getCostBreakdown($unit->id, $startDate, $endDate, $forceRefresh);

        return response()->json($data);
    }

    /**
     * Get daily cost summary for a quarry unit.
     */
    public function getDailySummary(Request $request, OperationalUnit $unit): JsonResponse
    {
        if ($unit->code !== 'QRY') {
            return response()->json(['error' => 'Daily cost summaries are only supported for Quarry units.'], 400);
        }

        if ($errorResponse = $this->validateRange($request)) {
            return $errorResponse;
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->costService->getDailySummary($unit->id, $startDate, $endDate, $forceRefresh);

        return response()->json($data);
    }

    /**
     * Get monthly cost summaries for a given year.
     */
    public function getMonthlySummary(Request $request, OperationalUnit $unit): JsonResponse
    {
        if ($unit->code !== 'QRY') {
            return response()->json(['error' => 'Monthly cost summaries are only supported for Quarry units.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2020|max:2050',
            'force_refresh' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $year = (int) $request->input('year', Carbon::now()->year);
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->costService->getMonthlySummary($unit->id, $year, $forceRefresh);

        return response()->json($data);
    }

    /**
     * Get vendor-wise costing & deduction summary.
     */
    public function getVendorSummary(Request $request, OperationalUnit $unit): JsonResponse
    {
        if ($unit->code !== 'QRY') {
            return response()->json(['error' => 'Vendor costing reports are only supported for Quarry units.'], 400);
        }

        if ($errorResponse = $this->validateRange($request)) {
            return $errorResponse;
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);

        $data = $this->costService->getVendorSummary($unit->id, $startDate, $endDate, $forceRefresh);

        return response()->json($data);
    }
}
