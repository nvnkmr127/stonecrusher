<?php

namespace App\Services;

use App\Models\DailyClosing;
use Carbon\Carbon;

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
     * Ideally used in Controllers.
     */
    public static function checkAllowed($date)
    {
        if (self::isClosed($date)) {
            abort(403, 'Transactions for this date are locked due to Daily Closing.');
        }
    }
}
