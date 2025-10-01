<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;

class TestAdvanceDayLogic extends Command
{
    protected $signature = 'test:advance-day';
    protected $description = 'Test the new advance departure day logic for long-distance trails';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Testing Advance Day Logic for Long-Distance Trails ===');

        $trailData = [
            'name' => 'Mt. Pulag via Ambangeg Trail',
            'location' => 'Benguet, Philippines',
            'latitude' => 16.5966,
            'longitude' => 120.9060,
            'transport_included' => 1,
            'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
            'departure_point' => 'Shaw Boulevard Manila'
        ];

        $buildData = [
            'user_lat' => 14.6417,
            'user_lng' => 120.4736,
            'user_location' => 'Bataan, Philippines',
            'start_date' => now()->addDays(7)->format('Y-m-d')
        ];

        $itineraryData = [
            (object) [
                'duration_days' => 2,
                'duration_nights' => 1
            ]
        ];

        try {
            $this->line("🗓️ Hike scheduled for: " . $buildData['start_date']);
            $this->line("⏰ Expected hike start time: 08:30 AM");
            $this->line("");

            $generatedData = $service->generateItinerary($itineraryData, $trailData, $buildData);
            
            if (isset($generatedData['preHikeActivities']) && !empty($generatedData['preHikeActivities'])) {
                $activities = $generatedData['preHikeActivities'];
                $totalHours = round(end($activities)['minutes'] / 60, 1);
                
                $this->info("✅ Generated " . count($activities) . " pre-hike activities");
                $this->line("📊 Total pre-hike duration: {$totalHours} hours");
                $this->line("");
                
                $this->line("📋 Pre-hike Schedule:");
                foreach ($activities as $index => $activity) {
                    $minutes = $activity['minutes'] ?? 0;
                    $timeDisplay = sprintf('%02d:%02d', intval($minutes / 60), $minutes % 60);
                    
                    // Check if this is an advance day activity
                    $isAdvanceDay = $minutes >= 1440; // 24 hours or more
                    $dayLabel = $isAdvanceDay ? ' [DAY BEFORE]' : ' [HIKE DAY]';
                    
                    $this->line(sprintf(
                        "%d. %s%s - %s",
                        $index + 1,
                        $timeDisplay,
                        $dayLabel,
                        $activity['title'] ?? 'Unknown Activity'
                    ));
                    
                    if (!empty($activity['description'])) {
                        $this->line("   📝 " . $activity['description']);
                    }
                    $this->line("");
                }
                
                // Check for critical timing
                $lastActivity = end($activities);
                $arrivalTime = $lastActivity['minutes'] ?? 0;
                $arrivalHour = intval($arrivalTime / 60);
                $arrivalMinutes = $arrivalTime % 60;
                
                $this->line("🎯 Analysis:");
                if ($arrivalTime >= 1440) {
                    // Multi-day schedule
                    $actualArrivalHour = $arrivalHour - 24;
                    $this->info("✅ Advance day departure detected!");
                    $this->info("✅ Arrival time: " . sprintf('%02d:%02d', $actualArrivalHour, $arrivalMinutes) . " (day before hike)");
                    $this->info("✅ Plenty of time to rest before 08:30 AM hike start!");
                } else {
                    // Same day schedule
                    if ($arrivalHour <= 8) {
                        $this->info("✅ Same-day arrival: " . sprintf('%02d:%02d', $arrivalHour, $arrivalMinutes));
                        $this->info("✅ On time for 08:30 AM hike start!");
                    } else {
                        $this->error("❌ Same-day arrival: " . sprintf('%02d:%02d', $arrivalHour, $arrivalMinutes));
                        $this->error("❌ LATE for 08:30 AM hike start!");
                    }
                }
                
            } else {
                $this->error("❌ No pre-hike activities generated");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Test failed: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }

        return 0;
    }
}