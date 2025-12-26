<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DistanceService
{
    /**
     * Geocode an address to latitude and longitude.
     * Automatically selects API based on settings.
     */
    public function geocode(string $address): ?array
    {
        // Check if Google Maps API key is configured
        $googleApiKey = setting('google_maps_api_key');

        if ($googleApiKey) {
            return $this->geocodeWithGoogle($address, $googleApiKey);
        }

        return $this->geocodeWithNominatim($address);
    }

    /**
     * Geocode using OpenStreetMap Nominatim (free).
     */
    public function geocodeWithNominatim(string $address): ?array
    {
        $cacheKey = 'geocode:nominatim:' . md5($address);

        return Cache::remember($cacheKey, 2592000, function () use ($address) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'StoneCrusherERP/1.0',
                ])->get('https://nominatim.openstreetmap.org/search', [
                            'q' => $address,
                            'format' => 'json',
                            'limit' => 1,
                        ]);

                if ($response->successful() && count($response->json()) > 0) {
                    $result = $response->json()[0];
                    return [
                        'lat' => (float) $result['lat'],
                        'lng' => (float) $result['lon'],
                    ];
                }

                Log::warning('Nominatim geocoding failed', ['address' => $address]);
                return null;
            } catch (\Exception $e) {
                Log::error('Nominatim geocoding error', [
                    'address' => $address,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Geocode using Google Maps API (requires API key).
     */
    public function geocodeWithGoogle(string $address, string $apiKey): ?array
    {
        $cacheKey = 'geocode:google:' . md5($address);

        return Cache::remember($cacheKey, 2592000, function () use ($address, $apiKey) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $address,
                    'key' => $apiKey,
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && count($data['results']) > 0) {
                        $location = $data['results'][0]['geometry']['location'];
                        return [
                            'lat' => (float) $location['lat'],
                            'lng' => (float) $location['lng'],
                        ];
                    }
                }

                Log::warning('Google Maps geocoding failed', [
                    'address' => $address,
                    'status' => $data['status'] ?? 'unknown',
                ]);
                return null;
            } catch (\Exception $e) {
                Log::error('Google Maps geocoding error', [
                    'address' => $address,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    /**
     * Calculate distance between two points using Haversine formula.
     * Returns distance in kilometers.
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Get distance from crusher location to given address.
     * Returns distance in kilometers or null if geocoding fails.
     */
    public function getDistanceFromCrusher(string $address): ?float
    {
        // Get crusher coordinates from settings
        $crusherLat = (float) setting('crusher_latitude', 0);
        $crusherLng = (float) setting('crusher_longitude', 0);

        // Validate crusher coordinates
        if ($crusherLat == 0 && $crusherLng == 0) {
            Log::warning('Crusher coordinates not set in settings');
            return null;
        }

        // Geocode the address
        $coords = $this->geocode($address);

        if (!$coords) {
            return null;
        }

        // Calculate distance
        return $this->calculateDistance(
            $crusherLat,
            $crusherLng,
            $coords['lat'],
            $coords['lng']
        );
    }
}
