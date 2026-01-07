<?php

namespace App\Http\Controllers;

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
use App\Services\ReportExportService;

class ReportController extends Controller
{
    /**
     * Reports Dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Daily Detailed Report
     */
    public function daily(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // 1. Sales (Completed Gate Passes)
        $gatePasses = GatePass::with(['client', 'vehicle', 'metalType'])
            ->whereDate('date', $date)
            ->where('status', 'completed')
            ->get();

        $salesSummary = [
            'count' => $gatePasses->count(),
            'total_amount' => $gatePasses->sum('total_amount'),
            'total_volume' => $gatePasses->sum('loading_quantity'),
            // Specifics for Use Case 6.1
            'total_diesel' => $gatePasses->sum('diesel_amount'),
            'total_advance' => $gatePasses->sum('advance_amount'),
            'total_paid' => $gatePasses->sum('paid_amount'), // Cash collected at gate
        ];

        // Outstanding for today's sales (Total Sales - (Paid + Diesel + Advances?))
        // "Paid" typically includes Advance in some flows, but usually:
        // Outstanding = Total Amount - Paid Amount.
        // If Diesel and Advance are deduced from Total Amount payable to transporter? 
        // No, GatePass logic: 
        // Total Amount = Qty * Rate.
        // Paid Amount = What client paid? Or what we paid to truck?
        // Wait, GatePass is a SALE usually in this context (Crusher selling to Client).
        // BUT if it's "Diesel Cost", that implies EXPENSE. 
        // Does the crusher give diesel to the truck? 
        // In Use Case 6.1 description: "Diesel cost", "Advances adjusted".
        // This sounds like Transport/Vehicle side accounting? 
        // Or is it "Client paid via Diesel"?
        // Let's assume Standard Crusher context:
        // We sell material. Truck comes. We might fill diesel for the truck and deduct from... ? 
        // If the truck is ours, it's an expense.
        // If the truck is 3rd party, we might PAY them transport.
        // But "Sales value" is definitely Income.
        // "Outstanding amount" usually refers to Client Balance.
        // "Advances adjusted": Maybe Client paid advance earlier?

        // Let's stick to the Field Names available in GatePass model:
        // diesel_amount, advance_amount.
        // Let's display them as aggregated totals.
        // Outstanding logic: 
        // If GatePass has `payment_status` logic, usually Outstanding = Total - Paid.
        $salesSummary['outstanding'] = $salesSummary['total_amount'] - $salesSummary['total_paid'];

        // Metal-wise Breakdown for Use Case 6.1
        $metalStats = $gatePasses->groupBy('metal_type_id')->map(function ($rows) {
            return [
                'name' => $rows->first()->metalType->name ?? 'Unknown',
                'count' => $rows->count(),
                'quantity' => $rows->sum('loading_quantity'), // or net_weight if qty 0?
                'amount' => $rows->sum('total_amount'),
            ];
        });

        // 2. Collections (Client Transactions - Credit)
        $collections = ClientTransaction::with('client')
            ->whereDate('transaction_date', $date)
            ->where('transaction_type', 'credit')
            ->get();

        $collectionSummary = [
            'total_collected' => $collections->sum('amount'),
            'by_mode' => $collections->groupBy('payment_mode')->map->sum('amount'),
        ];

        return view('reports.daily', compact('date', 'gatePasses', 'salesSummary', 'metalStats', 'collections', 'collectionSummary'));
    }

    public function exportDaily(Request $request, ReportExportService $exportService)
    {
        return $exportService->exportDaily($request);
    }

    /**
     * Monthly Report
     */
    public function monthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Groupped by Date
        $dailySales = GatePass::whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('DATE(date) as date, SUM(total_amount) as total_sales, COUNT(*) as count')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $dailyCollections = ClientTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->where('transaction_type', 'credit')
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total_collections')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        // Merge dates
        $reportData = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $d = $current->toDateString();
            $sales = $dailySales[$d] ?? null;
            $col = $dailyCollections[$d] ?? null;

            if ($sales || $col) {
                $reportData[$d] = [
                    'sales' => $sales ? $sales->total_sales : 0,
                    'sales_count' => $sales ? $sales->count : 0,
                    'collections' => $col ? $col->total_collections : 0,
                ];
            }
            $current->addDay();
        }

        $totalSales = collect($reportData)->sum('sales');
        $totalCollections = collect($reportData)->sum('collections');

        return view('reports.monthly', compact('reportData', 'month', 'year', 'totalSales', 'totalCollections'));
    }

    public function exportMonthly(Request $request, ReportExportService $exportService)
    {
        return $exportService->exportMonthly($request);
    }

    /**
     * Custom Date Range Report
     */
    public function custom(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $sales = GatePass::with(['client', 'vehicle', 'metalType'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderBy('date')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalCount = $sales->count();

        return view('reports.custom', compact('sales', 'startDate', 'endDate', 'totalSales', 'totalCount'));
    }

    public function exportCustom(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $format = $request->input('format', 'csv');

        $sales = GatePass::with(['client', 'vehicle', 'metalType'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderBy('date')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalCount = $sales->count();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.reports.custom', compact('sales', 'startDate', 'endDate', 'totalSales', 'totalCount'));
            return $pdf->download("custom_report_{$startDate}_{$endDate}.pdf");
        }

        $filename = "custom_report_{$startDate}_{$endDate}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'GP Number', 'Client', 'Vehicle', 'Metal', 'Qty', 'Amount']);

            foreach ($sales as $gp) {
                fputcsv($file, [
                    $gp->date->toDateString(),
                    $gp->gate_pass_number,
                    $gp->client->name ?? 'N/A',
                    $gp->vehicle->vehicle_number,
                    $gp->metalType->name,
                    $gp->loading_quantity,
                    $gp->total_amount
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Breakdown Reports (Metal, Client, Vehicle)
     */
    public function summary(Request $request, $type)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = [];
        $title = ucfirst($type) . ' Summary';

        if ($type === 'metal') {
            $data = GatePass::with('metalType')
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->selectRaw('metal_type_id, SUM(total_amount) as total_sales, SUM(loading_quantity) as total_qty, COUNT(*) as count')
                ->groupBy('metal_type_id')
                ->get();
        } elseif ($type === 'client') {
            $data = GatePass::with('client')
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->selectRaw('client_id, SUM(total_amount) as total_sales, COUNT(*) as count, SUM(transport_cost) as transport')
                ->groupBy('client_id')
                ->orderByDesc('total_sales')
                ->get();
        } elseif ($type === 'vehicle') {
            $data = GatePass::with('vehicle')
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->selectRaw('vehicle_id, SUM(total_amount) as total_sales, COUNT(*) as count, SUM(distance_km) as total_km')
                ->groupBy('vehicle_id')
                ->orderByDesc('total_sales') // Or total_km
                ->get();
        } else {
            abort(404);
        }

        return view('reports.summary', compact('data', 'type', 'startDate', 'endDate', 'title'));
    }

    public function exportSummary(Request $request, $type, ReportExportService $exportService)
    {
        return $exportService->exportSummary($request, $type);
    }
    /**
     * Outstanding & Advance Report
     */
    public function outstanding(Request $request)
    {
        // Fetch all clients with their transactions
        // We need to calculate balance = Sum(Credit) - Sum(Debit)
        // Note: In Client Transaction model:
        // Credit = Payment from Client (or Opening Balance Credit) -> This increases 'Advance' or reduces 'Outstanding'
        // Debit = Sales (GatePass) -> This increases 'Outstanding'

        // Client::balance attribute logic: Credit - Debit.
        // Positive = Advance. Negative = Outstanding.

        // Optimization: We can do calculation in DB or Fetch all and process in PHP.
        // Since client list is likely manageable, processing in PHP is fine for now,
        // but eager loading transactions is heavy.
        // Better: Use withSum.

        $clients = Client::withSum([
            'transactions as total_credit' => function ($query) {
                $query->where('transaction_type', 'credit');
            }
        ], 'amount')
            ->withSum([
                'transactions as total_debit' => function ($query) {
                    $query->where('transaction_type', 'debit');
                }
            ], 'amount')
            ->get();

        $outstandingClients = collect();
        $advanceClients = collect();

        foreach ($clients as $client) {
            $balance = ($client->total_credit ?? 0) - ($client->total_debit ?? 0);
            $client->current_balance = $balance; // Add temporary attribute

            if ($balance < 0) {
                $outstandingClients->push($client);
            } elseif ($balance > 0) {
                $advanceClients->push($client);
            }
        }

        // Sort by magnitude
        $outstandingClients = $outstandingClients->sortBy('current_balance'); // Ascending (Largest negative first)
        $advanceClients = $advanceClients->sortByDesc('current_balance'); // Descending (Largest positive first)

        return view('reports.outstanding', compact('outstandingClients', 'advanceClients'));
    }
}
