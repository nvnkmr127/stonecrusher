<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TablerLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Reset cached roles and permissions
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function test_admin_dashboard_uses_tabler_layout()
    {
        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Tabler Core');
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Admin User'); // Sidebar text
    }

    public function test_user_dashboard_uses_tabler_layout()
    {
        $user = User::factory()->create(['name' => 'Test User']);
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Tabler Core');
        $response->assertSee('User Dashboard');
        $response->assertSee('User Dashboard'); // Sidebar text
    }
}
