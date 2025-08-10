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
        return view('hiker.assessment.gear');
    }

    public function storeGear(Request $request)
    {
        Session::put('assessment.gear', $request->all());
        return redirect()->route('assessment.fitness');
    }

    // STEP 2: FITNESS
    public function fitness()
    {
        return view('hiker.assessment.fitness');
    }

    public function storeFitness(Request $request)
    {
        Session::put('assessment.fitness', $request->all());
        return redirect()->route('assessment.health');
    }

    // STEP 3: HEALTH
    public function health()
    {
        $showWarning = session('assessment.health_warning', false);
        return view('hiker.assessment.health', compact('showWarning'));
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
        return view('hiker.assessment.weather');
    }

    public function storeWeather(Request $request)
    {
        Session::put('assessment.weather', $request->all());
        return redirect()->route('assessment.emergency');
    }

    // STEP 5: EMERGENCY
    public function emergency()
    {
        return view('hiker.assessment.emergency');
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
        return view('hiker.assessment.environment');
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

    public function calculateResults(Request $request)
    {
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
            'environment' => $this->calculateEnvironmentScore($environmentData), // Added environment score
        ];

        $weights = [
            'gear' => 0.13,
            'fitness' => 0.20,
            'health' => 0.20,
            'weather' => 0.13,
            'emergency' => 0.17,
            'environment' => 0.17 // Added environment weight
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
        $essentialGear = [
            'hiking_boots', 'backpack', 'water_bottles', 'first_aid_kit',
            'navigation_tools', 'headlamp', 'rain_gear', 'extra_clothing'
        ];
        foreach ($essentialGear as $item) {
            $maxScore += 5;
            if (isset($data['gear_items']) && in_array($item, $data['gear_items'])) {
                $score += 5;
            }
        }

        $experienceQuestions = [
            'know_gear_maintenance' => 10,
            'comfortable_backpack_weight' => 15,
            'navigate_without_gps' => 15,
            'emergency_gear_knowledge' => 10,
            'weather_appropriate_gear' => 10
        ];
        foreach ($experienceQuestions as $q => $pts) {
            $maxScore += $pts;
            if (isset($data[$q])) {
                $score += $this->convertLikertToPoints($data[$q], $pts);
            }
        }
        return $maxScore ? round(($score / $maxScore) * 100) : 0;
    }

    private function calculateFitnessScore($data)
    {
        $score = 0;
        $maxScore = 0;
        $fitnessQuestions = [
            'experienced_hiking' => 15,
            'jog_1km' => 15,
            'stairs_no_rest' => 15,
            'carry_5kg' => 15,
            'joint_pain' => -10,
            'chronic_injuries' => -10,
            'hydration' => 10,
            'mental_readiness' => 10,
            'endurance_activity' => 10
        ];
        foreach ($fitnessQuestions as $q => $pts) {
            if (isset($data[$q])) {
                if ($pts > 0) {
                    $maxScore += $pts;
                    $score += $this->convertLikertToPoints($data[$q], $pts);
                } else {
                    $score += $this->convertLikertToPoints($data[$q], $pts, true);
                }
            }
        }
        if (isset($data['cardio_frequency'])) {
            $maxScore += 10;
            $cf = [
                'daily' => 10,
                '3_5_per_week' => 8,
                '1_2_per_week' => 5,
                'rarely_never' => 0
            ];
            $score += $cf[$data['cardio_frequency']] ?? 0;
        }
        return $maxScore ? max(0, round(($score / $maxScore) * 100)) : 0;
    }

    private function calculateHealthScore($data)
    {
        $score = 100;
        $penalties = [
            'asthma' => -15, 'chronic' => -20, 'blood_pressure' => -10,
            'epilepsy' => -25, 'heart_murmur' => -15, 'lung_disorder' => -20,
            'recent_surgery' => -15, 'migraine' => -5, 'mobility' => -20
        ];
        $conditions = $data['health_conditions'] ?? [];
        foreach ($conditions as $c) {
            if (isset($penalties[$c])) {
                $score += $penalties[$c];
            }
        }

        $questions = [
            'chest_pain_activity' => -15, 'breath_uphill' => -10,
            'dizziness_exercise' => -15, 'heart_palpitations' => -15,
            'wheezing_exercise' => -10, 'joint_pain_terrain' => -8,
            'balance_problems' => -12, 'heat_exhaustion_history' => -10,
            'medication_effects' => -8, 'altitude_sickness_history' => -10
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
        $questions = [
            'know_mountain_forecast' => 8, 'checked_forecast' => 6,
            'aware_mountain_weather_changes' => 8, 'have_rain_gear' => 10,
            'willing_turn_back' => 10, 'identify_storm_signs' => 8,
            'understand_layering' => 8, 'have_wind_protection' => 6,
            'aware_wind_chill' => 6, 'have_sun_protection' => 6,
            'recognize_heat_illness' => 8, 'know_lightning_safety' => 10,
            'recognize_hypothermia' => 8, 'have_emergency_shelter' => 8
        ];
        foreach ($questions as $q => $pts) {
            $max += $pts;
            if (isset($data[$q])) {
                $score += $this->convertLikertToPoints($data[$q], $pts);
            }
        }
        return $max ? round(($score / $max) * 100) : 0;
    }

    private function calculateEmergencyScore($data)
    {
        $score = 0;
        $max = 0;
        $questions = [
            'first_aid_training' => 15, 'treat_common_injuries' => 10,
            'emergency_communication' => 12, 'know_lost_protocol' => 10,
            'navigate_without_gps' => 8, 'recognize_environmental_illness' => 10,
            'know_cpr' => 12, 'build_emergency_shelter' => 8,
            'provide_location_info' => 8, 'informed_hiking_plans' => 7,
            'know_distress_signals' => 5, 'mentally_prepared_emergency' => 8,
            'assess_avoid_risks' => 7
        ];
        foreach ($questions as $q => $pts) {
            $max += $pts;
            if (isset($data[$q])) {
                $score += $this->convertLikertToPoints($data[$q], $pts);
            }
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
            if (isset($data["question_$i"])) {
                $val = [
                    'never' => 0,
                    'rarely' => 1,
                    'sometimes' => 2,
                    'often' => 2.5,
                    'very_often' => 3,
                ][$data["question_$i"]] ?? 1.5;
                $score += $val;
            }
            $max += 3;
        }

        return $max ? round(($score / $max) * 100) : 0;
    }

    private function convertLikertToPoints($response, $maxPoints, $isNegative = false)
    {
        $map = [
            'strongly_agree' => 1.0, 'agree' => 0.75, 'neutral' => 0.5,
            'disagree' => 0.25, 'strongly_disagree' => 0.0,
            'always' => 1.0, 'often' => 0.75, 'sometimes' => 0.5,
            'rarely' => 0.25, 'never' => 0.0, 'yes' => 1.0, 'no' => 0.0
        ];
        $val = $map[$response] ?? 0.5;
        return $isNegative ? $maxPoints * (1.0 - $val) : $maxPoints * $val;
    }

    private function getLikertFrequencyValue($response)
    {
        $map = [
            'always' => 1.0, 'often' => 0.75,
            'sometimes' => 0.5, 'rarely' => 0.25,
            'never' => 0.0
        ];
        return $map[$response] ?? 0.5;
    }

    private function determineReadinessLevel($overallScore)
    {
        if ($overallScore >= 85) {
            return [
                'level' => 'Excellent - Ready for Challenging Hikes',
                'color' => 'green',
                'icon' => '🏔️',
                'message' => 'You demonstrate excellent preparation and readiness for challenging hiking adventures.'
            ];
        } elseif ($overallScore >= 70) {
            return [
                'level' => 'Good - Ready for Moderate Hikes',
                'color' => 'blue',
                'icon' => '🥾',
                'message' => 'You are well-prepared for moderate hiking trails.'
            ];
        } elseif ($overallScore >= 55) {
            return [
                'level' => 'Fair - Suitable for Easy Trails',
                'color' => 'yellow',
                'icon' => '⚠️',
                'message' => 'You have basic hiking readiness but should improve before tackling harder trails.'
            ];
        } else {
            return [
                'level' => 'Needs Improvement - Consider Preparation',
                'color' => 'red',
                'icon' => '🚨',
                'message' => 'Significant preparation is needed before hiking. Address recommendations first.'
            ];
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
        return $recommendations;
    }
}