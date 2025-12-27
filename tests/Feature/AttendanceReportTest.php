<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceReportTest extends TestCase
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

    public function test_admin_can_view_monthly_report()
    {
        // Create meaningful data
        Attendance::create(['user_id' => $this->user->id, 'date' => now()->startOfMonth()->format('Y-m-d'), 'status' => 'present']);
        Attendance::create(['user_id' => $this->user->id, 'date' => now()->startOfMonth()->addDay()->format('Y-m-d'), 'status' => 'late']);
        Attendance::create(['user_id' => $this->user->id, 'date' => now()->startOfMonth()->addDays(2)->format('Y-m-d'), 'status' => 'half_day']);
        Attendance::create(['user_id' => $this->user->id, 'date' => now()->startOfMonth()->addDays(3)->format('Y-m-d'), 'status' => 'absent']);

        $response = $this->actingAs($this->admin)->get(route('attendance.report'));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        // We expect counts:
        // Present: 2 (1 present + 1 late)
        // Late: 1
        // Half Day: 1
        // Absent: 1
    }

    public function test_admin_can_export_report()
    {
        Attendance::create(['user_id' => $this->user->id, 'date' => now()->startOfMonth()->format('Y-m-d'), 'status' => 'present']);

        $response = $this->actingAs($this->admin)->get(route('attendance.report.export'));

        $response->assertStatus(200);
        $response->assertStatus(200);

        // Assert content type starts with text/csv (handling charset case variations)
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-Type'));
    }
}
