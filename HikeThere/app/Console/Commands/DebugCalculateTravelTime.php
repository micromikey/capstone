<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;
use Illuminate\Support\Facades\Log;

class DebugCalculateTravelTime extends Command
{
    protected $signature = 'debug:calculate-travel-time';
    protected $description = 'Debug the calculateTravelTime method step by step';

    public function handle(ItineraryGeneratorService $service)
    {
        $this->info('=== Debugging calculateTravelTime Method ===');

        // Shaw Boulevard Manila to Mount Pulag
        $manilaLat = 14.5906;
        $manilaLng = 121.0570;
        $pulagLat = 16.5966;
        $pulagLng = 120.9060;

        $this->line("From: Manila ({$manilaLat}, {$manilaLng})");
        $this->line("To: Mount Pulag ({$pulagLat}, {$pulagLng})");
        $this->line("Transport: van");
        $this->line("Route Context: manila_to_baguio_trail");
        $this->line("");

        try {
            $reflection = new \ReflectionClass($service);
            $calculateMethod = $reflection->getMethod('calculateTravelTime');
            $calculateMethod->setAccessible(true);
            
            // Enable Laravel logging to see what happens inside
            Log::info("=== STARTING DEBUG TRAVEL TIME CALCULATION ===");
            
            $result = $calculateMethod->invoke(
                $service,
                $manilaLat, $manilaLng,
                $pulagLat, $pulagLng,
                'van',
                'manila_to_baguio_trail'
            );
            
            $this->line("📊 Result: " . ($result ?: 'NULL/EMPTY'));
            
            if ($result) {
                $hours = round($result / 60, 1);
                $this->info("✅ Travel time: {$result} minutes ({$hours} hours)");
                
                if ($result < 30) {
                    $this->error("❌ This will trigger fallback (< 30 minutes)");
                } else {
                    $this->info("✅ This should use Google Maps result (>= 30 minutes)");
                }
            } else {
                $this->error("❌ Empty result - this will trigger fallback");
            }
            
            // Test the specific trail data format used in web
            $this->line("\n🔄 Testing with actual trail data format...");
            
            $trailData = [
                'name' => 'Mt. Pulag via Ambangeg Trail',
                'location' => 'Benguet, Philippines',  
                'latitude' => 16.5966,
                'longitude' => 120.9060,
                'transport_details' => 'Van transportation from Shaw Boulevard Manila to Ambangeg Trail',
                'departure_point' => 'Shaw Boulevard Manila'
            ];
            
            $buildData = [
                'user_lat' => 14.6417,
                'user_lng' => 120.4736,
                'user_location' => 'Bataan, Philippines',
                'start_date' => now()->addDays(7)->format('Y-m-d')
            ];
            
            // Call generatePreHikeActivities to see the full flow
            $preHikeMethod = $reflection->getMethod('generatePreHikeActivities');
            $preHikeMethod->setAccessible(true);
            
            Log::info("=== TESTING FULL PRE-HIKE FLOW ===");
            
            $activities = $preHikeMethod->invoke($service, $trailData, $buildData);
            
            if (!empty($activities)) {
                $lastActivity = end($activities);
                $totalHours = round($lastActivity['minutes'] / 60, 1);
                $this->info("✅ Full flow result: {$totalHours} hours");
                
                // Look for the travel activity description
                foreach ($activities as $activity) {
                    if (stripos($activity['title'], 'Travel as Group') !== false) {
                        $this->line("🎯 Travel activity: " . $activity['description']);
                        break;
                    }
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Exception: " . $e->getMessage());
            $this->line("Stack trace: " . $e->getTraceAsString());
        }

        return 0;
    }
}