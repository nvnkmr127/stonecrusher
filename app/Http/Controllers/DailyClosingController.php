<?php

namespace App\Http\Controllers;

use App\Models\DailyClosing;
use App\Models\GatePass;
use App\Models\ClientTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\DayClosureService;

class DailyClosingController extends Controller
{
    public function index()
    {
        $closings = DailyClosing::with('closedBy')->latest('date')->paginate(10);
        return view('daily_closings.index', compact('closings'));
    }

    public function create(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        if (DayClosureService::isClosed($date)) {
            return redirect()->route('daily-closings.index')->with('error', "Date $date is already closed.");
        }

        $totals = $this->calculateTotals($date);

        return view('daily_closings.create', compact('date', 'totals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'confirm_closing' => 'required|accepted',
            'notes' => 'nullable|string'
        ]);

        $date = $validated['date'];

        if (DayClosureService::isClosed($date)) {
            return back()->with('error', 'Date is already closed.');
        }

        $totals = $this->calculateTotals($date);

        DailyClosing::updateOrCreate(
            ['date' => $date],
            [
                'total_sales' => $totals['total_sales'],
                'total_cash' => $totals['total_cash'],
                'total_expenses' => $totals['total_expenses'],
                'status' => 'closed',
                'closed_by_user_id' => auth()->id(),
                'notes' => $validated['notes'],
            ]
        );

        return redirect()->route('daily-closings.index')->with('success', "Daily Closing for $date completed successfully.");
    }

    public function reopen(Request $request, DailyClosing $dailyClosing)
    {
        // Only Admin
        if (!auth()->user()->hasRole('admin')) {
            return back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $dailyClosing->update([
            'status' => 'reopened',
            'notes' => $dailyClosing->notes . "\n[Reopened by " . auth()->user()->name . " on " . now() . "]: " . $request->reason,
        ]);

        return back()->with('success', 'Date reopened successfully.');
    }

    private function calculateTotals($date)
    {
        // Total Sales: Sum of all COMPLETED GatePasses total_amount
        $totalSales = GatePass::whereDate('date', $date)
            ->where('status', 'completed')
            ->sum('total_amount');

        // Total Cash / Collections: Sum of all ClientTransactions (Credit)
        // This includes payments related to GatePasses AND standalone payments.
        // We assume 'credit' transactions are 'Received Money'.
        $totalCollections = ClientTransaction::whereDate('transaction_date', $date)
            ->where('transaction_type', 'credit')
            ->sum('amount');

        // Total Expenses: (Placeholder if we don't have expenses table yet)
        // If ClientTransactions has 'debit' that is NOT a Sale (e.g. Refund?), we might count it.
        // But SalesService uses 'debit' for SALES.
        // So for now, Expense is 0 unless we have Expense model. 
        // Or maybe check if we have an Expense model? I didn't see one in file list.
        $totalExpenses = 0;

        return [
            'total_sales' => $totalSales,
            'total_cash' => $totalCollections,
            'total_expenses' => $totalExpenses
        ];
    }
}
