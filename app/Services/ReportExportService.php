<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\GatePass;
use App\Models\ClientTransaction;
use App\Models\Client;
use App\Models\Vehicle;
use App\Models\MetalType;
use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    public function exportDaily(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');

        $sales = GatePass::with(['client', 'vehicle', 'metalType'])
            ->whereDate('date', $date)
            ->where('status', 'completed')
            ->get();

        $transactions = ClientTransaction::with('client')
            ->whereDate('transaction_date', $date)
            ->get();

        if ($request->input('format') === 'pdf') {
            $salesSummary = [
                'count' => $sales->count(),
                'total_amount' => $sales->sum('total_amount'),
                'total_volume' => $sales->sum('loading_quantity'),
                'total_diesel' => $sales->sum('diesel_amount'),
                'total_advance' => $sales->sum('advance_amount'),
                'total_paid' => $sales->sum('paid_amount'),
            ];
            $salesSummary['outstanding'] = $salesSummary['total_amount'] - $salesSummary['total_paid'];

            $regularCollectionsAmount = GatePass::whereDate('date', $date)
                ->where('status', 'completed')
                ->whereNull('client_id')
                ->sum('paid_amount');

            $collections = ClientTransaction::with('client')
                ->whereDate('transaction_date', $date)
                ->where('transaction_type', 'credit')
                ->get();

            $collectionSummary = [
                'total_collected' => $collections->sum('amount') + $regularCollectionsAmount,
            ];

            $pdf = Pdf::loadView('exports.reports.daily', [
                'date' => $date,
                'gatePasses' => $sales,
                'salesSummary' => $salesSummary,
                'collections' => $collections,
                'collectionSummary' => $collectionSummary,
            ]);

            return $pdf->download("daily_report_{$date}.pdf");
        }

        $csvFileName = 'daily_report_' . $date . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $stock = \App\Models\DieselStock::where('date', $date)->first();

        $callback = function () use ($sales, $transactions, $date, $stock) {
            $file = fopen('php://output', 'w');

            // Diesel Stock Section (New: Requirement 1)
            fputcsv($file, ['DIESEL STOCK (TANK) - ' . Carbon::parse($date)->format('d M Y')]);
            fputcsv($file, ['Opening Stock', 'Purchased Liters', 'Total Available', 'Closing Balance']);
            if ($stock) {
                fputcsv($file, [
                    number_format($stock->opening_liters, 2) . ' L',
                    number_format($stock->purchased_liters, 2) . ' L',
                    number_format($stock->total_available, 2) . ' L',
                    number_format($stock->closing_liters, 2) . ' L',
                ]);
            } else {
                fputcsv($file, ['No stock record found', '-', '-', '-']);
            }
            fputcsv($file, []);

            // Diesel Consumption Breakdown (New: Requirement 3)
            $issues = \App\Models\DieselEntry::with('operationalUnit')
                ->whereDate('date', $date)
                ->get();

            if ($issues->count() > 0) {
                fputcsv($file, ['DIESEL CONSUMPTION BREAKDOWN']);
                fputcsv($file, ['Operational Unit', 'Total Liters Issued', 'Records Count']);
                foreach ($issues->groupBy('operational_unit_id') as $summary) {
                    fputcsv($file, [
                        $summary->first()->operationalUnit->name ?? 'N/A',
                        number_format($summary->sum('liters'), 2) . ' L',
                        $summary->count()
                    ]);
                }
                fputcsv($file, []);
            }

            // Sales Section
            fputcsv($file, ['DAILY SALES REPORT - ' . Carbon::parse($date)->format('d M Y')]);
            fputcsv($file, ['GP No', 'Date', 'Vehicle', 'Client', 'Metal', 'Quantity', 'Amount']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->gate_pass_number,
                    $sale->date->format('H:i'),
                    $sale->vehicle->vehicle_number,
                    $sale->client->name ?? 'Cash Sale',
                    $sale->metalType->name,
                    $sale->net_weight . ' CFT',
                    $sale->total_amount
                ]);
            }

            fputcsv($file, ['', '', '', '', '', 'Total Sales', $sales->sum('total_amount')]);
            fputcsv($file, []);

            // Transactions Section
            fputcsv($file, ['TRANSACTIONS / COLLECTIONS']);
            fputcsv($file, ['Client', 'Type', 'Amount', 'Payment Mode', 'Remarks']);

            foreach ($transactions as $txn) {
                fputcsv($file, [
                    $txn->client->name,
                    ucfirst($txn->transaction_type),
                    $txn->amount,
                    $txn->payment_mode,
                    $txn->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportMonthly(Request $request)
    {
        $monthVal = $request->input('month');
        $yearVal = $request->input('year');

        if (is_numeric($monthVal) && $yearVal) {
            $startDate = Carbon::createFromDate($yearVal, $monthVal, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($yearVal, $monthVal, 1)->endOfMonth();
            $month = $startDate->format('Y-m');
            $year = $yearVal;
            $monthNum = $monthVal;
        } elseif ($monthVal) {
            $startDate = Carbon::parse($monthVal)->startOfMonth();
            $endDate = Carbon::parse($monthVal)->endOfMonth();
            $month = $startDate->format('Y-m');
            $year = $startDate->year;
            $monthNum = $startDate->month;
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $month = $startDate->format('Y-m');
            $year = $startDate->year;
            $monthNum = $startDate->month;
        }

        if ($request->input('format') === 'pdf') {
            $dailySales = GatePass::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->selectRaw('DATE(date) as date_only, SUM(total_amount) as total_sales, COUNT(*) as count')
                ->groupByRaw('DATE(date)')
                ->get()
                ->keyBy('date_only');

            $dailyCollections = ClientTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                ->where('transaction_type', 'credit')
                ->selectRaw('DATE(transaction_date) as date_only, SUM(amount) as total_collections')
                ->groupBy('transaction_date')
                ->get()
                ->keyBy('date_only');

            $dailyRegularCollections = GatePass::whereBetween('date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->whereNull('client_id')
                ->selectRaw('DATE(date) as date_only, SUM(paid_amount) as total_regular_collections')
                ->groupByRaw('DATE(date)')
                ->get()
                ->keyBy('date_only');

            $reportData = [];
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $d = $current->toDateString();
                $sales = $dailySales[$d] ?? null;
                $col = $dailyCollections[$d] ?? null;
                $regCol = $dailyRegularCollections[$d] ?? null;

                if ($sales || $col || $regCol) {
                    $reportData[$d] = [
                        'date' => $current->format('d M Y'),
                        'sales' => $sales ? $sales->total_sales : 0,
                        'sales_count' => $sales ? $sales->count : 0,
                        'collections' => ($col ? $col->total_collections : 0) + ($regCol ? $regCol->total_regular_collections : 0),
                    ];
                }
                $current->addDay();
            }

            $pdf = Pdf::loadView('exports.reports.monthly', [
                'reportData' => $reportData,
                'month' => $monthNum,
                'year' => $year,
            ]);
            return $pdf->download("monthly_report_{$month}.pdf");
        }

        $dailySales = GatePass::selectRaw('DATE(date) as date_only, SUM(total_amount) as total_sales, COUNT(*) as trip_count')
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupByRaw('DATE(date)')
            ->get()
            ->keyBy('date_only');

        $expenses = DB::table('crusher_expenses')
            ->selectRaw('DATE(date) as date_only, SUM(amount) as total_expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupByRaw('DATE(date)')
            ->get()
            ->keyBy('date_only');

        $csvFileName = 'monthly_report_' . $month . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($startDate, $endDate, $dailySales, $expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Total Sales', 'Trips', 'Expenses', 'Net']);

            $current = $startDate->copy();
            while ($current <= $endDate) {
                $dateStr = $current->format('Y-m-d');
                $sales = $dailySales[$dateStr]->total_sales ?? 0;
                $trips = $dailySales[$dateStr]->trip_count ?? 0;
                $expense = $expenses[$dateStr]->total_expense ?? 0;

                fputcsv($file, [
                    $current->format('d M Y'),
                    $sales,
                    $trips,
                    $expense,
                    $sales - $expense
                ]);

                $current->addDay();
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCustom(Request $request)
    {
        $query = GatePass::with(['client', 'vehicle', 'metalType'])
            ->where('status', 'completed')
            ->latest();

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        $records = $query->get();

        $csvFileName = 'custom_sales_report.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['GP Number', 'Date', 'Client', 'Vehicle', 'Material', 'Weight', 'Amount']);

            foreach ($records as $row) {
                fputcsv($file, [
                    $row->gate_pass_number,
                    $row->date->format('d-m-Y H:i'),
                    $row->client->name ?? 'Cash',
                    $row->vehicle->vehicle_number,
                    $row->metalType->name,
                    $row->net_weight,
                    $row->total_amount
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSummary(Request $request, $type)
    {
        $query = GatePass::where('status', 'completed');

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $data = match ($type) {
            'metal' => $query->selectRaw('metal_type_id, SUM(net_weight) as total_weight, SUM(total_amount) as total_amount')
                ->groupBy('metal_type_id')
                ->with('metalType')->get(),
            'vehicle' => $query->selectRaw('vehicle_id, COUNT(*) as trips, SUM(net_weight) as total_weight')
                ->groupBy('vehicle_id')
                ->with('vehicle')->get(),
            'client' => $query->selectRaw('client_id, SUM(total_amount) as total_amount')
                ->groupBy('client_id')
                ->with('client')->get(),
            default => collect()
        };

        $csvFileName = $type . '_summary_report.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($data, $type) {
            $file = fopen('php://output', 'w');

            if ($type == 'metal') {
                fputcsv($file, ['Metal Type', 'Total CFT', 'Total Sales']);
                foreach ($data as $row) {
                    fputcsv($file, [$row->metalType->name ?? 'Unknown', $row->total_weight, $row->total_amount]);
                }
            } elseif ($type == 'vehicle') {
                fputcsv($file, ['Vehicle', 'Total Trips', 'Total CFT']);
                foreach ($data as $row) {
                    fputcsv($file, [$row->vehicle->vehicle_number ?? 'Unknown', $row->trips, $row->total_weight]);
                }
            } elseif ($type == 'client') {
                fputcsv($file, ['Client', 'Total Purchase']);
                foreach ($data as $row) {
                    fputcsv($file, [$row->client->name ?? 'Cash Customer', $row->total_amount]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportDistanceReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $format = $request->input('format', 'csv');

        $data = GatePass::with('vehicle')
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('status', 'completed')
            ->where('distance_km', '>', 0)
            ->selectRaw('vehicle_id, SUM(distance_km) as total_km, count(*) as trip_count')
            ->groupBy('vehicle_id')
            ->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.reports.distance', compact('data', 'startDate', 'endDate'));
            return $pdf->download("distance_report_{$startDate}_{$endDate}.pdf");
        }

        $filename = "distance_report_{$startDate}_{$endDate}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Vehicle', 'Trips', 'Total KM']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->vehicle->vehicle_number ?? 'Unknown',
                    $row->trip_count,
                    $row->total_km
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
