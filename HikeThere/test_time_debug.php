<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ItineraryGeneratorService;
use App\Services\WeatherHelperService;
use Carbon\Carbon;

echo "🔍 TIME CALCULATION DEBUG\n";
echo "========================\n\n";

$itineraryService = app(ItineraryGeneratorService::class);
$weatherHelper = app(WeatherHelperService::class);

echo "1. Night Activities Generated:\n";
echo "-----------------------------\n";
$nightActivities = $itineraryService->generateNightPlan(1, 1080);

foreach ($nightActivities as $activity) {
    $minutes = $activity['minutes'];
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    echo "Activity: {$activity['title']}\n";
    echo "  - Minutes value: {$minutes}\n";
    echo "  - Expected time: " . sprintf('%02d:%02d', $hours, $mins) . "\n\n";
}

echo "2. computeTimeForRow Debug:\n";
echo "--------------------------\n";
$baseDate = Carbon::parse('2025-09-28');
$startTime = '06:00';
$night = 1;

foreach ($nightActivities as $activity) {
    $minutes = $activity['minutes'];
    $computedTime = $weatherHelper->computeTimeForRow($baseDate, $startTime, $night, $minutes);
    
    echo "Activity: {$activity['title']}\n";
    echo "  - Input minutes: {$minutes}\n";
    echo "  - Computed time: {$computedTime}\n";
    echo "  - Base date: {$baseDate->toDateString()}\n";
    echo "  - Start time: {$startTime}\n";
    echo "  - Night: {$night}\n\n";
}

echo "3. Expected vs Actual:\n";
echo "---------------------\n";
echo "Expected: Night activities should show 18:00, 18:45, 19:45, 21:15\n";
echo "Problem: computeTimeForRow is adding minutes to base time instead of treating as absolute\n\n";

echo "✅ Debug complete!\n";