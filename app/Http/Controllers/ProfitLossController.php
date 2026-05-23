<?php

namespace App\Http\Controllers;

use App\Services\Finance\ProfitLossService;
use App\Http\Resources\Finance\ProfitLossResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfitLossController extends Controller
{
    protected ProfitLossService $profitLossService;

    public function __construct(ProfitLossService $profitLossService)
    {
        $this->profitLossService = $profitLossService;
    }

    /**
     * Get P&L breakdown for a specific date range.
     */
    public function getProfitLoss(Request $request): JsonResponse
    {
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

        $data = $this->profitLossService->getProfitLossBreakdown($startDate, $endDate, $forceRefresh);

        return response()->json(new ProfitLossResource($data));
    }

    /**
     * Get monthly P&L summary for a year.
     */
    public function getMonthlySummary(Request $request): JsonResponse
    {
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

        $data = $this->profitLossService->getMonthlyProfitLossSummary($year, $forceRefresh);

        return response()->json(new ProfitLossResource($data));
    }

    /**
     * Export P&L report as CSV or PDF.
     */
    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:range,monthly',
            'format' => 'required|in:csv,pdf',
            'start_date' => 'required_if:type,range|date|date_format:Y-m-d',
            'end_date' => 'required_if:type,range|date|date_format:Y-m-d|after_or_equal:start_date',
            'year' => 'required_if:type,monthly|integer|min:2020|max:2050',
            'force_refresh' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $type = $request->input('type');
        $format = $request->input('format');
        $forceRefresh = filter_var($request->input('force_refresh'), FILTER_VALIDATE_BOOLEAN);

        if ($format === 'csv') {
            if ($type === 'range') {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $data = $this->profitLossService->getProfitLossBreakdown($startDate, $endDate, $forceRefresh);
                
                $csvFileName = "profit_loss_report_{$startDate}_{$endDate}.csv";
                $headers = [
                    "Content-type" => "text/csv",
                    "Content-Disposition" => "attachment; filename=$csvFileName",
                    "Pragma" => "no-cache",
                    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                    "Expires" => "0"
                ];

                $callback = function () use ($data) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Metric', 'Amount']);
                    fputcsv($file, ['Period Start', $data['period']['start']]);
                    fputcsv($file, ['Period End', $data['period']['end']]);
                    fputcsv($file, ['Sales (Revenues)', number_format($data['sales'], 2, '.', '')]);
                    fputcsv($file, ['Crusher Expense', number_format($data['crusher_expense'], 2, '.', '')]);
                    fputcsv($file, ['Quarry Expense', number_format($data['quarry_expense'], 2, '.', '')]);
                    fputcsv($file, ['Labour Expense', number_format($data['labour'], 2, '.', '')]);
                    fputcsv($file, ['Diesel Expense', number_format($data['diesel'], 2, '.', '')]);
                    fputcsv($file, ['Other Expense', number_format($data['other_expense'], 2, '.', '')]);
                    fputcsv($file, ['Net Profit', number_format($data['net_profit'], 2, '.', '')]);
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            } else {
                $year = (int) $request->input('year', Carbon::now()->year);
                $data = $this->profitLossService->getMonthlyProfitLossSummary($year, $forceRefresh);

                $csvFileName = "profit_loss_monthly_report_{$year}.csv";
                $headers = [
                    "Content-type" => "text/csv",
                    "Content-Disposition" => "attachment; filename=$csvFileName",
                    "Pragma" => "no-cache",
                    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                    "Expires" => "0"
                ];

                $callback = function () use ($data) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Month', 'Sales', 'Crusher Expense', 'Quarry Expense', 'Labour', 'Diesel', 'Other Expense', 'Net Profit']);
                    foreach ($data['monthly_breakdown'] as $row) {
                        fputcsv($file, [
                            $row['month_name'],
                            number_format($row['sales'], 2, '.', ''),
                            number_format($row['crusher_expense'], 2, '.', ''),
                            number_format($row['quarry_expense'], 2, '.', ''),
                            number_format($row['labour'], 2, '.', ''),
                            number_format($row['diesel'], 2, '.', ''),
                            number_format($row['other_expense'], 2, '.', ''),
                            number_format($row['net_profit'], 2, '.', '')
                        ]);
                    }
                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }
        } else {
            // PDF Export
            if ($type === 'range') {
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                $data = $this->profitLossService->getProfitLossBreakdown($startDate, $endDate, $forceRefresh);
                $pdf = Pdf::loadView('exports.reports.profit_loss', compact('data', 'type'));
                return $pdf->download("profit_loss_report_{$startDate}_{$endDate}.pdf");
            } else {
                $year = (int) $request->input('year', Carbon::now()->year);
                $data = $this->profitLossService->getMonthlyProfitLossSummary($year, $forceRefresh);
                $pdf = Pdf::loadView('exports.reports.profit_loss', compact('data', 'type'));
                return $pdf->download("profit_loss_monthly_report_{$year}.pdf");
            }
        }
    }
}
