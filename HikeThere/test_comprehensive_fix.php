<?php

require_once __DIR__ . '/bootstrap/app.php';

use App\Services\ItineraryGeneratorService;
use Illuminate\Support\Facades\Log;

// Test the complete itinerary generation with all fixes
echo "🚀 COMPREHENSIVE ITINERARY TEST\n";
echo "==============================\n\n";

// Create trail data with package duration (like from real trail)
$trail = [
    'id' => 1,
    'name' => 'Ambangeg Trail',
    'difficulty' => 'moderate',
    'elevation_gain' => 1200,
    'distance_km' => 8.5,
    'estimated_duration_hours' => 24, // This should be overridden by package duration
    'package' => [
        'duration' => '36 hours', // 2 days, 1 night - this should take precedence
        'id' => 1,
        'name' => 'Weekend Adventure Package'
    ]
];

$routeData = [
    'distance_km' => 8.5,
    'estimated_duration_minutes' => 480
];

$userActivities = [];

try {
    $service = new ItineraryGeneratorService();
    $result = $service->generateItinerary($trail, $routeData, $userActivities);

    echo "📅 DURATION & DATES:\n";
    echo "Duration: {$result['duration_days']} days, {$result['duration_nights']} nights\n";
    echo "Start Date: {$result['start_date']}\n";
    echo "End Date: {$result['end_date']}\n\n";

    echo "🏔️ DAY ACTIVITIES:\n";
    foreach ($result['days'] as $dayIndex => $day) {
        echo "Day " . ($dayIndex + 1) . " ({$day['date']}):\n";
        foreach ($day['activities'] as $index => $activity) {
            $timeStr = sprintf('%02d:%02d', 
                intval($activity['minutes'] / 60), 
                $activity['minutes'] % 60
            );
            echo "  {$timeStr} - {$activity['title']} ({$activity['location']})\n";
            if ($index === 0) {
                echo "    ➡️ Starting activity: {$activity['title']}\n";
            }
            if ($index >= 2) break; // Show first 3 activities
        }
        echo "\n";
    }

    echo "🌙 NIGHT ACTIVITIES:\n";
    foreach ($result['nights'] as $nightIndex => $night) {
        echo "Night " . ($nightIndex + 1) . " ({$night['date']}):\n";
        foreach ($night['activities'] as $index => $activity) {
            $timeStr = sprintf('%02d:%02d', 
                intval($activity['minutes'] / 60), 
                $activity['minutes'] % 60
            );
            echo "  {$timeStr} - {$activity['title']} ({$activity['location']})\n";
            if ($index >= 2) break; // Show first 3 activities
        }
        echo "\n";
    }

    echo "✅ VALIDATION CHECKS:\n";
    
    // Check 1: Duration parsing from package
    $expectedDays = 2;
    $expectedNights = 1;
    $durationOK = ($result['duration_days'] === $expectedDays && $result['duration_nights'] === $expectedNights);
    echo "✓ Duration from package (36 hours = 2 days/1 night): " . ($durationOK ? "PASS" : "FAIL") . "\n";
    
    // Check 2: Day 1 starts at trailhead
    $day1FirstActivity = $result['days'][0]['activities'][0] ?? null;
    $day1OK = $day1FirstActivity && str_contains($day1FirstActivity['title'], 'Start') && str_contains($day1FirstActivity['title'], 'Trail');
    echo "✓ Day 1 starts at trailhead: " . ($day1OK ? "PASS" : "FAIL") . "\n";
    
    // Check 3: Day 2 starts at campsite  
    $day2FirstActivity = $result['days'][1]['activities'][0] ?? null;
    $day2OK = $day2FirstActivity && str_contains($day2FirstActivity['title'], 'Break Camp');
    echo "✓ Day 2 starts at campsite: " . ($day2OK ? "PASS" : "FAIL") . "\n";
    
    // Check 4: Night activities start at reasonable evening time (18:00 or later)
    $night1FirstActivity = $result['nights'][0]['activities'][0] ?? null;
    $nightTimeOK = $night1FirstActivity && $night1FirstActivity['minutes'] >= 18 * 60; // 18:00 = 1080 minutes
    echo "✓ Night activities start at evening time (18:00+): " . ($nightTimeOK ? "PASS" : "FAIL") . "\n";
    
    // Check 5: Timezone should be Philippines (Asia/Manila)
    $currentTz = config('app.timezone');
    $timezoneOK = ($currentTz === 'Asia/Manila');
    echo "✓ Philippines timezone (Asia/Manila): " . ($timezoneOK ? "PASS" : "FAIL") . "\n";
    
    echo "\n🎯 OVERALL RESULT: " . ($durationOK && $day1OK && $day2OK && $nightTimeOK && $timezoneOK ? "ALL TESTS PASSED! ✅" : "Some tests failed ❌") . "\n";

} catch (Exception $e) {
    echo "❌ Error generating itinerary: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n✨ Test complete!\n";