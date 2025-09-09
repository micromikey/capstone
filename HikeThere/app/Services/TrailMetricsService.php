<?php

namespace App\Services;

use App\Models\Trail;

/**
 * Service to derive quantitative trail metrics from coordinate / elevation data.
 */
class TrailMetricsService
{
    /**
     * Compute trail length (km) from coordinate array.
     * Coordinates: array of ['lat'=>float,'lng'=>float,(optional) 'elevation'|'ele'|'alt'=>float]
     */
    public function computeLengthFromCoordinates(array $coordinates): ?float
    {
        if (count($coordinates) < 2) {
            return null;
        }
        $distanceKm = 0.0;
        for ($i = 1; $i < count($coordinates); $i++) {
            $prev = $coordinates[$i-1];
            $curr = $coordinates[$i];
            if (!isset($prev['lat'],$prev['lng'],$curr['lat'],$curr['lng'])) {
                continue;
            }
            $distanceKm += $this->haversineKm($prev['lat'],$prev['lng'],$curr['lat'],$curr['lng']);
        }
        // Round to 2 decimals consistent with schema (decimal(8,2))
        return round($distanceKm, 2);
    }

    /**
     * Compute elevation statistics from coordinate or profile points if elevation keys present.
     * Returns [gain, high, low] or nulls if insufficient data.
     */
    public function computeElevationStats(array $coordinates): array
    {
        $elevations = [];
        foreach ($coordinates as $pt) {
            if (isset($pt['elevation'])) { $elevations[] = (float)$pt['elevation']; continue; }
            if (isset($pt['ele'])) { $elevations[] = (float)$pt['ele']; continue; }
            if (isset($pt['alt'])) { $elevations[] = (float)$pt['alt']; continue; }
        }
        if (count($elevations) < 2) {
            return [null,null,null];
        }
        $gain = 0.0;
        $threshold = 1.0; // meters, ignore tiny noise
        for ($i=1; $i<count($elevations); $i++) {
            $delta = $elevations[$i]-$elevations[$i-1];
            if ($delta > $threshold) { $gain += $delta; }
        }
        $high = max($elevations);
        $low = min($elevations);
        return [intval(round($gain)), intval(round($high)), intval(round($low))];
    }

    /**
     * Estimate hiking time (minutes) via simplified Naismith rule.
     * Base: 5 km/h -> 12 min per km. Add 60 min per 600 m ascent.
     */
    public function estimateTime(?float $distanceKm, ?int $elevationGainM): ?int
    {
        if ($distanceKm === null || $distanceKm <= 0) {
            return null;
        }
        $baseMinutes = $distanceKm * 12.0; // 5 km/h
        $gainMinutes = 0.0;
        if ($elevationGainM !== null && $elevationGainM > 0) {
            $gainMinutes = ($elevationGainM / 600.0) * 60.0;
        }
        return (int) round($baseMinutes + $gainMinutes);
    }

    /**
     * Optional derived difficulty suggestion (not persisted here) based on gain per km.
     */
    public function suggestDifficulty(?float $distanceKm, ?int $elevationGainM): ?string
    {
        if ($distanceKm === null || $distanceKm == 0 || $elevationGainM === null) {
            return null;
        }
        $gainPerKm = $elevationGainM / $distanceKm; // m per km
        if ($gainPerKm > 600) return 'advanced';
        if ($gainPerKm > 300) return 'intermediate';
        return 'beginner';
    }

    private function haversineKm(float $lat1,float $lon1,float $lat2,float $lon2): float
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    /**
     * Populate missing metrics on a Trail instance (not yet saved) if coordinates present.
     * Does not overwrite existing values.
     */
    public function fillMissingMetrics(Trail $trail): void
    {
        // Use coordinates as the source of trail data
        $raw = $trail->coordinates;
        if (!is_array($raw) || count($raw) < 2) return;

        // Optimize coordinates for representation
        $optimized = $this->optimizeCoordinates($raw);
        $trail->coordinates = $optimized;

        // Derive metrics (use raw for distance to avoid minor loss on simplification)
        if ($trail->length === null) {
            $trail->length = $this->computeLengthFromCoordinates($raw);
        }
        if ($trail->elevation_gain === null || $trail->elevation_high === null || $trail->elevation_low === null) {
            [$gain,$high,$low] = $this->computeElevationStats($raw);
            if ($trail->elevation_gain === null) { $trail->elevation_gain = $gain; }
            if ($trail->elevation_high === null) { $trail->elevation_high = $high; }
            if ($trail->elevation_low === null) { $trail->elevation_low = $low; }
        }
        if ($trail->estimated_time === null) {
            $trail->estimated_time = $this->estimateTime($trail->length, $trail->elevation_gain);
        }
    }

    private function optimizeCoordinates(array $coords): array
    {
        if (count($coords) < 3) return $coords;
        $optimized = [$coords[0]];
        $last = $coords[0];
        foreach ($coords as $i => $pt) {
            if ($i === 0 || $i === count($coords)-1) continue;
            if (!isset($pt['lat'],$pt['lng'],$last['lat'],$last['lng'])) continue;
            $d = $this->haversineKm($last['lat'],$last['lng'],$pt['lat'],$pt['lng']);
            if ($d > 0.01) { // >10m
                $optimized[] = $pt;
                $last = $pt;
            }
        }
        $optimized[] = end($coords);
        return $optimized;
    }
}
