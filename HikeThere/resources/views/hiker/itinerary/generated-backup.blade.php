@php
/**
 * Refactored Generated Itinerary Blade
 * 
 * This view now uses service classes and components for better maintainability.
 * All complex logic has been moved to dedicated services.
 */

use App\Services\ItineraryGeneratorService;

// Debug: Check what we're receiving
// dd('Variables passed to template:', compact('itinerary', 'trail', 'build', 'weatherData'));

// Ensure we have proper variable types
$itinerary = $itinerary ?? [];
$trail = $trail ?? null;
$build = $build ?? null;
$weatherData = $weatherData ?? [];

// Generate the complete itinerary data using the service
$itineraryService = app(ItineraryGeneratorService::class);
$generatedData = $itineraryService->generateItinerary($itinerary, $trail, $build, $weatherData);

// Extract the generated data for the view
$itinerary = $generatedData['itinerary'];
$trail = $generatedData['trail'];
$build = $generatedData['build'];
$weatherData = $generatedData['weatherData'];
$routeData = $generatedData['routeData'];
$dateInfo = $generatedData['dateInfo'];
$dayActivities = $generatedData['dayActivities'];
$nightActivities = $generatedData['nightActivities'];
@endphp

<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <!-- Header: Trail Route & Details -->
            <x-itinerary.header 
                :trail="$trail" 
                :dateInfo="$dateInfo" 
                :routeData="$routeData" 
            />

            <!-- Trail Summary Boxes -->
            <x-itinerary.summary-boxes 
                :trail="$trail" 
                :routeData="$routeData" 
                :build="$build" 
            />

            <!-- Itinerary Tables per day -->
            <div class="space-y-8">
                @for ($day = 1; $day <= $dateInfo['duration_days']; $day++)
                    <x-itinerary.day-table 
                        :day="$day"
                        :activities="$dayActivities[$day] ?? []"
                        :dateInfo="$dateInfo"
                        :weatherData="$weatherData"
                        :build="$build"
                    />

                    {{-- Insert Night table after each day (except the last day) --}}
                    @if ($day <= $dateInfo['nights'])
                        <x-itinerary.night-table 
                            :night="$day"
                            :activities="$nightActivities[$day] ?? []"
                            :dateInfo="$dateInfo"
                            :weatherData="$weatherData"
                            :build="$build"
                        />
                    @endif
                @endfor
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <x-itinerary.additional-info 
                :trail="$trail" 
                :build="$build" 
            />
        </div>
    </div>
</x-app-layout>