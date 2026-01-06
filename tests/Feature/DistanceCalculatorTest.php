<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DistanceCalculatorTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::set('rate_per_km', 15);
        Role::create(['name' => 'admin']);
    }

    public function test_can_view_calculator_page()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get(route('gate-passes.calculator'));

        $response->assertStatus(200);
        $response->assertSee('Distance Calculator');
        $response->assertSee('15'); // Rate
    }

    public function test_can_calculate_distance_via_api()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        // Set Crusher Location (Reference point)
        // Example: Hyderabad (17.3850, 78.4867)
        Setting::set('crusher_latitude', 17.3850);
        Setting::set('crusher_longitude', 78.4867);

        // Destination: Secunderabad (approx 17.4399, 78.4983)
        // Distance approx 6-7 km

        $response = $this->actingAs($user)->getJson(route('gate-passes.calculator', [
            'lat' => 17.4399,
            'lon' => 78.4983,
            'json' => 1
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure(['distance', 'cost', 'rate']);

        $data = $response->json();
        $this->assertGreaterThan(0, $data['distance']);
        $this->assertEquals(15, $data['rate']);
        $this->assertEquals($data['distance'] * 15, $data['cost']);
    }
}
