<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Update the itinerary with more realistic activities
$itinerary = App\Models\Itinerary::first();

$newActivities = [
    [
        'title' => 'Safety Briefing & Equipment Check',
        'location' => 'Trailhead',
        'description' => 'Final gear check and safety briefing',
        'minutes' => 0,
        'type' => 'prep',
        'cum_minutes' => 0,
        'cum_distance_km' => 0.0
    ],
    [
        'title' => 'Scenic Photo Stop',
        'location' => 'Viewpoint',  
        'description' => 'Great opportunity for photos and quick rest',
        'minutes' => 120,
        'type' => 'photo',
        'cum_minutes' => 120,
        'cum_distance_km' => 2.5
    ],
    [
        'title' => 'Lunch Break & Rest',
        'location' => 'Rest Area',
        'description' => 'Refuel and hydrate before continuing',
        'minutes' => 240,
        'type' => 'meal', 
        'cum_minutes' => 240,
        'cum_distance_km' => 5.0
    ],
    [
        'title' => 'Summit Achievement',
        'location' => 'Summit',
        'description' => 'Congratulations on the climb!',
        'minutes' => 360,
        'type' => 'summit',
        'cum_minutes' => 360,
        'cum_distance_km' => 8.5
    ]
];

// Update the itinerary
$dailySchedule = $itinerary->daily_schedule;
$dailySchedule[0]['activities'] = $newActivities;

$itinerary->daily_schedule = $dailySchedule;
$itinerary->save();

echo "✅ Updated itinerary with " . count($newActivities) . " activities\n";
echo "✅ Activities now have different times and locations\n";
echo "✅ This will test the dynamic weather system properly\n\n";

echo "Activities updated:\n";
foreach ($newActivities as $i => $activity) {
    $timeLabel = \Carbon\Carbon::parse('08:00')->addMinutes($activity['minutes'])->format('H:i');
    echo "  " . ($i+1) . ". {$activity['title']} at {$timeLabel} ({$activity['location']})\n";
}

echo "\n✅ Now test the web interface to see improved table formatting and dynamic weather!\n";