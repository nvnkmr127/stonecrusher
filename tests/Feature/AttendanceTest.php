<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::factory()->create();
        $this->admin->email_verified_at = now();
        $this->admin->save();
        $role = Role::create(['name' => 'admin']);
        $this->admin->assignRole($role);

        // Create User
        $this->user = User::factory()->create();
        $this->user->email_verified_at = now();
        $this->user->save();
    }

    public function test_admin_can_view_attendance_index()
    {
        $response = $this->actingAs($this->admin)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertViewIs('attendance.index');
    }

    public function test_admin_can_create_attendance()
    {
        $attendanceData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'check_out' => '18:30',
            'status' => 'present',
            'remarks' => 'On time',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendance.store'), $attendanceData);

        $response->assertRedirect(route('attendance.index'));
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'status' => 'present',
        ]);
    }

    public function test_admin_cannot_create_duplicate_attendance_for_same_day()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);

        $attendanceData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'status' => 'present',
        ];

        $response = $this->actingAs($this->admin)->post(route('attendance.store'), $attendanceData);

        $response->assertSessionHasErrors('date');
    }

    public function test_admin_can_update_attendance()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);

        $updateData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:35',
            'status' => 'late',
            'remarks' => 'Late entry',
        ];

        $response = $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);

        $response->assertRedirect(route('attendance.index'));
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => 'late',
            'check_in' => '09:35',
        ]);
    }

    public function test_cannot_check_out_without_check_in()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
            'check_in' => null,
        ]);

        $updateData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_out' => '17:00',
            'status' => 'present',
        ];

        $response = $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);

        $response->assertSessionHasErrors('check_out');
    }

    public function test_check_out_must_be_after_check_in()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
            'check_in' => '09:00',
        ]);

        $updateData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'check_in' => '09:00',
            'check_out' => '08:00',
            'status' => 'present',
        ];

        $response = $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);

        $response->assertSessionHasErrors('check_out');
    }

    public function test_attendance_date_filter()
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->subDay()->format('Y-m-d'),
            'status' => 'present',
        ]);

        $todayAttendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->admin)->get(route('attendance.index', ['date' => now()->format('Y-m-d')]));

        $response->assertStatus(200);
        $response->assertSee($todayAttendance->date);
    }
}
