<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceEditTest extends TestCase
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

    public function test_update_requires_remarks()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);

        $updateData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
            'remarks' => '', // Empty remarks
        ];

        $response = $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);

        $response->assertSessionHasErrors('remarks');
    }

    public function test_update_creates_activity_log()
    {
        $attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);

        $remarks = 'Updating for test reason';
        $updateData = [
            'user_id' => $this->user->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
            'remarks' => $remarks,
        ];

        $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'performed_by' => $this->admin->id,
            'action' => 'attendance_update',
        ]);

        // Optional: Verify description contains remarks
        $log = ActivityLog::where('user_id', $this->user->id)->latest()->first();
        $this->assertStringContainsString($remarks, $log->description);
    }
}
