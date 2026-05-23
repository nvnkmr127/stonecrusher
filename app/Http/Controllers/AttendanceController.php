<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Attendance::class, 'attendance');
    }

    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $query = Employee::with([
            'attendances' => function ($query) use ($date) {
                $query->whereDate('date', $date);
            }
        ]);

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('id', $request->employee_id);
        }

        $employees = $query->get();
        $employeesList = Employee::all(); // For filter dropdown

        return view('attendance.index', compact('employees', 'employeesList', 'date'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('attendance.create', compact('employees'));
    }

    private function calculateStatus($checkIn, $checkOut)
    {
        $shiftStartStr = \App\Models\Setting::get('attendance_shift_start', '09:30');
        $shiftEndStr = \App\Models\Setting::get('attendance_shift_end', '18:30');

        $shiftStart = Carbon::createFromTimeString($shiftStartStr);
        $shiftEnd = Carbon::createFromTimeString($shiftEndStr);

        $status = \App\Enums\AttendanceStatus::PRESENT->value;

        if ($checkIn) {
            $checkInTime = Carbon::parse($checkIn);
            $checkInTime = Carbon::createFromTime($checkInTime->hour, $checkInTime->minute, 0);

            if ($checkInTime->gt($shiftStart)) {
                $status = \App\Enums\AttendanceStatus::LATE->value;
            }
        }

        if ($checkOut) {
            $checkOutTime = Carbon::parse($checkOut);
            $checkOutTime = Carbon::createFromTime($checkOutTime->hour, $checkOutTime->minute, 0);

            if ($checkOutTime->lt($shiftEnd)) {
                $status = \App\Enums\AttendanceStatus::HALF_DAY->value;
            }
        }

        return $status;
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $parsedDate = \Carbon\Carbon::parse($value)->startOfDay();
                    if (
                        Attendance::where('employee_id', $request->employee_id)
                            ->where('date', $parsedDate)
                            ->exists()
                    ) {
                        $fail('Attendance for this employee has already been recorded for this date.');
                    }
                },
            ],
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => ['nullable', \Illuminate\Validation\Rule::enum(\App\Enums\AttendanceStatus::class)],
            'remarks' => 'nullable|string',
        ]);

        \App\Services\DayClosureService::checkAllowed($request->date);
        \App\Services\PayrollService::checkLock($request->date);

        $data = [
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'remarks' => $request->remarks,
        ];

        // Auto-calculate status if check_in is provided
        if ($request->filled('check_in')) {
            $calculatedStatus = $this->calculateStatus($request->check_in, $request->check_out);

            // Allow manual override only for Leave/Absent
            if ($request->status && in_array($request->status, [\App\Enums\AttendanceStatus::LEAVE->value, \App\Enums\AttendanceStatus::ABSENT->value])) {
                $data['status'] = $request->status;
            } else {
                $data['status'] = $calculatedStatus;
            }
        } elseif ($request->status) {
            $data['status'] = $request->status;
        } else {
            $data['status'] = \App\Enums\AttendanceStatus::ABSENT->value;
        }

        Attendance::create($data);

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Attendance $attendance)
    {
        if (request()->ajax()) {
            return response()->json($attendance);
        }
        $employees = Employee::all();
        return view('attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request, $attendance) {
                    $checkInTime = $request->check_in ?? $attendance->check_in;
                    if (!$checkInTime && $value) {
                        $fail('Check-out cannot be recorded without a check-in time.');
                        return;
                    }
                    if ($checkInTime && $value) {
                        if ($value <= $checkInTime) {
                            $fail('Check-out time must be after check-in time.');
                        }
                    }
                },
            ],
            'status' => ['nullable', \Illuminate\Validation\Rule::enum(\App\Enums\AttendanceStatus::class)],
            'remarks' => 'required|string',
        ]);

        \App\Services\DayClosureService::checkAllowed($request->date);
        \App\Services\PayrollService::checkLock($request->date);

        $data = [
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'remarks' => $request->remarks,
        ];

        $checkIn = $request->check_in ?? $attendance->check_in;
        $checkOut = $request->check_out ?? $attendance->check_out;

        if ($checkIn) {
            $calculatedStatus = $this->calculateStatus($checkIn, $checkOut);

            if ($request->status && in_array($request->status, [\App\Enums\AttendanceStatus::LEAVE->value, \App\Enums\AttendanceStatus::ABSENT->value])) {
                $data['status'] = $request->status;
            } else {
                $data['status'] = $calculatedStatus;
            }
        } elseif ($request->status) {
            $data['status'] = $request->status;
        }

        $attendance->update($data);

        // Log update activity
        activity()
            ->performedOn($attendance)
            ->causedBy(auth()->user())
            ->event('attendance_update')
            ->log("Attendance updated. Remarks: " . $request->remarks);

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        \App\Services\DayClosureService::checkAllowed($attendance->date);
        \App\Services\PayrollService::checkLock($attendance->date);

        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }

    public function bulk(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $employees = Employee::where('is_active', true)
            ->with([
                'attendances' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
            ])
            ->get();

        return view('attendance.bulk', compact('employees', 'month', 'year', 'daysInMonth', 'startDate'));
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\AttendanceStatus::class)],
        ]);

        \App\Services\DayClosureService::checkAllowed($request->date);
        \App\Services\PayrollService::checkLock($request->date);

        $parsedDate = \Carbon\Carbon::parse($request->date)->startOfDay();

        $attendance = Attendance::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $parsedDate],
            ['status' => $request->status]
        );

        return response()->json([
            'success' => true,
            'status' => $attendance->status
        ]);
    }

    public function bulkStore(Request $request)
    {
        // Keep fallback support
        return redirect()->route('attendance.index');
    }

    public function calendar(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;

        $employees = Employee::where('is_active', true)
            ->with([
                'attendances' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
            ])
            ->get();

        return view('attendance.calendar', compact('employees', 'month', 'year', 'daysInMonth', 'startDate'));
    }
}
