<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Enhanced Distance Calculator Service
 * 
 * This service provides accurate distance calculations for hiking activities
 * using multiple calculation methods and validation.
 */
class EnhancedDistanceCalculatorService
{
    /**
     * Calculate accurate cumulative distances for activities
     * 
     * @param array $activities List of activities
     * @param float $totalDistance Total trail distance in km
     * @param array $routeData Optional route data with waypoints
     * @return array Activities with corrected cum_distance_km values
     */
    public function calculateAccurateDistances($activities, $totalDistance, $routeData = [])
    {
        if (empty($activities)) {
            return $activities;
        }

        // Method 1: Try to use route waypoints if available
        if (!empty($routeData) && isset($routeData['waypoints'])) {
            $distances = $this->calculateFromWaypoints($activities, $routeData['waypoints']);
            if ($distances) {
                return $this->applyDistancesToActivities($activities, $distances, $totalDistance);
            }
        }

        // Method 2: Use proportional distribution based on activity timing
        $distances = $this->calculateProportionalDistances($activities, $totalDistance);
        return $this->applyDistancesToActivities($activities, $distances, $totalDistance);
    }

    /**
     * Calculate distances from route waypoints
     */
    protected function calculateFromWaypoints($activities, $waypoints)
    {
        try {
            $distances = [];
            $waypointIndex = 0;
            
            foreach ($activities as $index => $activity) {
                if ($waypointIndex < count($waypoints)) {
                    $distances[$index] = $waypoints[$waypointIndex]['cumulative_distance_km'] ?? 0;
                    $waypointIndex++;
                } else {
                    // Extrapolate for remaining activities
                    $distances[$index] = end($distances) ?? 0;
                }
            }
            
            return $distances;
        } catch (\Exception $e) {
            Log::warning('Waypoint distance calculation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate proportional distances based on activity timing and type
     */
    protected function calculateProportionalDistances($activities, $totalDistance)
    {
        $totalActivities = count($activities);
        $distances = [];
        
        // Calculate based on activity progression and type weighting
        foreach ($activities as $index => $activity) {
            $activityType = strtolower($activity['type'] ?? 'activity');
            $activityMinutes = $activity['cum_minutes'] ?? $activity['minutes'] ?? 0;
            
            // Calculate progress ratio based on activity position and timing
            $progressRatio = $this->calculateProgressRatio($activity, $activities, $index);
            
            // Apply activity type weighting
            $typeWeight = $this->getActivityTypeWeight($activityType);
            
            // Calculate cumulative distance
            $cumulativeDistance = $totalDistance * $progressRatio * $typeWeight;
            
            // Ensure logical progression (no distance decrease)
            if ($index > 0 && $cumulativeDistance < $distances[$index - 1]) {
                $cumulativeDistance = $distances[$index - 1] + 0.5; // Minimum 0.5km progress
            }
            
            $distances[$index] = round($cumulativeDistance, 2);
        }
        
        // Ensure the last activity reaches the total distance
        if (!empty($distances)) {
            $lastIndex = count($distances) - 1;
            $distances[$lastIndex] = $totalDistance;
        }
        
        return $distances;
    }

    /**
     * Calculate progress ratio for an activity
     */
    protected function calculateProgressRatio($activity, $allActivities, $currentIndex)
    {
        $totalActivities = count($allActivities);
        
        // Base ratio on position
        $positionRatio = ($currentIndex + 1) / $totalActivities;
        
        // Adjust based on activity timing if available
        $activityMinutes = $activity['cum_minutes'] ?? $activity['minutes'] ?? 0;
        if ($activityMinutes > 0) {
            $totalMinutes = end($allActivities)['cum_minutes'] ?? 480; // Default 8 hours
            $timeRatio = $activityMinutes / $totalMinutes;
            
            // Weighted average of position and time ratios
            $positionRatio = ($positionRatio * 0.3) + ($timeRatio * 0.7);
        }
        
        // Special handling for key activity types
        $activityType = strtolower($activity['type'] ?? 'activity');
        switch ($activityType) {
            case 'start':
            case 'prep':
                return 0.0; // Starting point
                
            case 'summit':
                return 0.85; // Summit typically at 85% of trail
                
            case 'descent':
                if (strpos(strtolower($activity['title'] ?? ''), 'begin') !== false) {
                    return 0.90; // Start of descent
                }
                break;
                
            case 'camp':
            case 'return':
                return 1.0; // End point
        }
        
        return min(1.0, max(0.0, $positionRatio));
    }

    /**
     * Get weight factor for different activity types
     */
    protected function getActivityTypeWeight($activityType)
    {
        $weights = [
            'start' => 0.0,
            'prep' => 0.05,
            'photo' => 1.0,
            'viewpoint' => 1.0,
            'rest' => 1.0,
            'meal' => 1.0,
            'lunch' => 1.0,
            'climb' => 1.1,  // Climbing covers more ground
            'ascent' => 1.1,
            'summit' => 0.95, // Summit often not the furthest point
            'descent' => 1.0,
            'camp' => 1.0,
            'return' => 1.0,
        ];
        
        return $weights[$activityType] ?? 1.0;
    }

    /**
     * Apply calculated distances to activities
     */
    protected function applyDistancesToActivities($activities, $distances, $totalDistance)
    {
        foreach ($activities as $index => &$activity) {
            $activity['cum_distance_km'] = $distances[$index] ?? 0;
        }
        
        // Validate and log the calculation
        $this->validateDistanceCalculation($activities, $totalDistance);
        
        return $activities;
    }

    /**
     * Validate distance calculation results
     */
    protected function validateDistanceCalculation($activities, $totalDistance)
    {
        if (empty($activities)) {
            return;
        }
        
        $lastActivity = end($activities);
        $calculatedTotal = $lastActivity['cum_distance_km'] ?? 0;
        $difference = abs($totalDistance - $calculatedTotal);
        
        if ($difference > 1.0) {
            Log::warning("Distance calculation variance detected", [
                'expected_total' => $totalDistance,
                'calculated_total' => $calculatedTotal,
                'difference' => $difference,
                'activity_count' => count($activities)
            ]);
        }
    }

    /**
     * Calculate distance between two geographic coordinates (Haversine formula)
     */
    public function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);
        
        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLonRad / 2) * sin($deltaLonRad / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Get distance calculation summary for debugging
     */
    public function getDistanceCalculationSummary($activities, $totalDistance)
    {
        $summary = [
            'total_activities' => count($activities),
            'trail_distance' => $totalDistance,
            'calculated_distance' => 0,
            'segments' => [],
            'validation' => []
        ];
        
        $previousDistance = 0;
        foreach ($activities as $index => $activity) {
            $cumDistance = $activity['cum_distance_km'] ?? 0;
            $segmentDistance = $cumDistance - $previousDistance;
            
            $summary['segments'][] = [
                'activity' => $activity['title'] ?? 'Unknown',
                'type' => $activity['type'] ?? 'unknown',
                'cumulative_km' => $cumDistance,
                'segment_km' => $segmentDistance
            ];
            
            $summary['calculated_distance'] = $cumDistance;
            $previousDistance = $cumDistance;
        }
        
        $difference = abs($totalDistance - $summary['calculated_distance']);
        $summary['validation'] = [
            'difference_km' => $difference,
            'accuracy_percentage' => $totalDistance > 0 ? (1 - $difference / $totalDistance) * 100 : 0,
            'status' => $difference <= 1.0 ? 'GOOD' : 'NEEDS_REVIEW'
        ];
        
        return $summary;
    }
}