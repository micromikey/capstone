<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Trail;
use App\Models\Build;
use App\Services\TrailCalculatorService;
use App\Services\WeatherHelperService;
use App\Services\DataNormalizerService;
use App\Services\IntelligentItineraryService;
use App\Services\DurationParserService;

class ItineraryGeneratorService
{
    protected $trailCalculator;
    protected $weatherHelper;
    protected $dataNormalizer;
    protected $intelligentItinerary;
    protected $durationParser;

    public function __construct(
        TrailCalculatorService $trailCalculator,
        WeatherHelperService $weatherHelper,
        DataNormalizerService $dataNormalizer,
        IntelligentItineraryService $intelligentItinerary,
        DurationParserService $durationParser
    ) {
        $this->trailCalculator = $trailCalculator;
        $this->weatherHelper = $weatherHelper;
        $this->dataNormalizer = $dataNormalizer;
        $this->intelligentItinerary = $intelligentItinerary;
        $this->durationParser = $durationParser;
    }

    /**
     * Generate a complete itinerary from raw input data
     */
    public function generateItinerary($itinerary = null, $trail = null, $build = null, $weatherData = [])
    {
        // Normalize all input data
        $normalizedData = $this->dataNormalizer->normalizeInputs($itinerary, $trail, $build, $weatherData);
        
        // Extract normalized values
        $itinerary = $normalizedData['itinerary'];
        $trail = $normalizedData['trail'];
        $build = $normalizedData['build'];
        $weatherData = $normalizedData['weatherData'];
        $routeData = $normalizedData['routeData'];

        // Calculate duration and dates
        $dateInfo = $this->calculateDateInfo($itinerary, $trail, $routeData);
        
        // Generate activities for each day and night
        $dayActivities = $this->generateDayActivities($itinerary, $trail, $dateInfo, $routeData);
        $nightActivities = $this->generateNightActivities($itinerary, $dateInfo, $dayActivities);

        return [
            'itinerary' => $itinerary,
            'trail' => $trail,
            'build' => $build,
            'weatherData' => $weatherData,
            'routeData' => $routeData,
            'dateInfo' => $dateInfo,
            'dayActivities' => $dayActivities,
            'nightActivities' => $nightActivities,
        ];
    }

    /**
     * Calculate duration, dates, and timing information
     */
    protected function calculateDateInfo($itinerary, $trail, $routeData)
    {
        $durationDays = isset($itinerary['duration_days']) ? intval($itinerary['duration_days']) : null;
        $nights = isset($itinerary['nights']) ? intval($itinerary['nights']) : null;
        
        // Try to parse duration from trail package data if available
        if (empty($durationDays) && !empty($trail)) {
            $trailDuration = null;
            
            // Handle different trail data formats
            if (is_object($trail) && isset($trail->duration)) {
                $trailDuration = $trail->duration;
            } elseif (is_array($trail) && isset($trail['duration'])) {
                $trailDuration = $trail['duration'];
            }
            
            // Use DurationParserService to parse trail duration
            if (!empty($trailDuration)) {
                try {
                    $parsedDuration = $this->durationParser->normalizeDuration($trailDuration);
                    if ($parsedDuration) {
                        $durationDays = $parsedDuration['days'];
                        $nights = $parsedDuration['nights'];
                    }
                } catch (\Exception $e) {
                    // Log error and fallback to original calculation
                    Log::warning("Failed to parse trail duration: " . $trailDuration, ['error' => $e->getMessage()]);
                }
            }
        }
        
        // Fallback to trail calculator if still no duration
        if (empty($durationDays)) {
            $durationDays = $this->trailCalculator->deriveDurationFromTrail($trail, $routeData);
        }

        // Ensure nights is set properly
        if (is_null($nights)) {
            $nights = max(0, $durationDays - 1);
        }
        
        $startTime = $itinerary['start_time'] ?? '06:00';
        $startDate = isset($itinerary['start_date']) ? Carbon::parse($itinerary['start_date']) : Carbon::today();
        $endDate = $startDate->copy()->addDays(max(0, $durationDays - 1));

        return [
            'duration_days' => $durationDays,
            'nights' => $nights,
            'start_time' => $startTime,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * Generate activities for all days
     */
    protected function generateDayActivities($itinerary, $trail, $dateInfo, $routeData)
    {
        $activitiesByDay = [];
        $userActivities = $itinerary['activities'] ?? [];
        
        // Handle daily_schedule format as well
        if (empty($userActivities) && !empty($itinerary['daily_schedule']) && is_array($itinerary['daily_schedule'])) {
            foreach ($itinerary['daily_schedule'] as $idx => $day) {
                $userActivities[$idx + 1] = is_array($day) && isset($day['activities']) ? $day['activities'] : [];
            }
        }

        for ($day = 1; $day <= $dateInfo['duration_days']; $day++) {
            $dayUserActivities = $userActivities[$day] ?? [];
            
            if (empty($dayUserActivities)) {
                // Use intelligent generation based on user profile and preferences
                $dayActivities = $this->intelligentItinerary->generatePersonalizedActivities(
                    $itinerary, $trail, $dateInfo, $routeData, $day
                );
                
                // Fallback to default plan if intelligent generation fails
                if (empty($dayActivities)) {
                    $dayActivities = $this->generateDayPlan($day, $trail, $dateInfo, $routeData);
                }
            } else {
                // Expand user activities with scaffold
                $dayActivities = $this->expandDayActivities($dayUserActivities, $trail, $day, $dateInfo, $routeData);
            }

            // Merge side trips and stopovers
            $dayActivities = $this->mergeSideTripsIntoDay($dayActivities, $itinerary, $day);
            
            // Remove duplicate activities based on similar titles and times
            $dayActivities = $this->removeDuplicateActivities($dayActivities);
            
            $activitiesByDay[$day] = $dayActivities;
        }

        return $activitiesByDay;
    }

    /**
     * Generate activities for all nights
     */
    protected function generateNightActivities($itinerary, $dateInfo, $dayActivities)
    {
        $nightActivitiesByIndex = [];
        
        // Get user-provided night activities
        $userNightActivities = $itinerary['night_activities'] ?? 
                              $itinerary['nights'] ?? 
                              $itinerary['nights_activities'] ?? [];

        for ($night = 1; $night <= $dateInfo['nights']; $night++) {
            $nightUserActivities = $userNightActivities[$night] ?? [];
            
            if (empty($nightUserActivities)) {
                // Find arrival time from corresponding day
                $arrivalMinutes = $this->getLastActivityTime($dayActivities[$night] ?? []);
                $nightActivities = $this->generateNightPlan($night, $arrivalMinutes);
            } else {
                $nightActivities = $nightUserActivities;
            }
            
            $nightActivitiesByIndex[$night] = $nightActivities;
        }

        return $nightActivitiesByIndex;
    }

    /**
     * Generate a realistic day plan based on trail characteristics
     */
    public function generateDayPlan($dayIndex, $trail, $dateInfo, $routeData = [])
    {
        $totalKm = floatval($trail['distance_km'] ?? 10);
        $durationDays = $dateInfo['duration_days'];
        
        // Calculate distance for this specific day
        $distPerDay = $this->trailCalculator->calculateDayDistance($dayIndex, $totalKm, $durationDays, $routeData);
        
        // Calculate hiking time
        $speed = $this->trailCalculator->computeHikingSpeedKph($trail);
        $hikingHours = $distPerDay / $speed;
        $bufferHours = max(0.5, $hikingHours * 0.2);
        $totalHikeHours = $hikingHours + $bufferHours;
        $hikeMinutes = intval(round($totalHikeHours * 60));

        // Use trail's estimated_time if available, otherwise calculate
        $trailEstimatedTime = $trail['estimated_time'] ?? null;
        if ($trailEstimatedTime) {
            // Parse estimated time (e.g., "8-10 hours" -> use average)
            if (preg_match('/(\d+)(?:-(\d+))?\s*hours?/i', $trailEstimatedTime, $matches)) {
                $minHours = intval($matches[1]);
                $maxHours = isset($matches[2]) ? intval($matches[2]) : $minHours;
                $avgHours = ($minHours + $maxHours) / 2;
                $hikeMinutes = intval($avgHours * 60);
            } else {
                // Fallback to calculated time
                $hikeMinutes = intval(round($totalHikeHours * 60));
            }
        } else {
            $hikeMinutes = intval(round($totalHikeHours * 60));
        }

        // Generate trail activities only (from trailhead to end)
        $activities = [];
        $cursor = 0;

        // Trail start activity
        $trailName = $trail['name'] ?? 'Trail';
        $startTitle = 'Start ' . $trailName;
        $startDescription = 'Begin your hike';
        
        $activities[] = array_merge(
            $this->createActivity($cursor, 0.0, $startTitle, 'hike', $trailName),
            ['description' => $startDescription]
        );

        // Create more detailed trail activities for better experience
        $activities[] = $this->createActivity(
            $cursor + 15, 
            0.0, 
            'Safety Briefing & Equipment Check', 
            'prep', 
            'Trailhead'
        );

        // Early trail segment (first 20% - usually steeper/more challenging)
        $earlyBreak = intval(round($hikeMinutes * 0.2));
        $activities[] = $this->createActivity(
            $cursor + $earlyBreak, 
            round($distPerDay * 0.2, 2), 
            'First Water Break', 
            'rest', 
            'Trail'
        );

        // Quarter point with scenic opportunity
        $quarterTime = intval(round($hikeMinutes * 0.35));
        $activities[] = $this->createActivity(
            $cursor + $quarterTime, 
            round($distPerDay * 0.35, 2), 
            'Scenic Photo Stop', 
            'photo', 
            'Viewpoint'
        );

        // Midday break (most important meal)
        $halfTime = intval(round($hikeMinutes * 0.5));
        $activities[] = $this->createActivity(
            $cursor + $halfTime, 
            round($distPerDay / 2, 2), 
            'Lunch Break & Rest', 
            'meal', 
            'Rest Area'
        );

        // Post-lunch energy check
        $postLunch = intval(round($hikeMinutes * 0.65));
        $activities[] = $this->createActivity(
            $postLunch, 
            round($distPerDay * 0.65, 2), 
            'Hydration & Navigation Check', 
            'checkpoint', 
            'Trail'
        );

        // Final approach (challenging part)
        $finalApproach = intval(round($hikeMinutes * 0.85));
        $approachTitle = ($dayIndex < $durationDays) ? 'Final Approach to Campsite' : 'Final Push to Summit';
        $activities[] = $this->createActivity(
            $finalApproach, 
            round($distPerDay * 0.85, 2), 
            $approachTitle, 
            'climb', 
            'Trail'
        );

        // Trail completion with celebration
        $endTitle = ($dayIndex < $durationDays) ? 'Arrive & Set Up Camp' : 'Summit Achievement';
        $endType = ($dayIndex < $durationDays) ? 'camp' : 'summit';
        $endLocation = ($dayIndex < $durationDays) ? 'Campsite' : 'Summit';
        $endDescription = ($dayIndex < $durationDays) ? 'Rest and prepare for next day' : 'Celebrate and enjoy the view';
        
        $activities[] = array_merge(
            $this->createActivity(
                $cursor + $hikeMinutes, 
                round($distPerDay, 2), 
                $endTitle, 
                $endType, 
                $endLocation
            ),
            ['description' => $endDescription]
        );

        // Add descent activities if it's a summit day (return trail)
        if ($dayIndex >= $durationDays && $hikeMinutes > 240) { // 4+ hour trails usually require descent
            $descentStart = $cursor + $hikeMinutes + 30; // 30 min rest at summit
            $descentTime = intval($hikeMinutes * 0.6); // Descent is typically faster
            
            $activities[] = $this->createActivity(
                $descentStart, 
                round($distPerDay, 2), 
                'Begin Descent', 
                'descent', 
                'Summit'
            );
            
            $activities[] = $this->createActivity(
                $descentStart + intval($descentTime / 2), 
                round($distPerDay * 0.7, 2), 
                'Descent Rest Stop', 
                'rest', 
                'Trail'
            );
            
            $activities[] = $this->createActivity(
                $descentStart + $descentTime, 
                0.0, 
                'Return to Trailhead', 
                'finish', 
                'Trailhead'
            );
        }

        return $activities;
    }

    /**
     * Generate night plan activities
     */
    public function generateNightPlan($nightIndex, $arrivalMinutes = 1080)
    {
        $activities = [];
        $cursor = max(0, intval($arrivalMinutes));

        $activities[] = $this->createActivity($cursor, null, 'Set up Camp / Check-in', 'camp', 'Campsite');
        $cursor += 45;
        
        $activities[] = $this->createActivity($cursor, null, 'Dinner & Rest', 'meal', 'Campsite');
        $cursor += 60;
        
        $activities[] = $this->createActivity($cursor, null, 'Stargazing / Campfire', 'relax', 'Campsite');
        $cursor += 90;
        
        $activities[] = $this->createActivity($cursor, null, 'Sleep', 'overnight', 'Tents/Campsite');

        return $activities;
    }

    /**
     * Expand sparse user activities using generated scaffold
     */
    protected function expandDayActivities($userActivities, $trail, $dayIndex, $dateInfo, $routeData)
    {
        if (!empty($userActivities) && count($userActivities) > 3) {
            return $userActivities;
        }

        $scaffold = $this->generateDayPlan($dayIndex, $trail, $dateInfo, $routeData);
        $result = [];

        foreach ($scaffold as $scaffoldActivity) {
            $matched = null;
            
            foreach ($userActivities as $userActivity) {
                if ($this->activitiesMatch($scaffoldActivity, $userActivity)) {
                    $matched = $userActivity;
                    if (!isset($matched['minutes'])) {
                        $matched['minutes'] = $scaffoldActivity['minutes'];
                    }
                    break;
                }
            }

            $result[] = $matched ?: $scaffoldActivity;
        }

        // Add any unmatched user activities
        foreach ($userActivities as $userActivity) {
            if (!$this->isActivityInResult($userActivity, $result)) {
                $result[] = $userActivity;
            }
        }

        // Sort by minutes
        usort($result, function($a, $b) {
            return intval($a['minutes'] ?? 0) <=> intval($b['minutes'] ?? 0);
        });

        return $result;
    }

    /**
     * Merge side trips into day activities
     */
    protected function mergeSideTripsIntoDay($activities, $itinerary, $dayIndex)
    {
        $sideTrips = $itinerary['side_trips'] ?? $itinerary['sidetrips'] ?? [];
        $stopOvers = $itinerary['stop_overs'] ?? $itinerary['stopovers'] ?? [];
        
        $extras = array_slice($sideTrips, 0, 2);
        $extras = array_merge($extras, array_slice($stopOvers, 0, 2));
        
        if (empty($extras)) {
            return $activities;
        }

        return $this->mergeExtrasIntoActivities($activities, $extras);
    }

    /**
     * Helper method to create activity array
     */
    protected function createActivity($minutes, $cumDistance, $title, $type, $location)
    {
        return [
            'minutes' => $minutes,
            'cum_minutes' => $minutes,
            'cum_distance_km' => $cumDistance,
            'title' => $title,
            'type' => $type,
            'location' => $location,
        ];
    }

    /**
     * Check if two activities match for merging
     */
    protected function activitiesMatch($scaffold, $user)
    {
        $scaffoldTitle = strtolower($scaffold['title'] ?? '');
        $userTitle = strtolower($user['title'] ?? '');
        
        if (!empty($userTitle) && str_contains($scaffoldTitle, $userTitle)) {
            return true;
        }

        $scaffoldLocation = strtolower($scaffold['location'] ?? '');
        $userLocation = strtolower($user['location'] ?? '');
        
        if (!empty($userLocation) && !empty($scaffoldLocation) && 
            str_contains($userLocation, $scaffoldLocation)) {
            return true;
        }

        return false;
    }

    /**
     * Check if activity is already in result array
     */
    protected function isActivityInResult($activity, $result)
    {
        foreach ($result as $resultActivity) {
            if (($resultActivity['title'] ?? '') === ($activity['title'] ?? '') &&
                ($resultActivity['minutes'] ?? null) === ($activity['minutes'] ?? null)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the last activity time from a day's activities
     */
    protected function getLastActivityTime($activities)
    {
        if (empty($activities)) {
            return 1080; // Default 18:00
        }

        $minutes = array_map(function($a) {
            return intval($a['minutes'] ?? 0);
        }, $activities);

        return max($minutes);
    }

    /**
     * Merge extras into activities using simple heuristic
     */
    protected function mergeExtrasIntoActivities($activities, $extras)
    {
        if (empty($extras)) {
            return $activities;
        }

        $result = [];
        foreach ($activities as $activity) {
            $result[] = $activity;
            
            if (!empty($extras)) {
                $extra = array_shift($extras);
                if ($extra) {
                    $result[] = $extra;
                }
            }
        }

        // Append any remaining extras
        foreach ($extras as $extra) {
            $result[] = $extra;
        }

        return $result;
    }

    /**
     * Remove duplicate activities with similar titles and times
     */
    protected function removeDuplicateActivities($activities)
    {
        if (empty($activities)) {
            return $activities;
        }

        $uniqueActivities = [];
        $seenTitles = [];

        foreach ($activities as $activity) {
            $title = strtolower(trim($activity['title'] ?? ''));
            $minutes = intval($activity['minutes'] ?? 0);
            
            // Create a unique key based on title keywords and time
            $titleWords = explode(' ', $title);
            $keyWords = [];
            
            // Extract key words, ignoring common words
            foreach ($titleWords as $word) {
                if (strlen($word) > 2 && !in_array($word, ['the', 'and', 'for', 'your', 'to'])) {
                    $keyWords[] = $word;
                }
            }
            
            $uniqueKey = implode('_', $keyWords) . '_' . intval($minutes / 30); // Group by 30-min intervals
            
            // Skip if we've seen a similar activity
            if (!in_array($uniqueKey, $seenTitles)) {
                $seenTitles[] = $uniqueKey;
                $uniqueActivities[] = $activity;
            }
        }

        return $uniqueActivities;
    }
}