<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
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

        $query = User::with([
            'attendances' => function ($query) use ($date) {
                $query->whereDate('date', $date);
            }
        ]);

        if ($request->has('user_id') && $request->user_id) {
            $query->where('id', $request->user_id);
        }

        $employees = $query->get();
        $users = User::all(); // For filter dropdown

        return view('attendance.index', compact('employees', 'users', 'date'));
    }

    public function create()
    {
        $users = User::all();
        return view('attendance.create', compact('users'));
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
            // Ignore date part for comparison
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
            'user_id' => 'required|exists:users,id',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if (
                        Attendance::where('user_id', $request->user_id)
                            ->where('date', $value)
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

        $data = $request->all();

        // Auto-calculate status if check_in is provided
        if (isset($data['check_in'])) {
            $data['status'] = $this->calculateStatus($data['check_in'], $data['check_out'] ?? null);

            // Allow manual override only for Leave/Absent
            if (in_array($request->status, [\App\Enums\AttendanceStatus::LEAVE->value, \App\Enums\AttendanceStatus::ABSENT->value])) {
                $data['status'] = $request->status;
            }
        } elseif ($request->status) {
            // If no times but status provided (e.g. absent/leave)
            $data['status'] = $request->status;
        } else {
            $data['status'] = \App\Enums\AttendanceStatus::ABSENT->value;
        }

        Attendance::create($data);

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Attendance $attendance)
    {
        $users = User::all();
        return view('attendance.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request, $attendance) {
                    // Check if check-in exists (either in request or DB)
                    $checkInTime = $request->check_in ?? $attendance->check_in;

                    if (!$checkInTime && $value) {
                        $fail('Check-out cannot be recorded without a check-in time.');
                        return;
                    }

                    // Check if check-out is after check-in
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
        \App\Services\DayClosureService::checkAllowed($attendance->date);

        $data = $request->all();

        // Auto-calculate status
        $checkIn = $request->check_in ?? $attendance->check_in;
        $checkOut = $request->check_out ?? $attendance->check_out;

        if ($checkIn) {
            $calculatedStatus = $this->calculateStatus($checkIn, $checkOut);

            if (!in_array($request->status, [\App\Enums\AttendanceStatus::LEAVE->value, \App\Enums\AttendanceStatus::ABSENT->value])) {
                $data['status'] = $calculatedStatus;
            } else {
                $data['status'] = $request->status;
            }
        }

        $attendance->update($data);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        \App\Services\DayClosureService::checkAllowed($attendance->date);

        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
