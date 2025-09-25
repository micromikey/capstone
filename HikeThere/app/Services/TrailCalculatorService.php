<?php

namespace App\Services;

class TrailCalculatorService
{
    /**
     * Compute realistic hiking speed based on trail characteristics
     */
    public function computeHikingSpeedKph($trail)
    {
        // Base speed by difficulty
        $difficulty = strtolower($trail['difficulty'] ?? 'moderate');
        
        if (str_contains($difficulty, 'easy') || str_contains($difficulty, 'beginner')) {
            $baseSpeed = 4.8; // km/h (brisk)
        } elseif (str_contains($difficulty, 'hard') || str_contains($difficulty, 'advanced')) {
            $baseSpeed = 2.6; // slower on technical terrain
        } else {
            $baseSpeed = 3.6; // moderate
        }

        // Elevation penalty: meters of ascent per km slows pace
        $elevation = floatval($trail['elevation_m'] ?? 0);
        $distance = max(0.1, floatval($trail['distance_km'] ?? 0));
        $elevationPerKm = $elevation / $distance;

        // Apply penalty of ~0.06 km/h for every 100m/km of climb
        $penalty = ($elevationPerKm / 100) * 0.06;
        $speed = max(0.9, $baseSpeed - $penalty);

        // Overnight hiking with packs is slightly slower
        if (!empty($trail['overnight_allowed'])) {
            $speed *= 0.95;
        }

        return round($speed, 2);
    }

    /**
     * Derive duration from trail data when not explicitly provided
     */
    public function deriveDurationFromTrail($trail, $routeData = [])
    {
        $defaultDays = 1;

        // Try to parse duration from trail duration string
        $trailDurationLabel = $trail['duration'] ?? $routeData['duration'] ?? null;
        if (!empty($trailDurationLabel) && preg_match('/(\d+)\s*day/i', $trailDurationLabel, $matches)) {
            return max(1, intval($matches[1]));
        }

        // Calculate from estimated time
        if (!empty($trail['estimated_time'])) {
            $minutes = intval($trail['estimated_time']);
            return max(1, (int) ceil($minutes / (60 * 8))); // assume 8h hiking per day
        }

        if (!empty($routeData['estimated_duration_hours'])) {
            $hours = floatval($routeData['estimated_duration_hours']);
            return max(1, (int) ceil($hours / 8));
        }

        return $defaultDays;
    }

    /**
     * Calculate distance for a specific day using route data when available
     */
    public function calculateDayDistance($dayIndex, $totalKm, $durationDays, $routeData = [])
    {
        $defaultPerDay = $totalKm / max(1, $durationDays);

        try {
            if (!empty($routeData) && is_array($routeData)) {
                $legs = $routeData['legs'] ?? $routeData['route']['legs'] ?? $routeData['routes'][0]['legs'] ?? null;
                
                if (is_array($legs) && count($legs) > 0) {
                    return $this->calculateDayDistanceFromLegs($dayIndex, $durationDays, $legs);
                }
            }
        } catch (\Throwable $e) {
            // Fall back to equal split
        }

        return $defaultPerDay;
    }

    /**
     * Calculate day distance from route legs
     */
    protected function calculateDayDistanceFromLegs($dayIndex, $durationDays, $legs)
    {
        // Build per-leg km array
        $legKms = [];
        $totalLegsKm = 0.0;
        
        foreach ($legs as $leg) {
            $meters = $leg['distance_m'] ?? ($leg['distance']['value'] ?? ($leg['distance_meters'] ?? 0));
            $meters = is_numeric($meters) ? floatval($meters) : 0;
            $km = $meters / 1000.0;
            $legKms[] = $km;
            $totalLegsKm += $km;
        }

        $legsCount = count($legKms);
        
        // Map legs into day buckets
        $startIdx = (int) floor(($dayIndex - 1) * $legsCount / $durationDays);
        $endIdx = (int) ceil($dayIndex * $legsCount / $durationDays) - 1;
        $endIdx = max($startIdx, min($endIdx, $legsCount - 1));

        $sum = 0.0;
        for ($i = $startIdx; $i <= $endIdx; $i++) {
            $sum += ($legKms[$i] ?? 0);
        }

        return $sum > 0 ? $sum : ($totalLegsKm / max(1, $durationDays));
    }

    /**
     * Format elapsed time in human readable format
     */
    public function formatElapsed($minutes)
    {
        $m = intval($minutes);
        if ($m < 60) {
            return $m . 'm';
        }
        
        $h = intdiv($m, 60);
        $rem = $m % 60;
        return $h . 'h' . ($rem ? ' ' . $rem . 'm' : '');
    }

    /**
     * Format distance in kilometers
     */
    public function formatDistanceKm($km)
    {
        if ($km === null) {
            return '-';
        }
        return number_format(floatval($km), 2) . ' km';
    }
}