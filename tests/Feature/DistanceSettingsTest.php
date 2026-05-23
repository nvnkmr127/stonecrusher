<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DistanceSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_settings_page_accessible_by_admin()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Transport Rate per KM');
        $response->assertSee('Default to Round Trip Calculation?');
    }

    public function test_update_distance_settings()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->post(route('settings.update'), [
            'company_name' => 'Test Co',
            'app_timezone' => 'UTC',
            'currency_symbol' => '$',
            'financial_year' => '2025',
            'date_format' => 'Y-m-d',
            'attendance_shift_start' => '09:00',
            'attendance_shift_end' => '17:00',
            'crusher_latitude' => 12.34,
            'crusher_longitude' => 56.78,
            'default_diesel_rate' => 95.50,
            'rate_per_km' => 15.00,
            'default_round_trip' => 1
        ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertEquals(15.00, Setting::get('rate_per_km'));
        $this->assertEquals(1, Setting::get('default_round_trip'));
    }

    public function test_gate_pass_uses_default_round_trip()
    {
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        Setting::set('default_round_trip', 1);

        $response = $this->actingAs($user)->get(route('gate-passes.calculator'));
        $response->assertStatus(200);
        // We check if the JavaScript variable is initialized correctly
        $response->assertSee("isRoundTrip: true");
    }
}
