<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AttendancePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $manager;
    protected $user;
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();

        // Reset cached roles and permissions
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Setup Permissions (Same as Seeder logic)
        $permissions = ['attendance.view_any', 'attendance.mark', 'attendance.edit'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo(['attendance.view_any', 'attendance.mark']);

        $userRole = Role::create(['name' => 'user']);

        // Create Users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        $this->user = User::factory()->create();
        $this->user->assignRole('user');

        // Create Employee
        $this->employee = Employee::factory()->create();
    }

    public function test_admin_has_full_access()
    {
        $this->actingAs($this->admin)->get(route('attendance.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('attendance.create'))->assertStatus(200);
        $this->actingAs($this->admin)->post(route('attendance.store'), [
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present'
        ])->assertRedirect(route('attendance.index'));

        $attendance = Attendance::first();
        $this->actingAs($this->admin)->get(route('attendance.edit', $attendance))->assertStatus(200);
        $this->actingAs($this->admin)->put(route('attendance.update', $attendance), [
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'absent',
            'remarks' => 'Changed by Admin'
        ])->assertRedirect(route('attendance.index'));
    }

    public function test_manager_can_mark_attendance_but_not_edit_past_records()
    {
        // Can View and Create
        $this->actingAs($this->manager)->get(route('attendance.index'))->assertStatus(200);
        $this->actingAs($this->manager)->get(route('attendance.create'))->assertStatus(200);

        // Can Store (Mark Check-in)
        $this->actingAs($this->manager)->post(route('attendance.store'), [
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'status' => 'present' // Will be auto-calculated but passed for validation
        ])->assertRedirect(route('attendance.index'));

        $attendance = Attendance::first();

        // Manager Update: Can update ONLY if check_out is null (Marking Check-out)
        // Currently check_out is null.
        $this->actingAs($this->manager)->put(route('attendance.update', $attendance), [
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'check_out' => '18:00',
            'status' => 'present',
            'remarks' => 'Checking Out'
        ])->assertRedirect(route('attendance.index'));

        // Refresh attendance, check_out is now set.
        $attendance->refresh();

        // Manager try to edit AGAIN (changing status/remarks after checkout is done) -> Should fail
        $response = $this->actingAs($this->manager)->put(route('attendance.update', $attendance), [
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'check_out' => '18:00',
            'status' => 'absent', // Try to change status
            'remarks' => 'Malicious Edit'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_access_attendance_module()
    {
        $this->actingAs($this->user)->get(route('attendance.index'))->assertStatus(403);
        $this->actingAs($this->user)->get(route('attendance.create'))->assertStatus(403);
        $this->actingAs($this->user)->post(route('attendance.store'), [])->assertStatus(403);
    }
}
