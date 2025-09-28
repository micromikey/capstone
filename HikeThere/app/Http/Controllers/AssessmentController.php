<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AssessmentController extends Controller
{
    public function instruction()
    {
        return view('hiker.assessment.instruction');
    }
    
    // STEP 1: GEAR
    public function gear()
    {
        // Load any previously saved gear data from session
        $gearData = session('assessment.gear', []);
        return view('hiker.assessment.gear', compact('gearData'));
    }

    public function storeGear(Request $request)
    {
        // Save gear data to session
        Session::put('assessment.gear', $request->all());
        
        // Redirect to next step
        return redirect()->route('assessment.fitness');
    }

    // STEP 2: FITNESS
    public function fitness()
    {
        // Load any previously saved fitness data from session
        $fitnessData = session('assessment.fitness', []);
        
        // Debug: Log what data is being loaded
        \Log::info('Fitness data loaded from session:', $fitnessData);
        
        return view('hiker.assessment.fitness', compact('fitnessData'));
    }

    public function storeFitness(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Fitness assessment data received:', $request->all());
        
        // Debug: Check specific fields
        $data = $request->all();
        \Log::info('experienced_hiking: ' . ($data['experienced_hiking'] ?? 'NOT SET'));
        \Log::info('jog_1km: ' . ($data['jog_1km'] ?? 'NOT SET'));
        \Log::info('stairs_no_rest: ' . ($data['stairs_no_rest'] ?? 'NOT SET'));
        \Log::info('carry_5kg: ' . ($data['carry_5kg'] ?? 'NOT SET'));
        \Log::info('joint_pain: ' . ($data['joint_pain'] ?? 'NOT SET'));
        \Log::info('chronic_injuries: ' . ($data['chronic_injuries'] ?? 'NOT SET'));
        \Log::info('high_altitude: ' . ($data['high_altitude'] ?? 'NOT SET'));
        \Log::info('endurance_activity: ' . ($data['endurance_activity'] ?? 'NOT SET'));
        
        // Debug: Check if radio buttons are being submitted
        \Log::info('All form fields:', array_keys($data));
        \Log::info('Radio button values:');
        foreach ($data as $key => $value) {
            if (in_array($key, ['jog_1km', 'stairs_no_rest', 'carry_5kg', 'joint_pain', 'chronic_injuries'])) {
                \Log::info("$key: $value");
            }
        }
        
        // Save fitness data to session
        Session::put('assessment.fitness', $data);
        
        // Debug: Log what was saved to session
        \Log::info('Fitness data saved to session:', session('assessment.fitness'));
        
        // Redirect to next step
        return redirect()->route('assessment.health');
    }

    // STEP 3: HEALTH
    public function health()
    {
        $showWarning = session('assessment.health_warning', false);
        // Load any previously saved health data from session
        $healthData = session('assessment.health', []);
        return view('hiker.assessment.health', compact('showWarning', 'healthData'));
    }

    public function storeHealth(Request $request)
    {
        $data = $request->all();
        Session::put('assessment.health', $data);

        // Check if risky conditions were selected
        $selectedConditions = $data['health_conditions'] ?? [];
        $riskyConditions = [
            'cardiovascular', 'asthma', 'neurological',
            'severe_allergy', 'recent_surgery', 'medication_dependency'
        ];

        $hasRisk = count(array_intersect($selectedConditions, $riskyConditions)) > 0;
        session(['assessment.health_warning' => $hasRisk]);

        return redirect()->route('assessment.weather');
    }

    // STEP 4: WEATHER
    public function weather()
    {
        // Load any previously saved weather data from session
        $weatherData = session('assessment.weather', []);
        return view('hiker.assessment.weather', compact('weatherData'));
    }

    public function storeWeather(Request $request)
    {
        Session::put('assessment.weather', $request->all());
        return redirect()->route('assessment.emergency');
    }

    // STEP 5: EMERGENCY
    public function emergency()
    {
        // Load any previously saved emergency data from session
        $emergencyData = session('assessment.emergency', []);
        return view('hiker.assessment.emergency', compact('emergencyData'));
    }

    public function storeEmergency(Request $request)
    {
        Session::put('assessment.emergency', $request->all());
        // Redirect to environment assessment instead of results
        return redirect()->route('assessment.environment');
    }

    // STEP 6: ENVIRONMENT
    public function environment()
    {
        // Load any previously saved environment data from session
        $environmentData = session('assessment.environment', []);
        return view('hiker.assessment.environment', compact('environmentData'));
    }

    public function storeEnvironment(Request $request)
    {
        Session::put('assessment.environment', $request->all());
        // Now redirect to results after environment assessment
        return redirect()->route('assessment.result');
    }

    // FINAL RESULT
    public function result()
    {
        return $this->calculateResults(new Request());
    }

    // View saved assessment results
    public function viewSavedResults()
    {
        $user = auth()->user();
        $latestResult = $user->latestAssessmentResult;
        
        if (!$latestResult) {
            return redirect()->route('assessment.instruction')
                ->with('error', 'No assessment results found. Please complete an assessment first.');
        }
        
        // Clear any existing session data to ensure we show database results
        Session::forget([
            'assessment.gear',
            'assessment.fitness',
            'assessment.health',
            'assessment.weather',
            'assessment.emergency',
            'assessment.environment',
            'assessment.health_warning'
        ]);
        
        $scores = [
            'gear' => $latestResult->gear_score,
            'fitness' => $latestResult->fitness_score,
            'health' => $latestResult->health_score,
            'weather' => $latestResult->weather_score,
            'emergency' => $latestResult->emergency_score,
            'environment' => $latestResult->environment_score,
            'overall' => $latestResult->overall_score,
        ];
        
        $readinessLevel = [
            'level' => $latestResult->readiness_level,
            'color' => $latestResult->readiness_level_color,
            'icon' => $latestResult->readiness_level_icon,
            'message' => $this->getReadinessLevelMessage($latestResult->overall_score),
        ];
        
        $recommendations = $latestResult->recommendations ?? $this->generateRecommendations($scores, []);
        
        return view('hiker.assessment.results', compact('scores', 'readinessLevel', 'recommendations'));
    }

    // Save assessment results to database
    public function saveResults(Request $request)
    {
        $user = auth()->user();
        
        // Get all assessment data from session
        $gearData = Session::get('assessment.gear', []);
        $fitnessData = Session::get('assessment.fitness', []);
        $healthData = Session::get('assessment.health', []);
        $weatherData = Session::get('assessment.weather', []);
        $emergencyData = Session::get('assessment.emergency', []);
        $environmentData = Session::get('assessment.environment', []);

        // Calculate scores
        $scores = [
            'gear' => $this->calculateGearScore($gearData),
            'fitness' => $this->calculateFitnessScore($fitnessData),
            'health' => $this->calculateHealthScore($healthData),
            'weather' => $this->calculateWeatherScore($weatherData),
            'emergency' => $this->calculateEmergencyScore($emergencyData),
            'environment' => $this->calculateEnvironmentScore($environmentData),
        ];

        $weights = [
            'gear' => 0.13,
            'fitness' => 0.20,
            'health' => 0.20,
            'weather' => 0.13,
            'emergency' => 0.17,
            'environment' => 0.17
        ];

        $overallScore = round(
            ($scores['gear'] * $weights['gear']) +
            ($scores['fitness'] * $weights['fitness']) +
            ($scores['health'] * $weights['health']) +
            ($scores['weather'] * $weights['weather']) +
            ($scores['emergency'] * $weights['emergency']) +
            ($scores['environment'] * $weights['environment'])
        );

        $readinessLevel = $this->determineReadinessLevel($overallScore);
        $recommendations = $this->generateRecommendations($scores, [
            'gear' => $gearData,
            'fitness' => $fitnessData,
            'health' => $healthData,
            'weather' => $weatherData,
            'emergency' => $emergencyData,
            'environment' => $environmentData
        ]);

        // Save to database
        $assessmentResult = \App\Models\AssessmentResult::create([
            'user_id' => $user->id,
            'overall_score' => $overallScore,
            'gear_score' => $scores['gear'],
            'fitness_score' => $scores['fitness'],
            'health_score' => $scores['health'],
            'weather_score' => $scores['weather'],
            'emergency_score' => $scores['emergency'],
            'environment_score' => $scores['environment'],
            'readiness_level' => $readinessLevel['level'],
            'recommendations' => $recommendations,
            'completed_at' => now(),
        ]);

        // Clear session data
        Session::forget([
            'assessment.gear',
            'assessment.fitness',
            'assessment.health',
            'assessment.weather',
            'assessment.emergency',
            'assessment.environment',
            'assessment.health_warning'
        ]);

        return redirect()->route('custom.profile.show')
            ->with('success', 'Assessment results saved successfully to your profile!');
    }

    public function calculateResults(Request $request)
    {
        $user = auth()->user();
        
        // Check if user has saved assessment results in database
        $latestResult = $user->latestAssessmentResult;
        
        if ($latestResult) {
            // Use saved database results instead of recalculating from session
            $scores = [
                'gear' => $latestResult->gear_score,
                'fitness' => $latestResult->fitness_score,
                'health' => $latestResult->health_score,
                'weather' => $latestResult->weather_score,
                'emergency' => $latestResult->emergency_score,
                'environment' => $latestResult->environment_score,
                'overall' => $latestResult->overall_score,
            ];
            
            $readinessLevel = [
                'level' => $latestResult->readiness_level,
                'color' => $latestResult->readiness_level_color,
                'icon' => $latestResult->readiness_level_icon,
                'message' => $this->getReadinessLevelMessage($latestResult->overall_score),
            ];
            
            // Get recommendations from database or regenerate them
            $recommendations = $latestResult->recommendations ?? $this->generateRecommendations($scores, []);
            
            return view('hiker.assessment.results', compact('scores', 'readinessLevel', 'recommendations'));
        }
        
        // Fallback to session-based calculation if no database results exist
        $gearData = Session::get('assessment.gear', []);
        $fitnessData = Session::get('assessment.fitness', []);
        $healthData = Session::get('assessment.health', []);
        $weatherData = Session::get('assessment.weather', []);
        $emergencyData = Session::get('assessment.emergency', []);
        $environmentData = Session::get('assessment.environment', []);

        $scores = [
            'gear' => $this->calculateGearScore($gearData),
            'fitness' => $this->calculateFitnessScore($fitnessData),
            'health' => $this->calculateHealthScore($healthData),
            'weather' => $this->calculateWeatherScore($weatherData),
            'emergency' => $this->calculateEmergencyScore($emergencyData),
            'environment' => $this->calculateEnvironmentScore($environmentData),
        ];

        $weights = [
            'gear' => 0.13,
            'fitness' => 0.20,
            'health' => 0.20,
            'weather' => 0.13,
            'emergency' => 0.17,
            'environment' => 0.17
        ];

        $scores['overall'] = round(
            ($scores['gear'] * $weights['gear']) +
            ($scores['fitness'] * $weights['fitness']) +
            ($scores['health'] * $weights['health']) +
            ($scores['weather'] * $weights['weather']) +
            ($scores['emergency'] * $weights['emergency']) +
            ($scores['environment'] * $weights['environment'])
        );

        $readinessLevel = $this->determineReadinessLevel($scores['overall']);
        $recommendations = $this->generateRecommendations($scores, [
            'gear' => $gearData,
            'fitness' => $fitnessData,
            'health' => $healthData,
            'weather' => $weatherData,
            'emergency' => $emergencyData,
            'environment' => $environmentData
        ]);

        return view('hiker.assessment.results', compact('scores', 'readinessLevel', 'recommendations'));
    }



    // Score Calculation Methods
    private function calculateGearScore($data)
    {
        $score = 0;
        $maxScore = 0;
        
        // Essential gear items (individual checkboxes) - using exact form field names
        $essentialGear = [
            'backpack', 'water', 'food', 'first_aid_kit',
            'headlight_flashlamp', 'whistle', 'extra_clothes', 'rain_gear',
            'sun_protection', 'map_compass', 'powerbank_batteries'
        ];
        
        foreach ($essentialGear as $item) {
            $maxScore += 5;
            if (isset($data[$item]) && $data[$item] == 'on') {
                $score += 5;
            }
        }

        // No experience questions in current form, so max score is just essential gear
        return $maxScore ? round(($score / $maxScore) * 100) : 0;
    }

    private function calculateFitnessScore($data)
    {
        $score = 0;
        $maxScore = 0;
        
        // Fitness questions with positive scoring
        $fitnessQuestions = [
            'experienced_hiking' => 15,
            'jog_1km' => 15,
            'stairs_no_rest' => 15,
            'carry_5kg' => 15,
            'endurance_activity' => 10,
            'high_altitude' => 10
        ];
        
        // Treat missing answers as neutral so unanswered questions don't implicitly penalize the user.
        foreach ($fitnessQuestions as $q => $pts) {
            $maxScore += $pts;
            $response = $data[$q] ?? 'neutral';
            $score += $this->convertLikertToPoints($response, $pts);
        }

        // Negative factors with reduced penalties: default to 'never' (no problem) when missing
        $maxScore += 10;
        $response = $data['joint_pain'] ?? 'never';
        $score += $this->convertLikertToPoints($response, 10, true);

        $maxScore += 10;
        $response = $data['chronic_injuries'] ?? 'never';
        $score += $this->convertLikertToPoints($response, 10, true);
        
        return $maxScore ? max(0, round(($score / $maxScore) * 100)) : 0;
    }

    private function calculateHealthScore($data)
    {
        $score = 100;
        
        // Health conditions with moderate penalties
        $penalties = [
            'asthma' => -10, 'chronic' => -15, 'blood_pressure' => -8,
            'epilepsy' => -20, 'heart_murmur' => -12, 'lung_disorder' => -15,
            'recent_surgery' => -12, 'migraine' => -3, 'mobility' => -15
        ];
        
        $conditions = $data['health_conditions'] ?? [];
        foreach ($conditions as $c) {
            if (isset($penalties[$c])) {
                $score += $penalties[$c];
            }
        }

        // Activity-related questions with moderate penalties
        $questions = [
            'chest_pain_activity' => -12, 'breath_uphill' => -8,
            'dizziness_exercise' => -12, 'heart_palpitations' => -12,
            'wheezing_exercise' => -8, 'joint_pain_terrain' => -6,
            'balance_problems' => -10, 'heat_exhaustion_history' => -8,
            'medication_effects' => -6, 'altitude_sickness_history' => -8
        ];
        
        foreach ($questions as $q => $p) {
            if (isset($data[$q])) {
                $score += ($p * $this->getLikertFrequencyValue($data[$q]));
            }
        }
        
        return max(0, min(100, round($score)));
    }

    private function calculateWeatherScore($data)
    {
        $score = 0;
        $max = 0;
        
        // Weather awareness questions
        $questions = [
            'know_mountain_forecast' => 8, 'checked_forecast' => 8,
            'aware_mountain_weather_changes' => 8, 'have_rain_gear' => 10,
            'willing_turn_back' => 10, 'identify_storm_signs' => 8,
            'understand_layering' => 8, 'have_wind_protection' => 8,
            'aware_wind_chill' => 8, 'have_sun_protection' => 8,
            'recognize_heat_illness' => 8, 'know_lightning_safety' => 10,
            'recognize_hypothermia' => 8, 'have_emergency_shelter' => 8
        ];
        
        // Missing answers treated as neutral
        foreach ($questions as $q => $pts) {
            $max += $pts;
            $response = $data[$q] ?? 'neutral';
            $score += $this->convertLikertToPoints($response, $pts);
        }
        
        return $max ? round(($score / $max) * 100) : 0;
    }

    private function calculateEmergencyScore($data)
    {
        $score = 0;
        $max = 0;
        
        // Emergency preparedness questions
        $questions = [
            'first_aid_training' => 15, 'treat_common_injuries' => 12,
            'emergency_communication' => 12, 'know_lost_protocol' => 10,
            'navigate_without_gps' => 10, 'recognize_environmental_illness' => 10,
            'know_cpr' => 12, 'build_emergency_shelter' => 8,
            'provide_location_info' => 8, 'informed_hiking_plans' => 8,
            'know_distress_signals' => 8, 'mentally_prepared_emergency' => 8,
            'assess_avoid_risks' => 8
        ];
        
        // Missing answers treated as neutral
        foreach ($questions as $q => $pts) {
            $max += $pts;
            $response = $data[$q] ?? 'neutral';
            $score += $this->convertLikertToPoints($response, $pts);
        }
        
        return $max ? round(($score / $max) * 100) : 0;
    }

    // NEW: Environment scoring
    private function calculateEnvironmentScore($data)
    {
        $score = 0;
        $max = 0;

        // Score for correct principles (7 correct out of 10 options)
        $correctPrinciples = [
            'Plan ahead and prepare',
            'Travel and camp on durable surfaces',
            'Dispose of waste properly',
            'Leave what you find',
            'Minimize campfire impacts',
            'Respect wildlife',
            'Be considerate of other visitors',
        ];
        
    $selected = $data['principles'] ?? [];
    $score += count(array_intersect($selected, $correctPrinciples)) * 10;
    $max += 70;

        // Score for Likert questions (10 questions, each max 3 points)
        for ($i = 0; $i < 10; $i++) {
            $response = $data["question_$i"] ?? 'sometimes';
            $val = [
                'never' => 0,
                'rarely' => 1,
                'sometimes' => 2,
                'often' => 2.5,
                'very_often' => 3,
            ][$response] ?? 2;
            $score += $val;
            $max += 3;
        }

        return $max ? round(($score / $max) * 100) : 0;
    }

    private function convertLikertToPoints($response, $maxPoints, $isNegative = false)
    {
        $map = [
            'strongly_agree' => 1.0, 'agree' => 0.85, 'neutral' => 0.6,
            'disagree' => 0.3, 'strongly_disagree' => 0.1,
            'always' => 1.0, 'often' => 0.85, 'sometimes' => 0.6,
            'rarely' => 0.3, 'never' => 0.1, 'yes' => 1.0, 'no' => 0.0,
            '5' => 1.0, '4' => 0.85, '3' => 0.6, '2' => 0.3, '1' => 0.1
        ];
        
        $val = $map[$response] ?? 0.6;
        return $isNegative ? $maxPoints * (1.0 - $val) : $maxPoints * $val;
    }

    private function getLikertFrequencyValue($response)
    {
        $map = [
            'always' => 1.0, 'often' => 0.8,
            'sometimes' => 0.6, 'rarely' => 0.3,
            'never' => 0.1
        ];
        
        return $map[$response] ?? 0.6;
    }

    private function determineReadinessLevel($overallScore)
    {
        if ($overallScore >= 85) {
            return [
                'level' => 'Excellent - Ready for Challenging Hikes',
                'color' => 'green',
                'icon' => '🏔️',
                'message' => 'You demonstrate excellent preparation and readiness for challenging hiking adventures. Your comprehensive preparation shows you understand the demands of hiking and have taken appropriate measures to ensure safety and enjoyment.'
            ];
        } elseif ($overallScore >= 75) {
            return [
                'level' => 'Good - Ready for Moderate Hikes',
                'color' => 'blue',
                'icon' => '🥾',
                'message' => 'You are well-prepared for moderate hiking trails. Your preparation demonstrates good awareness of hiking requirements and safety considerations.'
            ];
        } elseif ($overallScore >= 60) {
            return [
                'level' => 'Fair - Suitable for Easy Trails',
                'color' => 'yellow',
                'icon' => '⚠️',
                'message' => 'You have basic hiking readiness but should address the recommendations before tackling harder trails. Focus on improving areas with lower scores.'
            ];
        } else {
            return [
                'level' => 'Needs Improvement - Consider Preparation',
                'color' => 'red',
                'icon' => '🚨',
                'message' => 'Significant preparation is needed before hiking. Please address the critical recommendations first and consider starting with very easy trails once improvements are made.'
            ];
        }
    }

    private function getReadinessLevelMessage($overallScore)
    {
        if ($overallScore >= 85) {
            return 'You demonstrate excellent preparation and readiness for challenging hiking adventures. Your comprehensive preparation shows you understand the demands of hiking and have taken appropriate measures to ensure safety and enjoyment.';
        } elseif ($overallScore >= 75) {
            return 'You are well-prepared for moderate hiking trails. Your preparation demonstrates good awareness of hiking requirements and safety considerations.';
        } elseif ($overallScore >= 60) {
            return 'You have basic hiking readiness but should address the recommendations before tackling harder trails. Focus on improving areas with lower scores.';
        } else {
            return 'Significant preparation is needed before hiking. Please address the critical recommendations first and consider starting with very easy trails once improvements are made.';
        }
    }

    private function generateRecommendations($scores, $allData)
    {
        $recommendations = [];
        if ($scores['gear'] < 50) {
            $recommendations[] = [
                'category' => 'Gear & Equipment',
                'priority' => 'critical',
                'message' => 'Essential hiking gear is missing or inadequate.'
            ];
        }
        if ($scores['health'] < 50) {
            $recommendations[] = [
                'category' => 'Health Assessment',
                'priority' => 'critical',
                'message' => 'Health concerns may pose significant risks during hiking.'
            ];
        }
        if ($scores['emergency'] < 50) {
            $recommendations[] = [
                'category' => 'Emergency Preparedness',
                'priority' => 'critical',
                'message' => 'Emergency skills and knowledge are insufficient.'
            ];
        }
        if ($scores['environment'] < 50) {
            $recommendations[] = [
                'category' => 'Environmental Care',
                'priority' => 'critical',
                'message' => 'Your environmental practices need improvement. Review Leave No Trace principles and responsible hiking habits.'
            ];
        }
        if ($scores['fitness'] >= 50 && $scores['fitness'] < 70) {
            $recommendations[] = [
                'category' => 'Physical Fitness',
                'priority' => 'high',
                'message' => 'Improve cardiovascular fitness and hiking-specific conditioning.'
            ];
        }
        if ($scores['weather'] >= 50 && $scores['weather'] < 70) {
            $recommendations[] = [
                'category' => 'Weather Awareness',
                'priority' => 'high',
                'message' => 'Enhance weather forecasting skills and prepare for weather hazards.'
            ];
        }
        if ($scores['weather'] < 70) {
            $recommendations[] = [
                'category' => 'Weather',
                'priority' => $scores['weather'] < 55 ? 'critical' : 'high',
                'message' => 'Review weather forecasts and be prepared for sudden changes. Consider rescheduling if severe weather is expected.'
            ];
        }
        foreach ($scores as $category => $score) {
            if ($score >= 70 && $score < 85 && $category !== 'overall') {
                $recommendations[] = [
                    'category' => ucfirst($category),
                    'priority' => 'medium',
                    'message' => "Good foundation in {$category}, but room for improvement."
                ];
            }
        }

        // Deduplicate recommendations by category+message and sort by priority
        $seen = [];
        $deduped = [];
        foreach ($recommendations as $rec) {
            $key = strtolower($rec['category'] . '|' . $rec['message']);
            if (isset($seen[$key])) {
                // upgrade priority if necessary (critical > high > medium > low)
                $existingIndex = $seen[$key];
                $existing = $deduped[$existingIndex];
                $priorityOrder = ['low' => 0, 'medium' => 1, 'high' => 2, 'critical' => 3];
                if ($priorityOrder[$rec['priority']] > $priorityOrder[$existing['priority']]) {
                    $deduped[$existingIndex]['priority'] = $rec['priority'];
                }
            } else {
                $seen[$key] = count($deduped);
                $deduped[] = $rec;
            }
        }

        usort($deduped, function ($a, $b) {
            $priorityOrder = ['critical' => 3, 'high' => 2, 'medium' => 1, 'low' => 0];
            $pa = $priorityOrder[$a['priority']] ?? 0;
            $pb = $priorityOrder[$b['priority']] ?? 0;
            if ($pa === $pb) {
                return strcmp($a['category'], $b['category']);
            }
            return $pb <=> $pa;
        });

        return $deduped;
    }
}