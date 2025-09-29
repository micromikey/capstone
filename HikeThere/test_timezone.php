<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Carbon\Carbon;

echo "🕐 TIMEZONE VERIFICATION TEST\n";
echo "============================\n\n";

echo "1. Application Timezone Settings:\n";
echo "--------------------------------\n";
echo "Laravel config timezone: " . config('app.timezone') . "\n";
echo "PHP default timezone: " . date_default_timezone_get() . "\n";
echo "Carbon default timezone: " . Carbon::now()->timezoneName . "\n\n";

echo "2. Current Time Comparison:\n";
echo "--------------------------\n";
$now = Carbon::now();
$utcNow = Carbon::now('UTC');
$manilaTime = Carbon::now('Asia/Manila');

echo "Current time (app default): " . $now->format('Y-m-d H:i:s T') . "\n";
echo "UTC time: " . $utcNow->format('Y-m-d H:i:s T') . "\n";
echo "Manila time: " . $manilaTime->format('Y-m-d H:i:s T') . "\n";
echo "Offset from UTC: " . $manilaTime->format('P') . "\n\n";

echo "3. Timezone Validation:\n";
echo "----------------------\n";
if ($now->timezoneName === 'Asia/Manila') {
    echo "✅ Application is correctly using Philippines timezone (Asia/Manila)\n";
} else {
    echo "❌ Application timezone issue - Expected: Asia/Manila, Got: " . $now->timezoneName . "\n";
}

echo "4. Sample Itinerary Times (Philippines Time):\n";
echo "---------------------------------------------\n";
$startDate = Carbon::parse('2025-09-28 06:00:00');
echo "Hike start: " . $startDate->format('Y-m-d H:i:s T (l)') . "\n";

$eveningTime = Carbon::parse('2025-09-28 18:00:00');
echo "Evening camp setup: " . $eveningTime->format('Y-m-d H:i:s T (l)') . "\n";

$stargazing = Carbon::parse('2025-09-28 19:45:00');
echo "Stargazing time: " . $stargazing->format('Y-m-d H:i:s T (l)') . "\n";

echo "\n✅ Timezone verification complete!\n";