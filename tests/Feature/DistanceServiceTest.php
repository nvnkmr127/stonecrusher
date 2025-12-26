<?php

namespace Tests\Feature;

use App\Services\DistanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DistanceServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed settings
        $this->artisan('db:seed', ['--class' => 'SettingsSeeder']);
    }

    public function test_haversine_distance_calculation_is_accurate()
    {
        $service = new DistanceService();

        // Bangalore to Chennai (known distance ~290 km)
        $distance = $service->calculateDistance(12.9716, 77.5946, 13.0827, 80.2707);

        $this->assertGreaterThan(280, $distance);
        $this->assertLessThan(310, $distance);
    }

    public function test_geocode_caches_results()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '12.9716',
                    'lon' => '77.5946',
                ]
            ], 200),
        ]);

        $service = new DistanceService();

        // First call - should hit API
        $coords1 = $service->geocodeWithNominatim('Bangalore, India');

        // Second call - should hit cache
        $coords2 = $service->geocodeWithNominatim('Bangalore, India');

        $this->assertEquals($coords1, $coords2);
        $this->assertEquals(12.9716, $coords1['lat']);
        $this->assertEquals(77.5946, $coords1['lng']);

        // Verify only one HTTP request was made
        Http::assertSentCount(1);
    }

    public function test_nominatim_geocoding_works()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '12.9716',
                    'lon' => '77.5946',
                    'display_name' => 'Bangalore, India',
                ]
            ], 200),
        ]);

        $service = new DistanceService();
        $coords = $service->geocodeWithNominatim('Bangalore, India');

        $this->assertNotNull($coords);
        $this->assertArrayHasKey('lat', $coords);
        $this->assertArrayHasKey('lng', $coords);
        $this->assertEquals(12.9716, $coords['lat']);
        $this->assertEquals(77.5946, $coords['lng']);
    }

    public function test_nominatim_geocoding_handles_failure()
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 200),
        ]);

        $service = new DistanceService();
        $coords = $service->geocodeWithNominatim('Invalid Address XYZ123');

        $this->assertNull($coords);
    }

    public function test_google_maps_geocoding_works()
    {
        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'results' => [
                    [
                        'geometry' => [
                            'location' => [
                                'lat' => 12.9716,
                                'lng' => 77.5946,
                            ],
                        ],
                    ]
                ],
            ], 200),
        ]);

        $service = new DistanceService();
        $coords = $service->geocodeWithGoogle('Bangalore, India', 'test-api-key');

        $this->assertNotNull($coords);
        $this->assertEquals(12.9716, $coords['lat']);
        $this->assertEquals(77.5946, $coords['lng']);
    }

    public function test_google_maps_geocoding_handles_failure()
    {
        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'ZERO_RESULTS',
                'results' => [],
            ], 200),
        ]);

        $service = new DistanceService();
        $coords = $service->geocodeWithGoogle('Invalid Address', 'test-api-key');

        $this->assertNull($coords);
    }

    public function test_geocode_uses_google_when_api_key_is_set()
    {
        // Set Google Maps API key in settings
        \App\Models\Setting::set('google_maps_api_key', 'test-key');
        Cache::flush();

        Http::fake([
            'maps.googleapis.com/*' => Http::response([
                'status' => 'OK',
                'results' => [
                    [
                        'geometry' => [
                            'location' => [
                                'lat' => 12.9716,
                                'lng' => 77.5946,
                            ],
                        ],
                    ]
                ],
            ], 200),
        ]);

        $service = new DistanceService();
        $coords = $service->geocode('Bangalore, India');

        $this->assertNotNull($coords);

        // Verify Google Maps API was called
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'maps.googleapis.com');
        });
    }

    public function test_geocode_uses_nominatim_when_no_api_key()
    {
        // Ensure no Google Maps API key
        \App\Models\Setting::set('google_maps_api_key', '');
        Cache::flush();

        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '12.9716',
                    'lon' => '77.5946',
                ]
            ], 200),
        ]);

        $service = new DistanceService();
        $coords = $service->geocode('Bangalore, India');

        $this->assertNotNull($coords);

        // Verify Nominatim was called
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'nominatim.openstreetmap.org');
        });
    }

    public function test_get_distance_from_crusher_works()
    {
        // Set crusher location (Bangalore)
        \App\Models\Setting::set('crusher_latitude', '12.9716');
        \App\Models\Setting::set('crusher_longitude', '77.5946');
        Cache::flush();

        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                [
                    'lat' => '13.0827',
                    'lon' => '80.2707',
                ]
            ], 200),
        ]);

        $service = new DistanceService();
        $distance = $service->getDistanceFromCrusher('Chennai, India');

        $this->assertNotNull($distance);
        $this->assertGreaterThan(280, $distance);
        $this->assertLessThan(310, $distance);
    }

    public function test_get_distance_from_crusher_returns_null_when_coordinates_not_set()
    {
        // Set crusher location to 0,0
        \App\Models\Setting::set('crusher_latitude', '0.0');
        \App\Models\Setting::set('crusher_longitude', '0.0');
        Cache::flush();

        $service = new DistanceService();
        $distance = $service->getDistanceFromCrusher('Some Address');

        $this->assertNull($distance);
    }

    public function test_get_distance_from_crusher_returns_null_when_geocoding_fails()
    {
        \App\Models\Setting::set('crusher_latitude', '12.9716');
        \App\Models\Setting::set('crusher_longitude', '77.5946');
        Cache::flush();

        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 200),
        ]);

        $service = new DistanceService();
        $distance = $service->getDistanceFromCrusher('Invalid Address');

        $this->assertNull($distance);
    }
}
