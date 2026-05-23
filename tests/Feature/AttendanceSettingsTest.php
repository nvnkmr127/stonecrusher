<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Add this import
use Tests\TestCase;

class AttendanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();

        // Define permissions
        $permissions = ['attendance.view_any', 'attendance.mark', 'attendance.edit'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin
        $this->admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo($permissions); // Give permissions to role
        $this->admin->assignRole($role);

        // Create Employee
        $this->employee = Employee::factory()->create();
    }

    public function test_custom_shift_timings_affect_status_calculation()
    {
        // Set custom shift start to 10:00 AM
        Setting::set('attendance_shift_start', '10:00');
        Setting::set('attendance_shift_end', '19:00');

        $this->withoutExceptionHandling();

        // Employee checks in at 09:45 (Late for 09:30, but On Time for 10:00)
        $this->actingAs($this->admin)->post(route('attendance.store'), [
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:45',
        ]);

        // Debugging validation errors if any
        if (Attendance::count() == 0) {
            dump(session('errors') ? session('errors')->all() : 'No Errors, maybe Redirect?');
        }

        $attendance = Attendance::first();
        $this->assertEquals('present', $attendance->status->value); // Should be present, not late
    }

    public function test_late_status_based_on_settings()
    {
        $this->withoutExceptionHandling();

        // Set custom shift start to 08:00 AM
        Setting::set('attendance_shift_start', '08:00');

        // Employee checks in at 08:15
        $this->actingAs($this->admin)->post(route('attendance.store'), [
            'employee_id' => $this->employee->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'check_in' => '08:15',
        ]);

        $attendance = Attendance::whereDate('date', now()->addDay()->format('Y-m-d'))->first();
        $this->assertEquals('late', $attendance->status->value);
    }
}
