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

        \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag);
    }

    public function test_admin_dashboard_renders_components()
    {
        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
        $response->assertSee('Projects Overview');
        $response->assertSee('Total Clients');
    }

    public function test_user_dashboard_renders_components()
    {
        $user = User::factory()->create(['name' => 'Test User']);
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('User Dashboard');
        $response->assertSee('My Profile');
        $response->assertSee('Activity Summary');
        $response->assertSee('Contact Support');
    }

    public function test_alert_component_renders_correctly()
    {
        $view = $this->blade('<x-alert type="success">Success Message</x-alert>');
        $view->assertSee('alert-success');
        $view->assertSee('Success Message');

        $view = $this->blade('<x-alert type="danger">Danger Message</x-alert>');
        $view->assertSee('alert-danger');
        $view->assertSee('Danger Message');

        $view = $this->blade('<x-alert type="warning">Warning Message</x-alert>');
        $view->assertSee('alert-warning');
        $view->assertSee('Warning Message');

        $view = $this->blade('<x-alert type="info">Info Message</x-alert>');
        $view->assertSee('alert-info');
        $view->assertSee('Info Message');
    }

    public function test_table_component_renders_users()
    {
        $view = $this->blade('<x-table><thead><tr><th>Email</th></tr></thead><tbody><tr><td>test@example.com</td></tr></tbody></x-table>');
        $view->assertSee('table-responsive');
        $view->assertSee('test@example.com');
    }

    public function test_form_components_render()
    {
        $view = $this->blade('<x-form.input name="test_input" label="Input Label" />');
        $view->assertSee('form-control');
        $view->assertSee('Input Label');

        $view = $this->blade('<x-form.select name="test_select" label="Select Label" :options="[]" />');
        $view->assertSee('form-select');
        $view->assertSee('Select Label');

        $view = $this->blade('<x-form.checkbox name="test_check" label="Check Label" />');
        $view->assertSee('form-check');
        $view->assertSee('Check Label');
    }

    public function test_sidebar_shows_correct_menu_for_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee('Manage Users');
        $response->assertSee('Global Setup');
        $response->assertDontSee('My Orders');
    }

    public function test_sidebar_shows_correct_menu_for_user()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertDontSee('Manage Users');
        $response->assertDontSee('Global Setup');
    }
}
