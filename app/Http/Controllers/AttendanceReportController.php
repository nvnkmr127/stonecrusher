<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\SalaryAdvance;
use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $employees = User::with([
            'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            },
            'advances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        ])->get();

        $daysInMonth = $startDate->daysInMonth;

        // PAYMENT HOLD LOGIC: Payment Month is current Month + 2
        $payoutDate = $startDate->copy()->addMonths(2);
        $payoutMonthName = $payoutDate->format('F Y');
        $reportData = $this->getPayrollData($month, $year);

        $payrollPeriod = PayrollPeriod::where('month', $month)->where('year', $year)->first();
        $canRelease = now()->greaterThanOrEqualTo($startDate->copy()->addMonths(2));

        return view('attendance.report', compact('reportData', 'month', 'year', 'payoutMonthName', 'payrollPeriod', 'canRelease'));
    }

    private function getPayrollData($month, $year)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $employees = User::with([
            'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            },
            'advances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        ])->get();

        $workingDays = (int) \App\Models\Setting::get('monthly_working_days', 26);

        return $employees->map(function ($employee) use ($month, $year, $workingDays) {
            $present = $employee->attendances->whereIn('status', [\App\Enums\AttendanceStatus::PRESENT, \App\Enums\AttendanceStatus::LATE])->count();
            $late = $employee->attendances->where('status', \App\Enums\AttendanceStatus::LATE)->count();
            $halfDay = $employee->attendances->where('status', \App\Enums\AttendanceStatus::HALF_DAY)->count();
            $absent = $employee->attendances->where('status', \App\Enums\AttendanceStatus::ABSENT)->count();
            $leave = $employee->attendances->where('status', \App\Enums\AttendanceStatus::LEAVE)->count();

            // NEW FORMULA PER 3.2
            $excessLeaveDays = max(0, $leave - 4);
            $deductionDays = $absent + $excessLeaveDays + ($halfDay * 0.5);

            $baseSalary = $employee->base_salary ?? 0;
            $dailyRate = $employee->daily_rate > 0 ? $employee->daily_rate : ($baseSalary > 0 ? $baseSalary / $workingDays : 0);

            $advances = $employee->advances->sum('amount');
            $absentDeduction = $deductionDays * $dailyRate;
            $monthlyNet = $baseSalary - $absentDeduction - $advances;

            $carryForward = $this->calculateCarryForward($employee, $month, $year);
            $totalPayable = $monthlyNet + $carryForward;

            return [
                'user' => $employee,
                'present' => $present,
                'late' => $late,
                'half_day' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'leave_allowed' => 4,
                'leave_used' => min($leave, 4),
                'base_salary' => $baseSalary,
                'advances' => $advances,
                'absent_deduction' => $absentDeduction,
                'monthly_net' => $monthlyNet,
                'carry_forward' => $carryForward,
                'remaining' => $totalPayable,
                'total_attended' => $present + ($halfDay * 0.5),
            ];
        });
    }

    public function lock(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $data = $this->getPayrollData($month, $year);
        $totalPayable = $data->sum('remaining');

        PayrollService::lock($month, $year, auth()->id(), $totalPayable);

        return back()->with('success', 'Payroll for ' . Carbon::create()->month($month)->year($year)->format('F Y') . ' has been locked.');
    }

    public function release(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $data = $this->getPayrollData($month, $year);
        // Only sum positive amounts for payout
        $totalPaid = $data->where('remaining', '>', 0)->sum('remaining');

        PayrollService::release($month, $year, auth()->id(), $totalPaid);

        return back()->with('success', 'Salary for ' . Carbon::create()->month($month)->year($year)->format('F Y') . ' has been released.');
    }

    public function export(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $reportData = $this->getPayrollData($month, $year);
        $payrollPeriod = PayrollPeriod::where('month', $month)->where('year', $year)->first();
        $status = $payrollPeriod ? $payrollPeriod->getStatus() : 'Draft';
        $payoutDate = Carbon::createFromDate($year, $month, 1)->addMonths(2);

        $filename = "payroll_report_{$month}_{$year}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Employee', 'Work Month', 'Payable Month', 'Base Salary', 'Leave Taken', 'Absent', 'Deductions', 'Advances', 'Carry Forward', 'Net Salary', 'Status'];

        $callback = function () use ($reportData, $columns, $month, $year, $payoutDate, $status) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($reportData as $row) {
                fputcsv($file, [
                    $row['user']->name,
                    Carbon::create($year, $month, 1)->format('F Y'),
                    $payoutDate->format('F Y'),
                    $row['base_salary'],
                    $row['leave'],
                    $row['absent'],
                    $row['absent_deduction'],
                    $row['advances'],
                    $row['carry_forward'],
                    $row['remaining'],
                    $status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function daily(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        // Fetch all users and eager load their attendance for the specific date
        $attendances = User::with([
            'attendances' => function ($query) use ($date) {
                $query->whereDate('date', $date);
            }
        ])->get();

        // Prepare data for the view
        $dailyData = $attendances->map(function ($user) {
            $attendance = $user->attendances->first(); // Should be only one per day
            return [
                'user' => $user,
                'check_in' => $attendance ? $attendance->check_in : null,
                'check_out' => $attendance ? $attendance->check_out : null,
                'status' => $attendance ? $attendance->status : 'absent', // Default to absent if no record? Or 'No Record'
                // Actually if no record exists, it might mean they haven't come in yet or are absent.
                // Let's explicitly show 'No Record' or 'Absent' based on business logic. 
                // For report clarity, 'No Record' is safer unless we run a daily closing script to mark absents.
                'status_label' => $attendance ? ucfirst(str_replace('_', ' ', $attendance->status)) : 'No Record',
                'remarks' => $attendance ? $attendance->remarks : null,
            ];
        });

        return view('attendance.daily_report', compact('dailyData', 'date'));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $employees = User::with([
            'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            },
            'advances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        ])->get();

        $daysInMonth = $startDate->daysInMonth;
        $payoutDate = $startDate->copy()->addMonths(2);
        $payoutMonthName = $payoutDate->format('F Y');

        $reportData = $employees->map(function ($employee) use ($daysInMonth, $month, $year) {
            $present = $employee->attendances->whereIn('status', [\App\Enums\AttendanceStatus::PRESENT, \App\Enums\AttendanceStatus::LATE])->count();
            $late = $employee->attendances->where('status', \App\Enums\AttendanceStatus::LATE)->count();
            $halfDay = $employee->attendances->where('status', \App\Enums\AttendanceStatus::HALF_DAY)->count();
            $absent = $employee->attendances->where('status', \App\Enums\AttendanceStatus::ABSENT)->count();
            $leave = $employee->attendances->where('status', \App\Enums\AttendanceStatus::LEAVE)->count();

            // NEW FORMULA PER 3.2
            $excessLeaveDays = max(0, $leave - 4);
            $deductionDays = $absent + $excessLeaveDays + ($halfDay * 0.5);

            $baseSalary = $employee->base_salary ?? 0;
            $workingDays = (int) \App\Models\Setting::get('monthly_working_days', 26);
            $dailyRate = $employee->daily_rate > 0 ? $employee->daily_rate : ($baseSalary > 0 ? $baseSalary / $workingDays : 0);

            $advances = $employee->advances->sum('amount');
            $absentDeduction = $deductionDays * $dailyRate;
            $monthlyNet = $baseSalary - $absentDeduction - $advances;

            $carryForward = $this->calculateCarryForward($employee, $month, $year);
            $totalPayable = $monthlyNet + $carryForward;

            return [
                'user' => $employee,
                'present' => $present,
                'late' => $late,
                'half_day' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'base_salary' => $baseSalary,
                'advances' => $advances,
                'absent_deduction' => $absentDeduction,
                'monthly_net' => $monthlyNet,
                'carry_forward' => $carryForward,
                'remaining' => $totalPayable,
                'total_attended' => $present + ($halfDay * 0.5),
            ];
        });

        $pdf = Pdf::loadView('exports.attendance.report', compact('reportData', 'month', 'year', 'payoutMonthName'));
        return $pdf->download("attendance_report_{$month}_{$year}.pdf");
    }

    private function calculateCarryForward($employee, $month, $year)
    {
        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        // Find earliest record for this employee
        $earliestAttendance = \App\Models\Attendance::where('user_id', $employee->id)->orderBy('date')->first();
        $earliestAdvance = \App\Models\SalaryAdvance::where('user_id', $employee->id)->orderBy('date')->first();

        $startDate = null;
        if ($earliestAttendance && $earliestAdvance) {
            $dateA = Carbon::parse($earliestAttendance->date);
            $dateB = Carbon::parse($earliestAdvance->date);
            $startDate = $dateA->lt($dateB) ? $dateA : $dateB;
        } elseif ($earliestAttendance) {
            $startDate = Carbon::parse($earliestAttendance->date);
        } elseif ($earliestAdvance) {
            $startDate = Carbon::parse($earliestAdvance->date);
        }

        if (!$startDate)
            return 0;

        $iterator = Carbon::parse($startDate)->startOfMonth();
        $balance = 0;
        $workingDays = (int) \App\Models\Setting::get('monthly_working_days', 26);

        while ($iterator->lt($currentDate)) {
            $m = $iterator->month;
            $y = $iterator->year;
            $daysInM = $iterator->daysInMonth;

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

            // Check if month was released
            $period = \App\Models\PayrollPeriod::where('month', $m)->where('year', $y)->first();
            if ($period && $period->is_released) {
                // If released, we assume positive net was paid. Negative net stays as debt.
                $balance = min(0, $net);
            } else {
                // If not released, everything carries forward (unpaid salary or debt)
                $balance = $net;
            }

            $iterator->addMonth();
        }

        return $balance;
    }
}
