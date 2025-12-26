<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function test_admin_can_view_users_list()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('User Management');
    }

    public function test_non_admin_cannot_view_users_list()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
            'department' => 'Sales',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'department' => 'Sales',
            'is_active' => true,
        ]);

        // Check activity log
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'created',
            'performed_by' => $admin->id,
        ]);
    }

    public function test_admin_can_update_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create(['name' => 'Old Name']);
        $user->assignRole('user');

        $response = $this->actingAs($admin)->put(route('users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'role' => 'user',
            'department' => 'Operations',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'department' => 'Operations',
        ]);
    }

    public function test_admin_cannot_remove_own_admin_role()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user', // Trying to demote self
            'is_active' => true,
        ]);

        $response->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_admin_can_toggle_user_status()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('user');

        $response = $this->actingAs($admin)->post(route('users.toggle-status', $user));

        $response->assertRedirect();
        $this->assertFalse($user->fresh()->is_active);

        // Check activity log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'deactivated',
        ]);
    }

    public function test_admin_cannot_deactivate_self()
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('users.toggle-status', $admin));

        $response->assertSessionHas('error');
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($admin)->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->delete(route('users.destroy', $admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_reset_user_password()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($admin)->post(route('users.reset-password', $user), [
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check activity log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'action' => 'password_reset',
        ]);
    }

    public function test_user_search_works()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com'])->assignRole('user');
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com'])->assignRole('user');

        $response = $this->actingAs($admin)->get(route('users.index', ['search' => 'John']));

        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    public function test_user_filter_by_role_works()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $adminUser = User::factory()->create()->assignRole('admin');
        $regularUser = User::factory()->create()->assignRole('user');

        $response = $this->actingAs($admin)->get(route('users.index', ['role' => 'admin']));

        $response->assertSee($adminUser->email);
        $response->assertDontSee($regularUser->email);
    }

    public function test_validation_prevents_duplicate_email()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $existingUser = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'existing@example.com', // Duplicate
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
