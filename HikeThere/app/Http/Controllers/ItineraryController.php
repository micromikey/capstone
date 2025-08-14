<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Models\AssessmentResult;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ItineraryController extends Controller
{
    public function build()
    {
        // Check if user has completed assessment
        $hasAssessment = Auth::user()->latestAssessmentResult()->exists();
        
        if (!$hasAssessment) {
            return redirect()->route('assessment.instruction')
                ->with('warning', 'Please complete the Pre-Hike Self-Assessment first to generate a personalized itinerary.');
        }

        // Get available trails for suggestions
        $trails = Trail::active()->get();
        
        // Get user's latest assessment for personalized recommendations
        $assessment = Auth::user()->latestAssessmentResult;
        
        return view('hiker.itinerary.build', compact('trails', 'assessment'));
    }

    public function generate(Request $request)
    {
        $user = Auth::user();
        $assessment = $user->latestAssessmentResult;

        if (!$assessment) {
            return redirect()->route('assessment.instruction')
                ->with('warning', 'Please complete the Pre-Hike Self-Assessment first.');
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'trail_name' => 'required|string|max:255',
            'duration' => 'required|string',
            'distance' => 'required|string',
            'time' => 'required|date_format:H:i',
            'date' => 'required|date|after:today',
            'transportation' => 'required|string',
            'trail' => 'nullable|string',
            'stopovers.*' => 'nullable|string|max:255',
            'sidetrips.*' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate itinerary based on assessment results and user preferences
        $itinerary = $this->generatePersonalizedItinerary($assessment, $request);

        // Save the itinerary
        $savedItinerary = Itinerary::create([
            'user_id' => $user->id,
            'title' => $itinerary['title'],
            'trail_name' => $itinerary['trail_name'],
            'difficulty_level' => $itinerary['difficulty_level'],
            'estimated_duration' => $itinerary['estimated_duration'],
            'distance' => $itinerary['distance'],
            'elevation_gain' => $itinerary['elevation_gain'],
            'best_time_to_hike' => $itinerary['best_time_to_hike'],
            'weather_conditions' => $itinerary['weather_conditions'],
            'gear_recommendations' => $itinerary['gear_recommendations'],
            'safety_tips' => $itinerary['safety_tips'],
            'route_description' => $itinerary['route_description'],
            'waypoints' => $itinerary['waypoints'],
            'emergency_contacts' => $itinerary['emergency_contacts'],
            'schedule' => $itinerary['schedule'],
            'stopovers' => $itinerary['stopovers'],
            'sidetrips' => $itinerary['sidetrips'],
            'transportation' => $itinerary['transportation'],
        ]);

        return redirect()->route('itinerary.show', $savedItinerary)
            ->with('success', 'Your personalized itinerary has been generated successfully!');
    }

    public function show(Itinerary $itinerary)
    {
        // Check if user owns this itinerary
        if ($itinerary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to itinerary.');
        }

        return view('hiker.itinerary.generated', compact('itinerary'));
    }

    public function pdf(Itinerary $itinerary)
    {
        // Check if user owns this itinerary
        if ($itinerary->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to itinerary.');
        }

        return view('hiker.itinerary.pdf', compact('itinerary'));
    }

    private function generatePersonalizedItinerary($assessment, $request)
    {
        // Get user preferences
        $user = Auth::user();
        $preferences = $user->preferences;

        // Determine difficulty level based on assessment scores
        $difficultyLevel = $this->determineDifficultyLevel($assessment);
        
        // Get trail preferences from request
        $trailName = $request->input('trail_name');
        $preferredDuration = $request->input('duration');
        $preferredDistance = $request->input('distance');
        $time = $request->input('time');
        $date = $request->input('date');
        $transportation = $request->input('transportation');
        $selectedTrail = $request->input('trail');
        $stopovers = $request->input('stopovers', []);
        $sidetrips = $request->input('sidetrips', []);

        // Generate gear recommendations based on assessment
        $gearRecommendations = $this->generateGearRecommendations($assessment);
        
        // Generate safety tips based on assessment
        $safetyTips = $this->generateSafetyTips($assessment);

        // Generate schedule
        $schedule = $this->generateSchedule($time, $date, $preferredDuration, $stopovers, $sidetrips);

        return [
            'title' => "Personalized {$trailName} Itinerary",
            'trail_name' => $trailName,
            'difficulty_level' => $difficultyLevel,
            'estimated_duration' => $preferredDuration,
            'distance' => $preferredDistance,
            'elevation_gain' => $this->getElevationForDifficulty($difficultyLevel),
            'best_time_to_hike' => $time,
            'weather_conditions' => $this->getWeatherConditions($date),
            'gear_recommendations' => $gearRecommendations,
            'safety_tips' => $safetyTips,
            'route_description' => $this->generateRouteDescription($difficultyLevel, $preferredDistance, $trailName),
            'waypoints' => $this->generateWaypoints($difficultyLevel, $preferredDistance, $stopovers, $sidetrips),
            'emergency_contacts' => $this->getEmergencyContacts($user),
            'schedule' => $schedule,
            'stopovers' => $stopovers,
            'sidetrips' => $sidetrips,
            'transportation' => $transportation,
        ];
    }

    private function determineDifficultyLevel($assessment)
    {
        $overallScore = $assessment->overall_score;
        
        if ($overallScore >= 80) {
            return 'Hard';
        } elseif ($overallScore >= 60) {
            return 'Moderate';
        } elseif ($overallScore >= 40) {
            return 'Easy';
        } else {
            return 'Easy';
        }
    }

    private function getElevationForDifficulty($difficulty)
    {
        return match($difficulty) {
            'Easy' => '100-300 meters',
            'Moderate' => '300-600 meters',
            'Hard' => '600-1000 meters',
            'Expert' => '1000+ meters',
            default => '300-500 meters'
        };
    }

    private function generateGearRecommendations($assessment)
    {
        $recommendations = ['Essential Items'];
        
        if ($assessment->gear_score < 70) {
            $recommendations[] = 'Navigation tools (compass, map)';
            $recommendations[] = 'First aid kit';
            $recommendations[] = 'Emergency shelter';
        }
        
        if ($assessment->weather_score < 70) {
            $recommendations[] = 'Weather-appropriate clothing';
            $recommendations[] = 'Rain gear';
            $recommendations[] = 'Extra layers';
        }
        
        if ($assessment->emergency_score < 70) {
            $recommendations[] = 'Emergency whistle';
            $recommendations[] = 'Signal mirror';
            $recommendations[] = 'Emergency blanket';
        }

        return $recommendations;
    }

    private function generateSafetyTips($assessment)
    {
        $tips = ['Always hike with a buddy or inform someone of your plans'];
        
        if ($assessment->health_score < 70) {
            $tips[] = 'Consult with your doctor before hiking';
            $tips[] = 'Carry necessary medications';
        }
        
        if ($assessment->fitness_score < 70) {
            $tips[] = 'Start with shorter trails and gradually increase difficulty';
            $tips[] = 'Take frequent breaks and stay hydrated';
        }
        
        if ($assessment->environment_score < 70) {
            $tips[] = 'Check trail conditions before departure';
            $tips[] = 'Be aware of wildlife in the area';
        }

        return $tips;
    }

    private function generateRouteDescription($difficulty, $distance, $trailName)
    {
        return "This {$difficulty} level trail at {$trailName} covers approximately {$distance} and offers a perfect balance of challenge and enjoyment. The route features scenic viewpoints, diverse terrain, and opportunities to observe local wildlife. The trail is well-marked and maintained, making it suitable for hikers of your current skill level.";
    }

    private function generateWaypoints($difficulty, $distance, $stopovers, $sidetrips)
    {
        $waypoints = [
            [
                'name' => 'Trailhead',
                'description' => 'Starting point with parking and information board',
                'distance' => '0 km',
                'elevation' => 'Starting elevation',
                'time' => 'Start time'
            ]
        ];

        // Add stopovers as waypoints
        foreach ($stopovers as $index => $stopover) {
            $waypoints[] = [
                'name' => $stopover,
                'description' => 'Rest stop and refreshment point',
                'distance' => $this->calculateDistance($index + 1, count($stopovers), $distance),
                'elevation' => 'Rest area elevation',
                'time' => 'Estimated arrival time'
            ];
        }

        // Add side trips as waypoints
        foreach ($sidetrips as $index => $sidetrip) {
            $waypoints[] = [
                'name' => $sidetrip,
                'description' => 'Optional side trip destination',
                'distance' => $this->calculateDistance($index + 1, count($sidetrips), $distance),
                'elevation' => 'Side trip elevation',
                'time' => 'Estimated arrival time'
            ];
        }

        $waypoints[] = [
            'name' => 'Summit/End Point',
            'description' => 'Final destination with panoramic views',
            'distance' => $distance,
            'elevation' => 'Final elevation',
            'time' => 'Estimated completion time'
        ];

        return $waypoints;
    }

    private function calculateDistance($index, $total, $totalDistance)
    {
        // Simple distance calculation - can be enhanced with actual trail data
        $percentage = $index / ($total + 1);
        $distance = round($percentage * $this->extractNumericDistance($totalDistance));
        return "~{$distance} km";
    }

    private function extractNumericDistance($distanceString)
    {
        // Extract numeric value from distance string (e.g., "5-8 km" -> 6.5)
        preg_match('/(\d+)-?(\d+)?/', $distanceString, $matches);
        if (isset($matches[2])) {
            return ($matches[1] + $matches[2]) / 2;
        }
        return (int)$matches[1];
    }

    private function generateSchedule($time, $date, $duration, $stopovers, $sidetrips)
    {
        $schedule = [
            'date' => $date,
            'start_time' => $time,
            'duration' => $duration,
            'stopovers' => $stopovers,
            'sidetrips' => $sidetrips,
            'estimated_completion' => $this->calculateCompletionTime($time, $duration)
        ];

        return $schedule;
    }

    private function calculateCompletionTime($startTime, $duration)
    {
        $start = \Carbon\Carbon::parse($startTime);
        $durationHours = $this->extractDurationHours($duration);
        return $start->addHours($durationHours)->format('H:i');
    }

    private function extractDurationHours($duration)
    {
        // Extract hours from duration string (e.g., "4-6 hours" -> 5)
        preg_match('/(\d+)-?(\d+)?/', $duration, $matches);
        if (isset($matches[2])) {
            return ($matches[1] + $matches[2]) / 2;
        }
        return (int)$matches[1];
    }

    private function getWeatherConditions($date)
    {
        // This could be enhanced with actual weather API integration
        $month = \Carbon\Carbon::parse($date)->month;
        
        if ($month >= 6 && $month <= 10) {
            return 'Rainy season - expect wet conditions. Bring rain gear and check weather forecasts.';
        } elseif ($month >= 11 && $month <= 2) {
            return 'Cool and dry season - ideal hiking conditions. Mornings may be chilly.';
        } else {
            return 'Transitional season - variable weather. Check local forecasts before departure.';
        }
    }

    private function getEmergencyContacts($user)
    {
        return [
            'local_emergency' => '911',
            'park_ranger' => '+63-XXX-XXX-XXXX',
            'emergency_contact' => $user->emergency_contact_phone ?? 'Not provided',
            'user_emergency_contact' => $user->emergency_contact_name ?? 'Not provided'
        ];
    }
}
