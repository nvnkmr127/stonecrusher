<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ComponentRenderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function test_admin_dashboard_renders_components()
    {
        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Component Library Examples');
        $response->assertSee('Success!');
        $response->assertSee('Primary Button');
    }

    public function test_user_dashboard_renders_components()
    {
        $user = User::factory()->create(['name' => 'Test User']);
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('My Profile');
        $response->assertSee('Activity Summary');
        $response->assertSee('Contact Support');
    }

    public function test_alert_component_renders_correctly()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee('alert-success');
        $response->assertSee('alert-info');
        $response->assertSee('alert-warning');
        $response->assertSee('alert-danger');
    }

    public function test_table_component_renders_users()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee('table-responsive');
        $response->assertSee($admin->email);
    }

    public function test_form_components_render()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee('form-control');
        $response->assertSee('form-select');
        $response->assertSee('form-check');
    }

    public function test_sidebar_shows_correct_menu_for_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee('User Management');
        $response->assertDontSee('My Orders');
    }

    public function test_sidebar_shows_correct_menu_for_user()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertSee('My Orders');
        $response->assertDontSee('User Management');
    }
}
