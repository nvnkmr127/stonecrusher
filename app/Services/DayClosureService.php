<?php

namespace App\Services;

use App\Models\DailyClosing;
use App\Models\GatePass;
use App\Models\ClientTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DayClosureService
{
    /**
     * Check if the given date is closed.
     *
     * @param string|Carbon $date
     * @return bool
     */
    public static function isClosed($date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return DailyClosing::where('date', $date)->where('status', 'closed')->exists();
    }

    /**
     * Check if the given date is closed and throw an exception or return error response if so.
     */
    public static function checkAllowed($date)
    {
        if (self::isClosed($date)) {
            abort(403, 'Transactions for this date are locked due to Daily Closing.');
        }
    }

    /**
     * Calculate all totals for a specific date (Sales, Collections, etc).
     */
    public static function getTotalsForDate($date)
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');

        // Total Sales: Sum of all COMPLETED GatePasses total_amount
        $totalSales = GatePass::whereDate('date', $dateStr)
            ->where('status', 'completed')
            ->sum('total_amount');

        // Total Collections: Sum of all ClientTransactions (Credit)
        $totalCollections = ClientTransaction::whereDate('transaction_date', $dateStr)
            ->where('transaction_type', 'credit')
            ->sum('amount');

        $totalExpenses = 0; // Placeholder

        return [
            'total_sales' => (float) $totalSales,
            'total_cash' => (float) $totalCollections,
            'total_expenses' => (float) $totalExpenses
        ];
    }

    /**
     * Perform the actual closing for a date.
     */
    public static function perform($date, $userId = null, $notes = 'Automatic System Closure')
    {
        $dateStr = Carbon::parse($date)->format('Y-m-d');

        if (self::isClosed($dateStr)) {
            return null;
        }

        $totals = self::getTotalsForDate($dateStr);

        return DailyClosing::updateOrCreate(
            ['date' => $dateStr],
            [
                'total_sales' => $totals['total_sales'],
                'total_cash' => $totals['total_cash'],
                'total_expenses' => $totals['total_expenses'],
                'status' => 'closed',
                'closed_by_user_id' => $userId,
                'notes' => $notes,
            ]
        );
    }
}
