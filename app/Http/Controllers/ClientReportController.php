<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientReportController extends Controller
{
    public function index()
    {
        // Eager load transactions to allow efficient balance calculation
        $clients = Client::with('transactions')->get();

        // Calculate totals for summary
        $totalSales = 0;
        $totalAdvances = 0;
        $totalOutstanding = 0; // Net receivables

        foreach ($clients as $client) {
            $credit = $client->transactions->where('transaction_type', 'credit')->sum('amount');
            $debit = $client->transactions->where('transaction_type', 'debit')->sum('amount');

            $client->total_credit = $credit;
            $client->total_debit = $debit;
            // Balance defined in Model: Credit - Debit. 
            // If Balance is negative, it's Outstanding (Due).
            // If Balance is positive, it's Advance.

            $totalSales += $debit;
            $totalAdvances += $credit;
            if ($client->balance < 0) {
                $totalOutstanding += abs($client->balance);
            }
        }

        return view('clients.reports.outstanding', compact('clients', 'totalSales', 'totalAdvances', 'totalOutstanding'));
    }

    public function export()
    {
        $clients = Client::with('transactions')->get();

        $csvFileName = 'client_outstanding_report_' . date('Y-m-d_H-i') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Client Name', 'Phone', 'Total Sales (Debit)', 'Total Advance (Credit)', 'Net Balance', 'Status'];

        $callback = function () use ($clients, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($clients as $client) {
                $balance = $client->balance;
                $status = $balance >= 0 ? 'Advance' : 'Outstanding';

                fputcsv($file, [
                    $client->name,
                    $client->phone,
                    $client->transactions->where('transaction_type', 'debit')->sum('amount'), // Sales
                    $client->transactions->where('transaction_type', 'credit')->sum('amount'), // Advances
                    abs($balance),
                    $status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function exportPdf()
    {
        $clients = Client::with('transactions')->get();

        $totalSales = 0;
        $totalAdvances = 0;
        $totalOutstanding = 0;

        foreach ($clients as $client) {
            $credit = $client->transactions->where('transaction_type', 'credit')->sum('amount');
            $debit = $client->transactions->where('transaction_type', 'debit')->sum('amount');

            $client->total_credit = $credit;
            $client->total_debit = $debit; // Store for view

            $totalSales += $debit;
            $totalAdvances += $credit;
            if ($client->balance < 0) {
                $totalOutstanding += abs($client->balance);
            }
        }

        $pdf = Pdf::loadView('exports.clients.outstanding', compact('clients', 'totalSales', 'totalAdvances', 'totalOutstanding'));
        return $pdf->download('client_outstanding_report_' . date('Y-m-d_H-i') . '.pdf');
    }
}
