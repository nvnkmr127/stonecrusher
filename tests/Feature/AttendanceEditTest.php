<?php
 
namespace Tests\Feature;
 
use App\Models\User;
use App\Models\Employee;
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
    protected $employee;
 
    protected function setUp(): void
    {
        parent::setUp();
 
        // Create Admin
        $this->admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $this->admin->assignRole($role);
 
        // Create User
        $this->user = User::factory()->create();
        
        // Create Employee mapped to the user
        $this->employee = Employee::create([
            'name' => $this->user->name,
            'role' => 'office',
            'base_salary' => 15000,
            'daily_rate' => 500,
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
    }
 
    public function test_update_requires_remarks()
    {
        $attendance = Attendance::create([
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);
 
        $updateData = [
            'employee_id' => $this->employee->id,
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
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
        ]);
 
        $remarks = 'Updating for test reason';
        $updateData = [
            'employee_id' => $this->employee->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'present',
            'remarks' => $remarks,
        ];
 
        $this->actingAs($this->admin)->put(route('attendance.update', $attendance), $updateData);
 
        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $attendance->id,
            'causer_id' => $this->admin->id,
            'event' => 'attendance_update',
        ]);
 
        // Optional: Verify description contains remarks
        $log = ActivityLog::where('subject_id', $attendance->id)->where('event', 'attendance_update')->first();
        $this->assertStringContainsString($remarks, $log->description);
    }
}
