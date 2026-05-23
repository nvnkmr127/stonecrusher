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
            'is_active' => 'boolean',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'department' => $validated['department'] ?? null,
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
            'is_active' => 'boolean',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $validated, $request) {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'department' => $validated['department'] ?? null,
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
        return view('users.show', compact('user'));
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
