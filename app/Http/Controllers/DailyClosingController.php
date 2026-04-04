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

        $totals = DayClosureService::getTotalsForDate($date);

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

        $closing = DayClosureService::perform($date, auth()->id(), $validated['notes'] ?? 'Manual Closing');

        if ($closing) {
            return redirect()->route('daily-closings.index')->with('success', "Daily Closing for $date completed successfully.");
        } else {
            return back()->with('error', 'Closing failed or date is already closed.');
        }
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
}
