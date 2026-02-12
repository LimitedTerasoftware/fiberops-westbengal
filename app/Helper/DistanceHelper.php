<?php

namespace App\Helpers;

class DistanceHelper
{
    public static function calculateAccurateDistance($points)
    {
        // thresholds
        $minMoveKm = 0.01;   // ignore < 10 meters
        $maxSpeedKmph = 200; // ignore impossible jumps
        $epsilon = 0.000001; // remove near duplicate GPS points

        if (count($points) < 2) {
            return 0.0;
        }

        // sort by datetime
        usort($points, function ($a, $b) {
            return strtotime($a['datetime']) <=> strtotime($b['datetime']);
        });

        // remove near-duplicate GPS points
        $clean = [];
        $prev = null;

        foreach ($points as $p) {
            if ($prev) {
                if (
                    abs($p['latitude'] - $prev['latitude']) < $epsilon &&
                    abs($p['longitude'] - $prev['longitude']) < $epsilon
                ) {
                    continue;
                }
            }
            $clean[] = $p;
            $prev = $p;
        }

        if (count($clean) < 2) {
            return 0.0;
        }

        // sum distances
        $totalKm = 0.0;

        for ($i = 1; $i < count($clean); $i++) {
            $p1 = $clean[$i - 1];
            $p2 = $clean[$i];

            $d = self::haversine($p1['latitude'], $p1['longitude'], $p2['latitude'], $p2['longitude']);

            $t1 = strtotime($p1['datetime']);
            $t2 = strtotime($p2['datetime']);
            $seconds = max(1, $t2 - $t1);

            $hours = $seconds / 3600.0;
            $speedKmph = $d / $hours;

            // ignore jitter
            if ($d < $minMoveKm) {
                continue;
            }
            // ignore unrealistic jumps
            if ($speedKmph > $maxSpeedKmph) {
                continue;
            }

            $totalKm += $d;
        }

        return round($totalKm, 2);
    }

    private static function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 +
            cos($lat1) * cos($lat2) *
            sin($dlon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
