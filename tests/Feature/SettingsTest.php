<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Seed default settings
        $this->artisan('db:seed', ['--class' => 'SettingsSeeder']);
    }

    public function test_setting_helper_function_works()
    {
        $value = setting('company_name');

        $this->assertEquals('Stone Crusher ERP', $value);
    }

    public function test_setting_helper_returns_default_for_missing_key()
    {
        $value = setting('non_existent_key', 'default_value');

        $this->assertEquals('default_value', $value);
    }

    public function test_setting_helper_uses_cache()
    {
        // First call should cache
        $value1 = setting('company_name');

        // Verify cache exists
        $this->assertTrue(Cache::has('setting.company_name'));

        // Second call should use cache
        $value2 = setting('company_name');

        $this->assertEquals($value1, $value2);
    }

    public function test_cache_invalidates_on_setting_update()
    {
        // Get initial value and cache it
        setting('company_name');
        $this->assertTrue(Cache::has('setting.company_name'));

        // Update the setting
        Setting::set('company_name', 'New Company Name');

        // Cache should be cleared
        $this->assertFalse(Cache::has('setting.company_name'));

        // New value should be returned
        $this->assertEquals('New Company Name', setting('company_name'));
    }

    public function test_admin_can_access_settings_page()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/settings');

        $response->assertStatus(200);
        $response->assertSee('System Settings');
        $response->assertSee('Company Name');
    }

    public function test_user_cannot_access_settings_page()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/settings');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_settings_page()
    {
        $response = $this->get('/settings');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_update_settings()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/settings', [
            'company_name' => 'Updated Company',
            'currency_symbol' => '$',
            'financial_year' => '2025-2026',
            'crusher_latitude' => '12.34',
            'crusher_longitude' => '56.78',
            'default_diesel_rate' => '120.50',
            'rate_per_km' => '15.00',
            'date_format' => 'Y-m-d',
        ]);

        $response->assertRedirect('/settings');
        $response->assertSessionHas('success');

        // Verify settings were updated
        $this->assertEquals('Updated Company', setting('company_name'));
        $this->assertEquals('$', setting('currency_symbol'));
        $this->assertEquals('2025-2026', setting('financial_year'));
    }

    public function test_settings_validation_works()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post('/settings', [
            'company_name' => '', // Required field
            'currency_symbol' => '$',
            'financial_year' => '2025-2026',
            'crusher_latitude' => 'invalid', // Should be numeric
            'crusher_longitude' => '56.78',
            'default_diesel_rate' => '-10', // Should be min:0
            'rate_per_km' => '15.00',
            'date_format' => 'Y-m-d',
        ]);

        $response->assertSessionHasErrors(['company_name', 'crusher_latitude', 'default_diesel_rate']);
    }

    public function test_sidebar_shows_settings_for_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertSee('Settings');
        $response->assertSee(route('settings.index'));
    }

    public function test_sidebar_does_not_show_settings_for_user()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertDontSee('Settings');
    }
}
