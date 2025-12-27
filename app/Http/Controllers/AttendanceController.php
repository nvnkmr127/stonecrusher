<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use App\Models\ActivityLog;

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

        $status = 'present';

        if ($checkIn) {
            $checkInTime = Carbon::parse($checkIn);
            // Ignore date part for comparison, verify same day logic if needed but Carbon::parse of H:i uses today's date
            // We need to compare strict time components.
            $checkInTime = Carbon::createFromTime($checkInTime->hour, $checkInTime->minute, 0);

            if ($checkInTime->gt($shiftStart)) {
                $status = 'late';
            }
        }

        if ($checkOut) {
            $checkOutTime = Carbon::parse($checkOut);
            $checkOutTime = Carbon::createFromTime($checkOutTime->hour, $checkOutTime->minute, 0);

            if ($checkOutTime->lt($shiftEnd)) {
                $status = 'half_day';
            }
        }

        // If late AND early exit? usually half_day takes precedence as it's less than full day
        // Status is already half_day if checkOut < shiftEnd.
        // What if Late but CheckOut is OK? Status is 'late'. Correct.

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
            'status' => 'nullable|in:present,late,half_day,leave,absent', // Made nullable as auto-calc will fill it
            'remarks' => 'nullable|string',
        ]);

        $data = $request->all();

        // Auto-calculate status if check_in is provided
        if (isset($data['check_in'])) {
            $data['status'] = $this->calculateStatus($data['check_in'], $data['check_out'] ?? null);

            // Allow manual override if status was explicitly provided in form? 
            // Requirement says "System automatically determines". 
            // Let's stick to auto-calc for now, or maybe only if status is NOT 'leave' or 'absent' manually set?
            // Use Case 2.1 said "User enters check-in time... Save attendance... Outcome: Employee marked as Present..."
            // Use Case 2.3 says "System automatically determines...".
            // We will enforce auto-calc unless data['status'] corresponds to 'leave' or 'absent' which might be manually set without times?
            // But if times are present, status should reflect them.
            if (in_array($request->status, ['leave', 'absent'])) {
                $data['status'] = $request->status;
            }
        } elseif ($request->status) {
            // If no times but status provided (e.g. absent/leave)
            $data['status'] = $request->status;
        } else {
            $data['status'] = 'absent'; // Default if nothing provided?
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
            'status' => 'nullable|in:present,late,half_day,leave,absent',
            'remarks' => 'required|string',
        ]);

        $data = $request->all();

        // Auto-calculate status
        $checkIn = $request->check_in ?? $attendance->check_in;
        $checkOut = $request->check_out ?? $attendance->check_out;

        if ($checkIn) {
            $calculatedStatus = $this->calculateStatus($checkIn, $checkOut);

            // Apply calculated status unless manual override to leave/absent preferred? 
            // Usually updates should follow strict rules if times are changing.
            if (!in_array($request->status, ['leave', 'absent'])) {
                $data['status'] = $calculatedStatus;
            } else {
                $data['status'] = $request->status;
            }
        }

        $attendance->update($data);

        // Log Activity
        ActivityLog::log(
            $attendance->user_id,
            'attendance_update',
            "Attendance updated by " . auth()->user()->name . ". Reason: " . $request->remarks
        );

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
