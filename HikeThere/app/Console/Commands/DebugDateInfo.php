<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ItineraryGeneratorService;
use Illuminate\Support\Facades\Log;

class DebugDateInfo extends Command
{
    protected $signature = 'debug:date-info';
    protected $description = 'Debug date info calculation';

    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        parent::__construct();
        $this->itineraryService = $itineraryService;
    }

    public function handle()
    {
        $this->info('🔍 DEBUG DATE INFO CALCULATION');
        $this->info('=============================');
        $this->newLine();

        // Create trail data with package duration
        $trail = [
            'id' => 1,
            'name' => 'Ambangeg Trail',
            'difficulty' => 'moderate',
            'elevation_gain' => 1200,
            'distance_km' => 8.5,
            'estimated_duration_hours' => 24, // This should be overridden
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

        $this->info('🔍 Input trail data:');
        $this->info(json_encode($trail, JSON_PRETTY_PRINT));
        $this->newLine();

        // Use reflection to access protected method
        $reflectionClass = new \ReflectionClass(get_class($this->itineraryService));
        $method = $reflectionClass->getMethod('calculateDateInfo');
        $method->setAccessible(true);

        try {
            // Call with empty itinerary to test trail parsing
            $itinerary = [];
            $dateInfo = $method->invoke($this->itineraryService, $itinerary, $trail, $routeData);

            $this->info('🔍 Resulting dateInfo:');
            $this->info(json_encode($dateInfo, JSON_PRETTY_PRINT));
            $this->newLine();

            $this->info('✅ Test complete!');

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error("Stack trace:");
            $this->error($e->getTraceAsString());
        }
    }
}