<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Trail;
use App\Models\AssessmentResult;

class IntelligentItineraryService
{
    protected $trailCalculator;
    protected $weatherHelper;

    public function __construct(
        TrailCalculatorService $trailCalculator,
        WeatherHelperService $weatherHelper
    ) {
        $this->trailCalculator = $trailCalculator;
        $this->weatherHelper = $weatherHelper;
    }

    /**
     * Generate intelligent, personalized activities based on user profile
     */
    public function generatePersonalizedActivities($itinerary, $trail, $dateInfo, $routeData, $dayIndex)
    {
        // Get user context
        $user = \Illuminate\Support\Facades\Auth::user();
        $assessment = $user?->latestAssessmentResult;
        $preferences = $user?->hiking_preferences ?? [];
        
        // Get trail and package characteristics
        $trailModel = $this->resolveTrailModel($trail);
        $package = $trailModel?->package;
        
        // Create personalization profile
        $profile = $this->buildPersonalizationProfile($user, $assessment, $preferences, $trailModel, $package);
        
        // Generate activities based on profile
        return $this->generateActivitiesFromProfile($profile, $trail, $dateInfo, $routeData, $dayIndex);
    }

    /**
     * Build comprehensive personalization profile
     */
    protected function buildPersonalizationProfile($user, $assessment, $preferences, $trailModel, $package)
    {
        $profile = [
            // User characteristics
            'fitness_level' => $this->getFitnessLevel($assessment),
            'experience_level' => $this->getExperienceLevel($assessment, $preferences),
            'risk_tolerance' => $this->getRiskTolerance($assessment, $preferences),
            'pace_preference' => $preferences['pace'] ?? 'moderate',
            'break_frequency' => $this->getBreakFrequency($assessment, $preferences),
            
            // Interests and preferences
            'photography_interest' => $preferences['interests']['photography'] ?? false,
            'nature_observation' => $preferences['interests']['wildlife'] ?? $preferences['interests']['flora'] ?? false,
            'social_hiking' => $preferences['social_preference'] ?? 'mixed',
            'challenge_seeking' => $preferences['challenge_level'] ?? 'moderate',
            
            // Trail characteristics
            'trail_difficulty' => strtolower($trailModel?->difficulty ?? 'moderate'),
            'trail_type' => $this->getTrailType($trailModel),
            'scenic_opportunities' => $this->getScenicOpportunities($trailModel, $package),
            'trail_features' => $this->getTrailFeatures($trailModel),
            
            // Package offerings
            'guided_experience' => $package?->transport_included ?? false,
            'group_size' => $this->estimateGroupSize($package),
            'included_activities' => $this->getIncludedActivities($package),
            
            // Environmental factors
            'weather_sensitivity' => $assessment?->weather_score ?? 50,
            'emergency_preparedness' => $assessment?->emergency_score ?? 50,
        ];

        return $profile;
    }

    /**
     * Generate activities based on personalization profile
     */
    protected function generateActivitiesFromProfile($profile, $trail, $dateInfo, $routeData, $dayIndex)
    {
        $activities = [];
        $cursor = 0;
        
        // Calculate hiking time based on fitness and pace
        $baseHikeTime = $this->calculateBaseHikeTime($trail, $profile);
        $adjustedHikeTime = $this->adjustTimeForProfile($baseHikeTime, $profile);
        
        // 1. Trail Start - Always included
        $activities[] = $this->createTrailStartActivity($cursor, $trail, $profile, $dayIndex, $dateInfo);
        
        // 2. Early activities based on experience level
        if ($profile['experience_level'] === 'beginner' || $profile['emergency_preparedness'] < 60) {
            $cursor += 15;
            $activities[] = $this->createSafetyBriefingActivity($cursor, $profile);
        }
        
        // 3. Break pattern based on fitness and preferences
        $breakPattern = $this->calculateBreakPattern($adjustedHikeTime, $profile);
        
        foreach ($breakPattern as $breakPoint) {
            $cursor += $breakPoint['offset'];
            $activity = $this->createBreakActivity($cursor, $breakPoint, $trail, $profile);
            if ($activity) {
                $activities[] = $activity;
            }
        }
        
        // 4. Interest-based activities
        if ($profile['photography_interest']) {
            $photoOps = $this->identifyPhotoOpportunities($adjustedHikeTime, $trail, $profile);
            foreach ($photoOps as $photoOp) {
                $activities[] = $this->createPhotoActivity($photoOp['time'], $photoOp, $profile);
            }
        }
        
        if ($profile['nature_observation']) {
            $natureStops = $this->identifyNatureStops($adjustedHikeTime, $trail, $profile);
            foreach ($natureStops as $stop) {
                $activities[] = $this->createNatureActivity($stop['time'], $stop, $profile);
            }
        }
        
        // 5. Challenge-based activities
        if ($profile['challenge_seeking'] === 'high' && $profile['fitness_level'] >= 7) {
            $challenges = $this->identifyOptionalChallenges($trail, $profile);
            foreach ($challenges as $challenge) {
                $activities[] = $this->createChallengeActivity($challenge['time'], $challenge, $profile);
            }
        }
        
        // 6. Trail completion
        $activities[] = $this->createTrailEndActivity($cursor + $adjustedHikeTime, $trail, $profile, $dayIndex, $dateInfo);
        
        // Sort by time and return
        usort($activities, fn($a, $b) => ($a['minutes'] ?? 0) <=> ($b['minutes'] ?? 0));
        
        return $activities;
    }

    /**
     * Calculate break pattern based on user profile
     */
    protected function calculateBreakPattern($hikeTime, $profile)
    {
        $pattern = [];
        
        // Base break frequency
        $baseInterval = match($profile['break_frequency']) {
            'frequent' => 45,  // Every 45 minutes
            'normal' => 60,    // Every hour
            'minimal' => 90,   // Every 1.5 hours
            default => 60
        };
        
        // Adjust for fitness level
        if ($profile['fitness_level'] < 5) {
            $baseInterval = max(30, $baseInterval - 15);
        } elseif ($profile['fitness_level'] > 8) {
            $baseInterval += 15;
        }
        
        // Generate break points
        $currentTime = $baseInterval;
        while ($currentTime < $hikeTime - 30) { // Don't add breaks too close to end
            $breakType = $this->determineBreakType($currentTime, $hikeTime, $profile);
            $pattern[] = [
                'offset' => $currentTime,
                'type' => $breakType,
                'duration' => $this->getBreakDuration($breakType, $profile)
            ];
            $currentTime += $baseInterval;
        }
        
        return $pattern;
    }

    /**
     * Create personalized activity based on type and profile
     */
    protected function createBreakActivity($time, $breakPoint, $trail, $profile)
    {
        $distance = $this->calculateDistanceAtTime($time, $trail);
        
        return match($breakPoint['type']) {
            'hydration' => [
                'minutes' => $time,
                'cum_minutes' => $time,
                'cum_distance_km' => $distance,
                'title' => $profile['fitness_level'] < 5 ? 'Rest & Hydration Break' : 'Quick Water Break',
                'type' => 'rest',
                'location' => 'Trail',
                'description' => $this->getHydrationAdvice($profile)
            ],
            'meal' => [
                'minutes' => $time,
                'cum_minutes' => $time,
                'cum_distance_km' => $distance,
                'title' => 'Lunch Break',
                'type' => 'meal',
                'location' => 'Rest Area',
                'description' => $this->getMealAdvice($profile, $time)
            ],
            'scenic' => [
                'minutes' => $time,
                'cum_minutes' => $time,
                'cum_distance_km' => $distance,
                'title' => 'Scenic Viewpoint',
                'type' => 'photo',
                'location' => 'Viewpoint',
                'description' => 'Perfect spot for photos and enjoying the view'
            ],
            default => null
        };
    }

    // Helper methods for profile analysis
    
    protected function getFitnessLevel($assessment)
    {
        if (!$assessment) return 5; // Default moderate
        
        $fitnessScore = $assessment->fitness_score ?? 50;
        return intval($fitnessScore / 10); // Convert to 1-10 scale
    }
    
    protected function getExperienceLevel($assessment, $preferences)
    {
        $overallScore = $assessment?->overall_score ?? 50;
        $experienceYears = $preferences['experience_years'] ?? 1;
        
        if ($overallScore < 40 || $experienceYears < 1) return 'beginner';
        if ($overallScore > 75 && $experienceYears > 3) return 'advanced';
        return 'intermediate';
    }
    
    protected function getRiskTolerance($assessment, $preferences)
    {
        $emergencyScore = $assessment?->emergency_score ?? 50;
        $riskPreference = $preferences['risk_tolerance'] ?? 'moderate';
        
        if ($emergencyScore < 40 || $riskPreference === 'low') return 'low';
        if ($emergencyScore > 75 && $riskPreference === 'high') return 'high';
        return 'moderate';
    }
    
    protected function getBreakFrequency($assessment, $preferences)
    {
        $fitnessScore = $assessment?->fitness_score ?? 50;
        $preference = $preferences['break_frequency'] ?? null;
        
        if ($preference) return $preference;
        
        if ($fitnessScore < 40) return 'frequent';
        if ($fitnessScore > 80) return 'minimal';
        return 'normal';
    }

    // Additional helper methods would go here...
    protected function resolveTrailModel($trail)
    {
        if (is_object($trail) && method_exists($trail, 'toArray')) {
            return $trail;
        }
        
        $trailName = is_array($trail) ? ($trail['name'] ?? $trail['trail_name'] ?? null) : $trail;
        if ($trailName) {
            return Trail::where('trail_name', $trailName)->first();
        }
        
        return null;
    }
    
    protected function calculateBaseHikeTime($trail, $profile)
    {
        $estimatedTime = $trail['estimated_time'] ?? null;
        
        if ($estimatedTime && preg_match('/(\d+)(?:-(\d+))?\s*hours?/i', $estimatedTime, $matches)) {
            $minHours = intval($matches[1]);
            $maxHours = isset($matches[2]) ? intval($matches[2]) : $minHours;
            return intval(($minHours + $maxHours) / 2 * 60);
        }
        
        // Fallback calculation
        $distance = floatval($trail['distance_km'] ?? 10);
        $speed = 3; // Base 3 km/h
        return intval($distance / $speed * 60);
    }
    
    protected function adjustTimeForProfile($baseTime, $profile)
    {
        $multiplier = 1.0;
        
        // Fitness adjustment
        if ($profile['fitness_level'] < 4) $multiplier += 0.3;
        elseif ($profile['fitness_level'] > 8) $multiplier -= 0.15;
        
        // Pace preference
        if ($profile['pace_preference'] === 'slow') $multiplier += 0.2;
        elseif ($profile['pace_preference'] === 'fast') $multiplier -= 0.1;
        
        return intval($baseTime * $multiplier);
    }
    
    protected function createTrailStartActivity($time, $trail, $profile, $dayIndex = 0, $dateInfo = null)
    {
        $trailName = $trail['name'] ?? 'Trail';
        
        // Calculate cumulative distance for Day 2+ (same logic as ItineraryGeneratorService)
        $cumulativeDistance = 0.0;
        if ($dayIndex > 1 && $dateInfo) {
            $totalKm = floatval($trail['distance_km'] ?? 10);
            $durationDays = $dateInfo['duration_days'] ?? 2;
            
            // Calculate distance covered by previous days
            for ($prevDay = 1; $prevDay < $dayIndex; $prevDay++) {
                $prevDayDistance = $totalKm / max(1, $durationDays); // Simple equal split for now
                $cumulativeDistance += $prevDayDistance;
            }
        }
        
        if ($dayIndex === 1) {
            // Day 1: Start at trailhead
            $title = "Start {$trailName}";
            $location = $trailName;
            $description = $profile['experience_level'] === 'beginner' 
                ? 'Take your time and enjoy the journey ahead'
                : 'Begin your adventure with confidence';
        } else {
            // Day 2+: Continue from campsite
            $title = "Break Camp & Continue Hike";
            $location = "Campsite";
            $description = $profile['experience_level'] === 'beginner' 
                ? 'Pack up camp carefully and continue your journey'
                : 'Break camp efficiently and resume hiking';
        }
            
        return [
            'minutes' => $time,
            'cum_minutes' => $time,
            'cum_distance_km' => $cumulativeDistance,
            'title' => $title,
            'type' => 'hike',
            'location' => $location,
            'description' => $description
        ];
    }
    
    protected function createSafetyBriefingActivity($time, $profile)
    {
        $briefingLevel = $profile['experience_level'] === 'beginner' ? 'Comprehensive' : 'Quick';
        
        return [
            'minutes' => $time,
            'cum_minutes' => $time,
            'cum_distance_km' => 0.0,
            'title' => "{$briefingLevel} Safety Briefing",
            'type' => 'prep',
            'location' => 'Trailhead',
            'description' => 'Equipment check and safety guidelines review'
        ];
    }
    
    protected function createTrailEndActivity($time, $trail, $profile, $dayIndex, $dateInfo)
    {
        $isMultiDay = $dateInfo['duration_days'] > 1;
        $isLastDay = $dayIndex >= $dateInfo['duration_days'];
        
        if ($isMultiDay && !$isLastDay) {
            $title = 'Arrive at Camp';
            $type = 'camp';
            $location = 'Campsite';
            $description = 'Set up camp and prepare for the night';
        } else {
            $title = $profile['challenge_seeking'] === 'high' ? 'Summit Achievement!' : 'Trail Complete';
            $type = 'summit';
            $location = 'Summit/End Point';
            $description = 'Congratulations on completing the trail!';
        }
        
        return [
            'minutes' => $time,
            'cum_minutes' => $time,
            'cum_distance_km' => floatval($trail['distance_km'] ?? 10),
            'title' => $title,
            'type' => $type,
            'location' => $location,
            'description' => $description
        ];
    }
    protected function getTrailType($trail)
    {
        $name = strtolower($trail['name'] ?? '');
        
        if (str_contains($name, 'summit') || str_contains($name, 'peak')) {
            return 'summit';
        } elseif (str_contains($name, 'river') || str_contains($name, 'falls')) {
            return 'waterfall';
        } elseif (str_contains($name, 'forest') || str_contains($name, 'woods')) {
            return 'forest';
        }
        
        return 'mountain'; // default
    }
    
    protected function getScenicOpportunities($trail, $package)
    {
        $opportunities = ['viewpoints'];
        
        $trailType = $this->getTrailType($trail);
        
        switch($trailType) {
            case 'summit':
                $opportunities[] = 'summit';
                $opportunities[] = 'panoramic_views';
                break;
            case 'waterfall':
                $opportunities[] = 'waterfalls';
                $opportunities[] = 'river_views';
                break;
            case 'forest':
                $opportunities[] = 'canopy_views';
                $opportunities[] = 'wildlife_spotting';
                break;
        }
        
        return $opportunities;
    }
    
    protected function getTrailFeatures($trail)
    {
        $features = [];
        $name = strtolower($trail['name'] ?? '');
        
        if (str_contains($name, 'bridge')) $features[] = 'bridge_crossing';
        if (str_contains($name, 'cave')) $features[] = 'cave_exploration';
        if (str_contains($name, 'rock')) $features[] = 'rock_formations';
        if (str_contains($name, 'lake')) $features[] = 'lake_views';
        
        return $features;
    }
    
    protected function estimateGroupSize($package)
    {
        // Base group size on package type/name
        $packageName = strtolower($package['package_name'] ?? '');
        
        if (str_contains($packageName, 'solo') || str_contains($packageName, 'private')) {
            return 1;
        } elseif (str_contains($packageName, 'couple') || str_contains($packageName, 'duo')) {
            return 2;
        } elseif (str_contains($packageName, 'family')) {
            return 5;
        } elseif (str_contains($packageName, 'group')) {
            return 12;
        }
        
        return 8; // default group size
    }
    
    protected function getIncludedActivities($package)
    {
        $activities = [];
        $packageName = strtolower($package['package_name'] ?? '');
        
        if (str_contains($packageName, 'photo')) {
            $activities[] = 'photography_session';
        }
        if (str_contains($packageName, 'meal') || str_contains($packageName, 'lunch')) {
            $activities[] = 'guided_meal';
        }
        if (str_contains($packageName, 'guide')) {
            $activities[] = 'expert_guidance';
        }
        
        return $activities;
    }
    protected function determineBreakType($time, $totalTime, $profile)
    {
        $progress = $time / $totalTime;
        
        // Lunch break around middle
        if ($progress >= 0.4 && $progress <= 0.6) {
            return 'meal';
        }
        
        // Scenic breaks for photography enthusiasts
        if ($profile['photography_interest'] && ($progress === 0.25 || $progress === 0.75)) {
            return 'scenic';
        }
        
        return 'hydration';
    }
    
    protected function getBreakDuration($type, $profile)
    {
        return match($type) {
            'meal' => $profile['pace_preference'] === 'slow' ? 45 : 30,
            'scenic' => $profile['photography_interest'] ? 20 : 10,
            'hydration' => $profile['fitness_level'] < 5 ? 15 : 10,
            default => 10
        };
    }
    
    protected function calculateDistanceAtTime($time, $trail)
    {
        $totalDistance = floatval($trail['distance_km'] ?? 10);
        $totalTime = $this->calculateBaseHikeTime($trail, []);
        
        if ($totalTime <= 0) return 0.0;
        
        $progress = $time / $totalTime;
        return round($totalDistance * $progress, 2);
    }
    protected function getHydrationAdvice($profile)
    {
        if ($profile['fitness_level'] < 5) {
            return 'Take frequent hydration breaks - drink water every 15-20 minutes';
        } elseif ($profile['experience_level'] < 5) {
            return 'Monitor your hydration closely - aim for 500ml per hour of hiking';
        } else {
            return 'Maintain regular hydration based on your experience and conditions';
        }
    }
    
    protected function getMealAdvice($profile, $time)
    {
        $hour = floor($time / 60);
        
        if ($hour < 10) {
            return $profile['fitness_level'] < 5 
                ? 'Start with energy-rich breakfast - you\'ll need sustained fuel'
                : 'Light breakfast to energize your morning hike';
        } elseif ($hour >= 11 && $hour <= 13) {
            return $profile['pace_preference'] === 'slow'
                ? 'Take time for a substantial lunch break - enjoy the scenery'
                : 'Quick energy boost with trail mix and hydration';
        } else {
            return 'Refuel with nutritious snacks to maintain energy levels';
        }
    }
    protected function identifyPhotoOpportunities($hikeTime, $trail, $profile)
    {
        if (!$profile['photography_interest']) return [];
        
        $opportunities = [];
        $quarterTime = $hikeTime / 4;
        
        // Golden hour morning
        $opportunities[] = ['time' => $quarterTime, 'type' => 'golden_hour'];
        
        // Scenic viewpoint
        $opportunities[] = ['time' => $hikeTime * 0.6, 'type' => 'viewpoint'];
        
        return $opportunities;
    }
    
    protected function identifyNatureStops($hikeTime, $trail, $profile)
    {
        if (!$profile['nature_interest']) return [];
        
        return [
            ['time' => $hikeTime * 0.3, 'type' => 'flora_observation'],
            ['time' => $hikeTime * 0.7, 'type' => 'wildlife_spotting']
        ];
    }
    
    protected function identifyOptionalChallenges($trail, $profile)
    {
        if ($profile['risk_tolerance'] < 7) return [];
        
        return [
            ['type' => 'side_trail', 'difficulty' => 'moderate'],
            ['type' => 'scramble', 'difficulty' => 'challenging']
        ];
    }
    
    protected function createPhotoActivity($time, $opportunity, $profile)
    {
        $activities = [
            'golden_hour' => [
                'activity' => 'Golden hour photography',
                'description' => 'Capture the trail in beautiful morning light',
                'duration' => 20
            ],
            'viewpoint' => [
                'activity' => 'Landscape photography',
                'description' => 'Document scenic viewpoints and trail features',
                'duration' => 15
            ]
        ];
        
        $base = $activities[$opportunity['type']] ?? $activities['viewpoint'];
        
        return [
            'time' => $this->formatTimeFromMinutes($time),
            'activity' => $base['activity'],
            'description' => $base['description'],
            'duration' => $base['duration']
        ];
    }
    
    protected function createNatureActivity($time, $stop, $profile)
    {
        $activities = [
            'flora_observation' => [
                'activity' => 'Flora observation',
                'description' => 'Study local plant species and botanical features',
                'duration' => 15
            ],
            'wildlife_spotting' => [
                'activity' => 'Wildlife spotting',
                'description' => 'Observe and identify local wildlife',
                'duration' => 20
            ]
        ];
        
        $base = $activities[$stop['type']] ?? $activities['flora_observation'];
        
        return [
            'time' => $this->formatTimeFromMinutes($time),
            'activity' => $base['activity'],
            'description' => $base['description'],
            'duration' => $base['duration']
        ];
    }
    
    protected function createChallengeActivity($time, $challenge, $profile)
    {
        $activities = [
            'side_trail' => [
                'activity' => 'Optional side trail exploration',
                'description' => 'Explore challenging side trails for experienced hikers',
                'duration' => 30
            ],
            'scramble' => [
                'activity' => 'Rock scrambling challenge',
                'description' => 'Navigate rocky terrain with advanced techniques',
                'duration' => 25
            ]
        ];
        
        $base = $activities[$challenge['type']] ?? $activities['side_trail'];
        
        return [
            'time' => $this->formatTimeFromMinutes($time),
            'activity' => $base['activity'],
            'description' => $base['description'],
            'duration' => $base['duration']
        ];
    }
    
    protected function formatTimeFromMinutes($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
}