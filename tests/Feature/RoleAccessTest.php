<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class RoleAccessTest extends TestCase
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

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_user_can_access_user_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('User Dashboard');
    }
}
