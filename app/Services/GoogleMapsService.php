<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Setting;

class GoogleMapsService
{
    /**
     * Round coordinates to 4 decimal places (approx 11m precision).
     * This increases cache hit rate by treating very close points as the same.
     */
    private function normalizeCoord($coord)
    {
        return round(floatval($coord), 4);
    }

    /**
     * Get address from lat/lng, using cache if available.
     */
    public function getReverseGeocode($lat, $lng)
    {
        $lat = $this->normalizeCoord($lat);
        $lng = $this->normalizeCoord($lng);

        // 1. Check Cache
        $cached = DB::table('geo_cache')
            ->where('lat', $lat)
            ->where('lng', $lng)
            ->first();

        if ($cached) {
            return $cached->address;
        }

        // 2. Call Google API
        $apiKey = Setting::get('map_key');
        if (!$apiKey) {
            Log::error('Google Map Key not set');
            return '';
        }

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";

        try {
            // Using existing curl pattern or file_get_contents
            if (function_exists('curl')) {
                $json = curl($url); // Assuming global helper exists as seen in controllers
            } else {
                $json = file_get_contents($url);
            }

            $details = json_decode($json, true);

            if (isset($details['status']) && $details['status'] == 'OK' && !empty($details['results'])) {
                $address = $details['results'][0]['formatted_address'];

                // 3. Store in Cache
                try {
                    DB::table('geo_cache')->insert([
                        'lat' => $lat,
                        'lng' => $lng,
                        'address' => $address,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $e) {
                    // Ignore duplicate key errors if race condition
                }

                return $address;
            } else {
                Log::warning("Google Geocode Failed: " . json_encode($details));
                return '';
            }

        } catch (\Exception $e) {
            Log::error("Google Geocode Exception: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Get directions between two points, using cache if available.
     * Returns an array with route_key (polyline), distance_text, etc.
     */
    public function getDirections($originLat, $originLng, $destLat, $destLng)
    {
        $originLat = $this->normalizeCoord($originLat);
        $originLng = $this->normalizeCoord($originLng);
        $destLat = $this->normalizeCoord($destLat);
        $destLng = $this->normalizeCoord($destLng);

        $sourceKey = "{$originLat},{$originLng}";
        $destKey = "{$destLat},{$destLng}";

        // 1. Check Cache
        $cached = DB::table('route_cache')
            ->where('source_key', $sourceKey)
            ->where('dest_key', $destKey)
            ->first();

        if ($cached && !empty($cached->response_json)) {
            return json_decode($cached->response_json, true);
        }

        // 2. Call Google API
        $apiKey = Setting::get('map_key');
        if (!$apiKey) {
            return null;
        }

        $url = "https://maps.googleapis.com/maps/api/directions/json?origin={$sourceKey}&destination={$destKey}&mode=driving&key={$apiKey}";

        try {
            if (function_exists('curl')) {
                $json = curl($url);
            } else {
                $json = file_get_contents($url);
            }

            $details = json_decode($json, true);

            if (isset($details['status']) && $details['status'] == 'OK' && !empty($details['routes'])) {

                // Extract useful info to store in columns (optional, but good for analytics)
                $route = isset($details['routes'][0]) ? $details['routes'][0] : [];
                $leg = isset($route['legs'][0]) ? $route['legs'][0] : [];

                $distanceValue = isset($leg['distance']['value']) ? $leg['distance']['value'] : 0; // meters
                $distanceKm = round($distanceValue / 1000, 2);

                $durationValue = isset($leg['duration']['value']) ? $leg['duration']['value'] : 0; // seconds
                $durationMin = round($durationValue / 60);

                $polyline = isset($route['overview_polyline']['points']) ? $route['overview_polyline']['points'] : '';

                // 3. Store in Cache
                try {
                    DB::table('route_cache')->insert([
                        'source_key' => $sourceKey,
                        'dest_key' => $destKey,
                        'distance_km' => $distanceKm,
                        'duration_min' => $durationMin,
                        'polyline' => $polyline,
                        'response_json' => $json, // Save full response to return exact structure
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } catch (\Exception $e) {
                    // Ignore duplicate key errors
                }

                return $details;
            }

        } catch (\Exception $e) {
            Log::error("Google Directions Exception: " . $e->getMessage());
        }

        return null;
    }
}
