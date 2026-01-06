<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
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
            }
        ])->get();

        $reportData = $employees->map(function ($employee) {
            $present = $employee->attendances->where('status', 'present')->count();
            $late = $employee->attendances->where('status', 'late')->count();
            $halfDay = $employee->attendances->where('status', 'half_day')->count();
            // Absent is tricky: it's either an 'absent' record OR no record for a working day.
            // For now, let's count explicit 'absent' status records + maybe days without records?
            // Actually, Requirement says "Absent days".
            // Let's stick to explicitly marked 'absent' OR leave for now, or just status counts.
            // If we want "No Record" as absent, we need to know total working days.

            $absent = $employee->attendances->where('status', 'absent')->count();
            $leave = $employee->attendances->where('status', 'leave')->count();

            // Total working days = count of attendance records? Or days in month? 
            // If we assume every day is a working day except Sundays, we could calculate potential working days.
            // But let's stick to summarizing the *records* we have plus maybe a simple count.

            return [
                'user' => $employee,
                'present' => $present + $late, // Late is technically present but late
                'late' => $late,
                'half_day' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'total_attended' => $present + $late + $halfDay,
            ];
        });

        return view('attendance.report', compact('reportData', 'month', 'year'));
    }

    public function export(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $employees = User::with([
            'attendances' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }
        ])->get();

        $filename = "attendance_report_{$month}_{$year}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Employee', 'Present (Days)', 'Late (Days)', 'Half Day', 'Absent', 'Leave'];

        $callback = function () use ($employees, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($employees as $employee) {
                $present = $employee->attendances->whereIn('status', ['present', 'late'])->count();
                $late = $employee->attendances->where('status', 'late')->count();
                $halfDay = $employee->attendances->where('status', 'half_day')->count();
                $absent = $employee->attendances->where('status', 'absent')->count();
                $leave = $employee->attendances->where('status', 'leave')->count();

                fputcsv($file, [
                    $employee->name,
                    $present,
                    $late,
                    $halfDay,
                    $absent,
                    $leave
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
            }
        ])->get();

        $reportData = $employees->map(function ($employee) {
            $present = $employee->attendances->where('status', 'present')->count();
            $late = $employee->attendances->where('status', 'late')->count();
            $halfDay = $employee->attendances->where('status', 'half_day')->count();
            $absent = $employee->attendances->where('status', 'absent')->count();
            $leave = $employee->attendances->where('status', 'leave')->count();

            return [
                'user' => $employee,
                'present' => $present + $late, // Late is technically present but late
                'late' => $late,
                'half_day' => $halfDay,
                'absent' => $absent,
                'leave' => $leave,
                'total_attended' => $present + $late + $halfDay,
            ];
        });

        $pdf = Pdf::loadView('exports.attendance.report', compact('reportData', 'month', 'year'));
        return $pdf->download("attendance_report_{$month}_{$year}.pdf");
    }
}
