<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Models\SalaryAdvance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollLiabilityController extends Controller
{
    public function index()
    {
        // 1. Total Pending Salary (Locked but not Paid)
        $pendingPeriods = PayrollPeriod::where('is_locked', true)
            ->where('is_released', false)
            ->get();

        $totalPendingSalary = $pendingPeriods->sum('total_payable');

        // 2. Upcoming Release Month
        // Find the locked, unpaid period with the earliest payout date.
        // Payout date is work_month + 2 months. 
        // We want the one that will be released soonest.
        $nextRelease = $pendingPeriods->sortBy(function ($p) {
            return Carbon::create($p->year, $p->month, 1)->addMonths(2);
        })->first();

        $upcomingReleaseMonth = $nextRelease
            ? Carbon::create($nextRelease->year, $nextRelease->month, 1)->addMonths(2)->format('F Y')
            : 'None';

        // 3. Total Advances Outstanding
        // This is tricky. Advances are technically settled per month.
        // But "Outstanding" usually means advances given in months that haven't been released yet.
        // Or advances given in the current DRAFT month.
        $unpaidMonths = PayrollPeriod::where('is_released', false)->get(['month', 'year']);

        // Let's sum all advances in months that are not yet released.
        // And also include advances in the current month (which likely doesn't have a PayrollPeriod record yet).
        // A better way: All advances minus those in released months.
        $releasedMonths = PayrollPeriod::where('is_released', true)->get(['month', 'year']);

        $totalAdvancesOutstanding = SalaryAdvance::where(function ($query) use ($releasedMonths) {
            foreach ($releasedMonths as $rm) {
                $query->whereNot(function ($q) use ($rm) {
                    $q->whereMonth('date', $rm->month)->whereYear('date', $rm->year);
                });
            }
        })->sum('amount');

        // 4. Negative Carry Forward Cases
        // We need to check all employees for their current balance.
        $employees = User::where('base_salary', '>', 0)->get();
        $negativeCases = [];

        // I'll borrow the calculateCarryForward logic from AttendanceReportController
        // Since I can't easily call it from here, I'll instantiate the controller or move logic to a service later.
        // For now, I'll use the controller instance as a helper if needed, but it's cleaner to just do a quick loop.
        $reportCtrl = new AttendanceReportController();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        foreach ($employees as $employee) {
            // Check carry forward at the START of the current month
            // This represents the debt they brought into this month.
            $cf = $this->calculateEmployeeBalance($employee, $currentMonth, $currentYear);
            if ($cf < 0) {
                $negativeCases[] = [
                    'user' => $employee,
                    'balance' => $cf
                ];
            }
        }

        return view('reports.liability', compact(
            'totalPendingSalary',
            'upcomingReleaseMonth',
            'totalAdvancesOutstanding',
            'negativeCases'
        ));
    }

    private function calculateEmployeeBalance($employee, $month, $year)
    {
        // Re-implementing simplified carry forward logic for liability report
        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $startDate = User::find($employee->id)->created_at ?? Carbon::parse('2024-01-01');

        $iterator = Carbon::parse($startDate)->startOfMonth();
        $balance = 0;
        $workingDays = (int) \App\Models\Setting::get('monthly_working_days', 26);

        while ($iterator->lt($currentDate)) {
            $m = $iterator->month;
            $y = $iterator->year;

            $attendances = \App\Models\Attendance::where('user_id', $employee->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->get();

            $advs = \App\Models\SalaryAdvance::where('user_id', $employee->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->sum('amount');

            $leave = $attendances->where('status', \App\Enums\AttendanceStatus::LEAVE->value)->count();
            $absent = $attendances->where('status', \App\Enums\AttendanceStatus::ABSENT->value)->count();
            $halfDay = $attendances->where('status', \App\Enums\AttendanceStatus::HALF_DAY->value)->count();

            $excessLeave = max(0, $leave - 4);
            $deductionDays = $absent + $excessLeave + ($halfDay * 0.5);

            $baseSalary = $employee->base_salary ?? 0;
            $dailyRate = $employee->daily_rate > 0 ? $employee->daily_rate : ($baseSalary > 0 ? $baseSalary / $workingDays : 0);

            $monthlyEarning = $baseSalary - ($deductionDays * $dailyRate);
            $net = $monthlyEarning - $advs + $balance;

            $period = \App\Models\PayrollPeriod::where('month', $m)->where('year', $y)->first();
            if ($period && $period->is_released) {
                $balance = min(0, $net);
            } else {
                $balance = $net;
            }

            $iterator->addMonth();
        }

        return $balance;
    }
}
