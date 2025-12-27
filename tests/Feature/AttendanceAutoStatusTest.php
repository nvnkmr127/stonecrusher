<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceAutoStatusTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $this->admin->assignRole($role);

        // Create User
        $this->user = User::factory()->create();
    }

    public function test_attendance_marked_late_if_check_in_after_0930()
    {
        $attendanceData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:45', // Late
            'status' => 'present', // Should be overwritten
        ];

        $this->actingAs($this->admin)->post(route('attendance.store'), $attendanceData);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'status' => 'late',
            'check_in' => '09:45',
        ]);
    }

    public function test_attendance_marked_present_if_check_in_on_time()
    {
        $attendanceData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:15', // On Time
            'status' => 'present',
        ];

        $this->actingAs($this->admin)->post(route('attendance.store'), $attendanceData);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'status' => 'present',
        ]);
    }

    public function test_attendance_marked_half_day_if_check_out_early()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'status' => 'present',
        ]);

        $updateData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_out' => '17:00', // Early exit (Shift ends 18:30)
            'status' => 'present', // Should be overwritten
        ];

        $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'half_day',
            'check_out' => '17:00',
        ]);
    }

    public function test_attendance_marked_present_if_full_day()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'status' => 'present',
        ]);

        $updateData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_out' => '18:45', // On time/Late exit
            'status' => 'present',
        ];

        $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'present',
        ]);
    }
}
