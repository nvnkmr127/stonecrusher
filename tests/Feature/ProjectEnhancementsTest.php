<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ProjectEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function test_project_can_have_location()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('projects.store'), [
            'name' => 'Test Project',
            'client_id' => $client->id,
            'location' => 'Test Location, City',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'location' => 'Test Location, City',
        ]);
    }

    public function test_project_can_have_estimated_quantity()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);

        $response = $this->actingAs($admin)->post(route('projects.store'), [
            'name' => 'Test Project',
            'client_id' => $client->id,
            'estimated_quantity' => 1500.50,
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::where('name', 'Test Project')->first();
        $this->assertEquals(1500.50, $project->estimated_quantity);
    }

    public function test_project_progress_must_be_between_0_and_100()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);

        // Test invalid progress > 100
        $response = $this->actingAs($admin)->post(route('projects.store'), [
            'name' => 'Test Project',
            'client_id' => $client->id,
            'status' => 'pending',
            'progress' => 150,
        ]);

        $response->assertSessionHasErrors('progress');

        // Test invalid progress < 0
        $response = $this->actingAs($admin)->post(route('projects.store'), [
            'name' => 'Test Project',
            'client_id' => $client->id,
            'status' => 'pending',
            'progress' => -10,
        ]);

        $response->assertSessionHasErrors('progress');

        // Test valid progress
        $response = $this->actingAs($admin)->post(route('projects.store'), [
            'name' => 'Test Project',
            'client_id' => $client->id,
            'status' => 'pending',
            'progress' => 50,
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'progress' => 50,
        ]);
    }

    public function test_dashboard_shows_project_statistics()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);

        // Create projects with different statuses
        Project::create(['name' => 'Active Project', 'client_id' => $client->id, 'status' => 'active']);
        Project::create(['name' => 'Completed Project', 'client_id' => $client->id, 'status' => 'completed']);
        Project::create(['name' => 'Pending Project', 'client_id' => $client->id, 'status' => 'pending']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Projects');
        $response->assertSee('Active Projects');
        $response->assertSee('Completed Projects');
        $response->assertSee('Pending Projects');
    }

    public function test_dashboard_shows_recent_projects()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $client = Client::create(['name' => 'Test Client', 'is_active' => true]);

        Project::create([
            'name' => 'Recent Project',
            'client_id' => $client->id,
            'location' => 'Test Location',
            'status' => 'active',
            'progress' => 75,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Recent Projects');
        $response->assertSee('Recent Project');
        $response->assertSee('Test Location');
    }
}
