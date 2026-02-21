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

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $parsedDate = \Carbon\Carbon::parse($value)->startOfDay();
                    if (
                        Attendance::where('user_id', $request->user_id)
                            ->where('date', $parsedDate)
                            ->exists()
                    ) {
                        $fail('Attendance for this employee has already been recorded for this date.');
                    }
                },
            ],
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\AttendanceStatus::class)],
            'remarks' => 'nullable|string',
        ]);

        \App\Services\DayClosureService::checkAllowed($request->date);
        \App\Services\PayrollService::checkLock($request->date);

        Attendance::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Attendance $attendance)
    {
        if (request()->ajax()) {
            return response()->json($attendance);
        }
        $users = User::all();
        return view('attendance.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\AttendanceStatus::class)],
            'remarks' => 'nullable|string',
        ]);

        \App\Services\DayClosureService::checkAllowed($request->date);
        \App\Services\PayrollService::checkLock($request->date);

        $attendance->update([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'status' => $request->status,
            'remarks' => $request->remarks,
        ]);

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

        $employees = User::where('is_active', true)
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
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => ['required', \Illuminate\Validation\Rule::enum(\App\Enums\AttendanceStatus::class)],
        ]);

        \App\Services\DayClosureService::checkAllowed($request->date);
        \App\Services\PayrollService::checkLock($request->date);

        $parsedDate = \Carbon\Carbon::parse($request->date)->startOfDay();

        $attendance = Attendance::updateOrCreate(
            ['user_id' => $request->user_id, 'date' => $parsedDate],
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

        $employees = User::where('is_active', true)
            ->with([
                'attendances' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('date', [$startDate, $endDate]);
                }
            ])
            ->get();

        return view('attendance.calendar', compact('employees', 'month', 'year', 'daysInMonth', 'startDate'));
    }
}
