<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'department' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'department' => $validated['department'] ?? null,
                'base_salary' => $validated['base_salary'] ?? 0,
                'daily_rate' => $validated['daily_rate'] ?? 0,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            $user->assignRole($validated['role']);

            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully!');
        });
    }

    /**
     * Show the form for editing the user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Prevent self-demotion
        if ($user->id === auth()->id() && $request->role !== 'admin' && auth()->user()->hasRole('admin')) {
            return back()->with('error', 'You cannot remove your own admin role!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'department' => 'nullable|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $validated, $request) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'department' => $validated['department'] ?? null,
                'base_salary' => $validated['base_salary'] ?? 0,
                'daily_rate' => $validated['daily_rate'] ?? 0,
                'is_active' => $validated['is_active'] ?? $user->is_active,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Update role
            $user->syncRoles([$validated['role']]);

            return redirect()
                ->route('users.index')
                ->with('success', 'User updated successfully!');
        });
    }

    /**
     * Display User Profile and Salary History.
     */
    public function show(User $user)
    {
        $startDate = $user->created_at ?? \Carbon\Carbon::parse('2024-01-01');
        $currentDate = now()->startOfMonth();
        $iterator = \Carbon\Carbon::parse($startDate)->startOfMonth();

        $history = [];
        $workingDays = (int) \App\Models\Setting::get('monthly_working_days', 26);
        $balance = 0;

        while ($iterator->lte($currentDate)) {
            $m = $iterator->month;
            $y = $iterator->year;

            $attendances = \App\Models\Attendance::where('user_id', $user->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->get();

            $advs = \App\Models\SalaryAdvance::where('user_id', $user->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->get();

            $advAmount = $advs->sum('amount');

            $leave = $attendances->where('status', \App\Enums\AttendanceStatus::LEAVE->value)->count();
            $absent = $attendances->where('status', \App\Enums\AttendanceStatus::ABSENT->value)->count();
            $halfDay = $attendances->where('status', \App\Enums\AttendanceStatus::HALF_DAY->value)->count();

            $excessLeave = max(0, $leave - 4);
            $deductionDays = $absent + $excessLeave + ($halfDay * 0.5);

            $dailyRate = $user->daily_rate > 0 ? $user->daily_rate : ($user->base_salary > 0 ? $user->base_salary / $workingDays : 0);
            $absentDeduction = $deductionDays * $dailyRate;
            $monthlyEarning = $user->base_salary - $absentDeduction;

            $net = $monthlyEarning - $advAmount + $balance;

            $period = \App\Models\PayrollPeriod::where('month', $m)->where('year', $y)->first();
            $status = $period ? $period->getStatus() : 'Draft';

            $history[] = [
                'month' => $iterator->format('F Y'),
                'payable_month' => $iterator->copy()->addMonths(2)->format('F Y'),
                'base_salary' => $user->base_salary,
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

        return view('users.show', compact('user', 'history'));
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', 'User deleted successfully!');
        });
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent self-deactivation
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'activated' : 'deactivated';


        return back()->with('success', "User {$action} successfully!");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);



        return back()->with('success', 'Password reset successfully!');
    }
}
