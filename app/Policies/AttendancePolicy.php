<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('attendance.view_any');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('attendance.mark');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        if ($user->hasPermissionTo('attendance.edit')) {
            return true;
        }

        // Managers (with mark permission but not edit) can only update if check_out is not set
        if ($user->hasPermissionTo('attendance.mark')) {
            return is_null($attendance->check_out);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->hasPermissionTo('attendance.edit');
    }

    /**
     * Determine whether the user can view reports.
     */
    public function viewReports(User $user): bool
    {
        // Admin or Manager can view reports provided they have view_any. 
        // Or maybe strictly limit report to Admin? Requirement says "Admin / Manager" can view reports.
        return $user->hasPermissionTo('attendance.view_any');
    }
}
