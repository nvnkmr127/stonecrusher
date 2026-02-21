<?php

namespace App\Services;

use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class PayrollService
{
    /**
     * Check if a specific date belongs to a locked payroll period.
     * Throws an exception if locked.
     */
    public static function checkLock($date)
    {
        $carbonDate = Carbon::parse($date);
        $month = $carbonDate->month;
        $year = $carbonDate->year;

        if (PayrollPeriod::isLocked($month, $year)) {
            throw ValidationException::withMessages([
                'payroll' => ["The payroll for {$carbonDate->format('F Y')} is locked and cannot be modified."]
            ]);
        }
    }

    /**
     * Lock a payroll period.
     */
    public static function lock($month, $year, $userId, $totalPayable = 0)
    {
        return PayrollPeriod::updateOrCreate(
            ['month' => $month, 'year' => $year],
            [
                'is_locked' => true,
                'locked_at' => now(),
                'locked_by' => $userId,
                'total_payable' => $totalPayable
            ]
        );
    }

    /**
     * Release/Pay a payroll period.
     */
    public static function release($month, $year, $userId, $totalPaid = 0)
    {
        return PayrollPeriod::updateOrCreate(
            ['month' => $month, 'year' => $year],
            [
                'is_released' => true,
                'released_at' => now(),
                'released_by' => $userId,
                'total_paid' => $totalPaid
            ]
        );
    }
}
