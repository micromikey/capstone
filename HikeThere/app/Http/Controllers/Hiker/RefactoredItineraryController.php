<?php

namespace App\Http\Controllers\Hiker;

use App\Http\Controllers\Controller;
use App\Services\ItineraryGeneratorService;
use Illuminate\Http\Request;
use App\Models\Trail;

class RefactoredItineraryController extends Controller
{
    protected $itineraryService;

    public function __construct(ItineraryGeneratorService $itineraryService)
    {
        $this->itineraryService = $itineraryService;
    }

    /**
     * Display the generated itinerary using the refactored approach
     */
    public function show(Request $request)
    {
        // Get data from request/session - this could come from various sources
        $itineraryData = $request->get('itinerary', session('itinerary_data', []));
        $trailId = $request->get('trail_id', $request->get('trail'));
        $buildData = $request->get('build', session('build_data', []));
        $weatherData = $request->get('weather', []);

        // Resolve trail if ID provided
        $trail = null;
        if ($trailId && is_numeric($trailId)) {
            try {
                $trail = Trail::find($trailId);
            } catch (\Exception $e) {
                // Handle trail not found
            }
        }

        // Generate the complete itinerary using our service
        $generatedData = $this->itineraryService->generateItinerary(
            $itineraryData,
            $trail,
            $buildData,
            $weatherData
        );

        // Extract data for the view
        extract($generatedData);

        // Return the refactored view
        return view('hiker.itinerary.generated-refactored', compact(
            'itinerary',
            'trail', 
            'build',
            'weatherData',
            'routeData',
            'dateInfo',
            'dayActivities',
            'nightActivities'
        ));
    }

    /**
     * API endpoint to generate itinerary data as JSON
     */
    public function generateApi(Request $request)
    {
        $itineraryData = $request->input('itinerary', []);
        $trailData = $request->input('trail', []);
        $buildData = $request->input('build', []);
        $weatherData = $request->input('weather', []);

        try {
            $generatedData = $this->itineraryService->generateItinerary(
                $itineraryData,
                $trailData,
                $buildData,
                $weatherData
            );

            return response()->json([
                'success' => true,
                'data' => $generatedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate itinerary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview method for testing the refactored system
     */
    public function preview()
    {
        // Sample data for testing
        $sampleItinerary = [
            'duration_days' => 3,
            'nights' => 2,
            'start_time' => '06:00',
            'start_date' => now()->addWeek()->format('Y-m-d'),
            'trail_name' => 'Sample Mountain Trail',
            'distance_km' => 15,
            'elevation_m' => 800,
            'difficulty' => 'moderate'
        ];

        $sampleTrail = [
            'name' => 'Sample Mountain Trail',
            'region' => 'Mountain Province',
            'distance_km' => 15,
            'elevation_m' => 800,
            'difficulty' => 'moderate',
            'overnight_allowed' => true,
            'route_description' => 'A beautiful mountain trail with scenic views and moderate difficulty.',
            'best_season' => 'March to May, October to December',
            'package_inclusions' => 'Guide, meals, camping equipment',
            'terrain_notes' => 'Rocky terrain with some steep sections'
        ];

        $sampleBuild = [
            'transport_mode' => 'pickup',
            'vehicle' => 'Air-conditioned Van',
            'meeting_point' => 'City Center Mall'
        ];

        $sampleWeather = [
            1 => ['06:00' => 'Clear / 18°C', '12:00' => 'Partly Cloudy / 25°C', '18:00' => 'Clear / 20°C'],
            2 => ['06:00' => 'Foggy / 16°C', '12:00' => 'Sunny / 28°C', '18:00' => 'Clear / 22°C'],
            3 => ['06:00' => 'Clear / 17°C', '12:00' => 'Sunny / 26°C', '18:00' => 'Partly Cloudy / 21°C']
        ];

        // Generate using our service
        $generatedData = $this->itineraryService->generateItinerary(
            $sampleItinerary,
            $sampleTrail,
            $sampleBuild,
            $sampleWeather
        );

        // Extract data for the view
        extract($generatedData);

        return view('hiker.itinerary.generated-refactored', compact(
            'itinerary',
            'trail', 
            'build',
            'weatherData',
            'routeData',
            'dateInfo',
            'dayActivities',
            'nightActivities'
        ));
    }
}