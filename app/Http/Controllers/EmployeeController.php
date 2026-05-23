<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\OperationalUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with('operationalUnit', 'user');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by operational unit
        if ($request->filled('operational_unit_id')) {
            $query->where('operational_unit_id', $request->operational_unit_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $employees = $query->latest()->paginate(15);
        $operationalUnits = OperationalUnit::all();
        
        $roles = [
            'office' => 'Office Staff',
            'driver' => 'Driver',
            'operational' => 'Operational Staff',
            'operator' => 'Operator',
        ];

        return view('employees.index', compact('employees', 'operationalUnits', 'roles'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $operationalUnits = OperationalUnit::all();
        
        // Get users not linked to any employee
        $users = User::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('employees')
                  ->whereColumn('employees.user_id', 'users.id');
        })->get();

        $roles = [
            'office' => 'Office Staff',
            'driver' => 'Driver',
            'operational' => 'Operational Staff',
            'operator' => 'Operator',
        ];

        return view('employees.create', compact('operationalUnits', 'users', 'roles'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:office,driver,operational,operator',
            'base_salary' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'operational_unit_id' => 'nullable|exists:operational_units,id',
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('employees', 'user_id'),
            ],
            'is_active' => 'boolean',
        ]);

        Employee::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'base_salary' => $validated['base_salary'] ?? 0,
            'daily_rate' => $validated['daily_rate'] ?? 0,
            'operational_unit_id' => $validated['operational_unit_id'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully!');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $startDate = $employee->created_at ?? \Carbon\Carbon::parse('2024-01-01');
        $currentDate = now()->startOfMonth();
        $iterator = \Carbon\Carbon::parse($startDate)->startOfMonth();

        $history = [];
        $workingDays = (int) \App\Models\Setting::get('monthly_working_days', 26);
        $balance = 0;

        while ($iterator->lte($currentDate)) {
            $m = $iterator->month;
            $y = $iterator->year;

            $attendances = \App\Models\Attendance::where('employee_id', $employee->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->get();

            $advs = \App\Models\SalaryAdvance::where('employee_id', $employee->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->get();

            $advAmount = $advs->sum('amount');

            $leave = $attendances->where('status', \App\Enums\AttendanceStatus::LEAVE->value)->count();
            $absent = $attendances->where('status', \App\Enums\AttendanceStatus::ABSENT->value)->count();
            $halfDay = $attendances->where('status', \App\Enums\AttendanceStatus::HALF_DAY->value)->count();

            $excessLeave = max(0, $leave - 4);
            $deductionDays = $absent + $excessLeave + ($halfDay * 0.5);

            $dailyRate = $employee->daily_rate > 0 ? $employee->daily_rate : ($employee->base_salary > 0 ? $employee->base_salary / $workingDays : 0);
            $absentDeduction = $deductionDays * $dailyRate;
            $monthlyEarning = $employee->base_salary - $absentDeduction;

            $net = $monthlyEarning - $advAmount + $balance;

            $period = \App\Models\PayrollPeriod::where('month', $m)->where('year', $y)->first();
            $status = $period ? $period->getStatus() : 'Draft';

            $history[] = [
                'month' => $iterator->format('F Y'),
                'payable_month' => $iterator->copy()->addMonths(2)->format('F Y'),
                'base_salary' => $employee->base_salary,
                'advances' => $advAmount,
                'deductions' => $absentDeduction,
                'net_salary' => $net,
                'status' => $status,
                'paid_date' => ($period && $period->is_released) ? $period->released_at?->format('d M Y') : 'N/A',
                'advances_list' => $advs
            ];

            if ($period && $period->is_released) {
                $balance = min(0, $net);
            } else {
                $balance = $net;
            }

            $iterator->addMonth();
        }

        $history = array_reverse($history);

        return view('employees.show', compact('employee', 'history'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $operationalUnits = OperationalUnit::all();
        
        // Get users not linked to any employee (except current employee's user)
        $users = User::whereNotExists(function ($query) use ($employee) {
            $query->select(DB::raw(1))
                  ->from('employees')
                  ->whereColumn('employees.user_id', 'users.id')
                  ->where('employees.id', '!=', $employee->id);
        })->get();

        $roles = [
            'office' => 'Office Staff',
            'driver' => 'Driver',
            'operational' => 'Operational Staff',
            'operator' => 'Operator',
        ];

        return view('employees.edit', compact('employee', 'operationalUnits', 'users', 'roles'));
    }

    /**
     * Update the specified employee.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:office,driver,operational,operator',
            'base_salary' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'operational_unit_id' => 'nullable|exists:operational_units,id',
            'user_id' => [
                'nullable',
                'exists:users,id',
                Rule::unique('employees', 'user_id')->ignore($employee->id),
            ],
            'is_active' => 'boolean',
        ]);

        $employee->update([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'base_salary' => $validated['base_salary'] ?? 0,
            'daily_rate' => $validated['daily_rate'] ?? 0,
            'operational_unit_id' => $validated['operational_unit_id'] ?? null,
            'user_id' => $validated['user_id'] ?? null,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $employee->is_active,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully!');
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Employee $employee)
    {
        $employee->update(['is_active' => !$employee->is_active]);

        $action = $employee->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Employee {$action} successfully!");
    }
}
