<?php

namespace App\Http\Controllers;

use App\Models\SalaryAdvance;
use App\Models\User;
use Illuminate\Http\Request;

class SalaryAdvanceController extends Controller
{
    public function index()
    {
        $advances = SalaryAdvance::with('user')->latest()->paginate(20);
        return view('salary_advances.index', compact('advances'));
    }

    public function create(Request $request)
    {
        $users = User::all();
        $selectedUserId = $request->input('user_id');
        return view('salary_advances.create', compact('users', 'selectedUserId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'nullable|string',
            'date' => 'required|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        \App\Services\PayrollService::checkLock($request->date);

        SalaryAdvance::create($validated);

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance recorded successfully.');
    }

    public function edit(SalaryAdvance $salaryAdvance)
    {
        $users = User::all();
        return view('salary_advances.edit', compact('salaryAdvance', 'users'));
    }

    public function update(Request $request, SalaryAdvance $salaryAdvance)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
            'payment_mode' => 'nullable|string',
            'date' => 'required|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        \App\Services\PayrollService::checkLock($request->date);
        \App\Services\PayrollService::checkLock($salaryAdvance->date);

        $salaryAdvance->update($validated);

        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance updated successfully.');
    }

    public function destroy(SalaryAdvance $salaryAdvance)
    {
        \App\Services\PayrollService::checkLock($salaryAdvance->date);
        $salaryAdvance->delete();
        return redirect()->route('salary-advances.index')
            ->with('success', 'Salary advance deleted successfully.');
    }
}
