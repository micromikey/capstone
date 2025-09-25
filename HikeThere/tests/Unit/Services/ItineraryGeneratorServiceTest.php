<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ItineraryGeneratorService;
use App\Services\TrailCalculatorService;
use App\Services\WeatherHelperService;
use App\Services\DataNormalizerService;

class ItineraryGeneratorServiceTest extends TestCase
{
    protected $itineraryService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->itineraryService = new ItineraryGeneratorService(
            new TrailCalculatorService(),
            new WeatherHelperService(),
            new DataNormalizerService()
        );
    }

    public function test_can_generate_basic_itinerary()
    {
        $itinerary = [
            'duration_days' => 2,
            'nights' => 1,
            'start_time' => '06:00',
            'start_date' => '2025-10-01'
        ];

        $trail = [
            'name' => 'Test Trail',
            'distance_km' => 10,
            'elevation_m' => 500,
            'difficulty' => 'moderate'
        ];

        $build = [
            'transport_mode' => 'pickup',
            'vehicle' => 'Van'
        ];

        $result = $this->itineraryService->generateItinerary($itinerary, $trail, $build, []);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('itinerary', $result);
        $this->assertArrayHasKey('trail', $result);
        $this->assertArrayHasKey('build', $result);
        $this->assertArrayHasKey('dateInfo', $result);
        $this->assertArrayHasKey('dayActivities', $result);
        $this->assertArrayHasKey('nightActivities', $result);

        // Check date info
        $this->assertEquals(2, $result['dateInfo']['duration_days']);
        $this->assertEquals(1, $result['dateInfo']['nights']);

        // Check we have activities for both days
        $this->assertArrayHasKey(1, $result['dayActivities']);
        $this->assertArrayHasKey(2, $result['dayActivities']);

        // Check we have night activities
        $this->assertArrayHasKey(1, $result['nightActivities']);
    }

    public function test_can_generate_day_plan()
    {
        $trail = [
            'name' => 'Test Trail',
            'distance_km' => 8,
            'elevation_m' => 300,
            'difficulty' => 'easy'
        ];

        $dateInfo = [
            'duration_days' => 1,
            'start_time' => '07:00'
        ];

        $activities = $this->itineraryService->generateDayPlan(1, $trail, $dateInfo, []);

        $this->assertIsArray($activities);
        $this->assertNotEmpty($activities);

        // Check we have expected activity types
        $activityTitles = array_column($activities, 'title');
        $this->assertContains('Wake up & Breakfast', $activityTitles);
        $this->assertContains('Hike Start', $activityTitles);
    }

    public function test_can_generate_night_plan()
    {
        $activities = $this->itineraryService->generateNightPlan(1, 900); // 15:00 arrival

        $this->assertIsArray($activities);
        $this->assertNotEmpty($activities);

        // Check we have expected night activities
        $activityTitles = array_column($activities, 'title');
        $this->assertContains('Set up Camp / Check-in', $activityTitles);
        $this->assertContains('Sleep', $activityTitles);
    }
}