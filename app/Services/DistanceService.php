<?php

namespace App\Services;

class DistanceService
{
    /**
     * Calculate the distance between two points using the Haversine formula.
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in kilometers, rounded to 2 decimal places
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Radius of the earth in km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Geocode an address using Nominatim (OpenStreetMap).
     *
     * @param string $address
     * @return array|null ['lat' => float, 'lng' => float]
     */
    public function geocodeWithNominatim(string $address): ?array
    {
        $cacheKey = 'geocode:nominatim:' . md5($address);
        return \Illuminate\Support\Facades\Cache::rememberForever($cacheKey, function () use ($address) {
            $response = \Illuminate\Support\Facades\Http::get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $address,
                'limit' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
                    return [
                        'lat' => (float) $data[0]['lat'],
                        'lng' => (float) $data[0]['lon']
                    ];
                }
            }

            return null;
        });
    }

    /**
     * Geocode an address using Google Maps API.
     *
     * @param string $address
     * @param string $apiKey
     * @return array|null ['lat' => float, 'lng' => float]
     */
    public function geocodeWithGoogle(string $address, string $apiKey): ?array
    {
        $cacheKey = 'geocode:google:' . md5($address);
        return \Illuminate\Support\Facades\Cache::rememberForever($cacheKey, function () use ($address, $apiKey) {
            $response = \Illuminate\Support\Facades\Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];
                    return [
                        'lat' => (float) $location['lat'],
                        'lng' => (float) $location['lng']
                    ];
                }
            }

            return null;
        });
    }

    /**
     * Geocode an address using the configured geocoding service.
     *
     * @param string $address
     * @return array|null ['lat' => float, 'lng' => float]
     */
    public function geocode(string $address): ?array
    {
        $apiKey = \App\Models\Setting::get('google_maps_api_key');
        if (!empty($apiKey)) {
            return $this->geocodeWithGoogle($address, $apiKey);
        }
        return $this->geocodeWithNominatim($address);
    }

    /**
     * Get the distance in kilometers from the crusher to the given address.
     *
     * @param string $address
     * @return float|null
     */
    public function getDistanceFromCrusher(string $address): ?float
    {
        $crusherLat = (float) \App\Models\Setting::get('crusher_latitude', 0.0);
        $crusherLon = (float) \App\Models\Setting::get('crusher_longitude', 0.0);

        if (empty($crusherLat) || empty($crusherLon) || ($crusherLat === 0.0 && $crusherLon === 0.0)) {
            return null;
        }

        $coords = $this->geocode($address);
        if (!$coords) {
            return null;
        }

        return $this->calculateDistance($crusherLat, $crusherLon, $coords['lat'], $coords['lng']);
    }
}
