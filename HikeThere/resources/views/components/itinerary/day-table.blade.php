@props(['day', 'activities', 'dateInfo', 'weatherData', 'build', 'trail'])

@php
    use App\Services\WeatherHelperService;
    use App\Services\TrailCalculatorService;
    
    $weatherHelper = app(WeatherHelperService::class);
    $trailCalculator = app(TrailCalculatorService::class);
    
    $baseDateForDay = $dateInfo['start_date']->copy()->addDays($day - 1);
@endphp

<div class="bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Day {{ $day }}</h2>
            <p class="text-sm font-medium text-gray-600">{{ $baseDateForDay->toFormattedDateString() }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full table-fixed divide-y divide-gray-200 border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300 w-20">Time</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300 w-1/4">Activity</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300 w-16">Elapsed</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300 w-16">Distance</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300 w-24">Weather</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300 w-20">Transport</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Notes</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($activities as $activity)
                    @php
                        $activity = (array) $activity;
                        $minutes = intval($activity['minutes'] ?? 0);
                        $timeLabel = $weatherHelper->computeTimeForRow($baseDateForDay, $dateInfo['start_time'], $day, $minutes);
                        
                        // Get dynamic weather based on activity position and time
                        $trail = $trail ?? null; // Get trail from parent scope
                        $weatherLabel = $weatherHelper->getWeatherFor($weatherData, $day, $timeLabel, $activity, $trail) ?? 'Fair / 25°C';
                        
                        // Transport calculation - improve logic based on activity type and location
                        $transportLabel = 'N/A';
                        $activityType = $activity['type'] ?? '';
                        $activityLocation = strtolower($activity['location'] ?? '');
                        
                        // Determine transport based on activity type
                        if (in_array($activityType, ['meal', 'overnight', 'rest', 'photo', 'checkpoint'])) {
                            $transportLabel = 'N/A'; // No transport needed for breaks/meals/photos
                        } elseif ($activityType === 'prep' || str_contains($activityLocation, 'trailhead')) {
                            $transportLabel = $build['vehicle'] ?? 'Transport';
                        } elseif (in_array($activityType, ['hike', 'climb', 'descent']) || str_contains($activityLocation, 'trail')) {
                            $transportLabel = 'On foot';
                        } elseif (in_array($activityType, ['summit', 'camp'])) {
                            $transportLabel = 'On foot';
                        } elseif ($activityType === 'finish' || str_contains($activityLocation, 'transfer')) {
                            $transportLabel = $build['vehicle'] ?? 'Transport';
                        } else {
                            // Fallback to build transport info
                            if (strtolower(($build['transport_mode'] ?? 'commute')) === 'pickup') {
                                $transportLabel = $build['vehicle'] ?? 'Pickup vehicle';
                            } else {
                                $legs = $build['legs'] ?? [];
                                $found = null;
                                foreach ($legs as $leg) {
                                    if (isset($leg['from']) && str_contains($activityLocation, strtolower($leg['from']))) {
                                        $found = $leg['vehicle'] ?? $found;
                                        break;
                                    }
                                    if (isset($leg['to']) && str_contains($activityLocation, strtolower($leg['to']))) {
                                        $found = $leg['vehicle'] ?? $found;
                                        break;
                                    }
                                }
                                $transportLabel = $found ?? ($build['vehicle'] ?? 'Varies');
                            }
                        }
                        
                        // Generate comprehensive notes including weather preparation advice
                        $activityNotes = $weatherHelper->generateIntelligentNote($activity, $weatherLabel);
                        $weatherAdvice = $weatherHelper->getWeatherPreparationAdvice($weatherLabel, $activity['type'] ?? '', $activity['title'] ?? '');
                        
                        // Combine activity notes with weather advice
                        $notesParts = array_filter([$activityNotes, $weatherAdvice]);
                        $notes = implode(' ', $notesParts);
                    @endphp
                    <tr class="hover:bg-gray-50 border-b border-gray-200">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 border-r border-gray-300">
                            {{ $timeLabel }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 border-r border-gray-300">
                            <div class="font-semibold text-gray-900">{{ $activity['title'] ?? 'Activity' }}</div>
                            @if(!empty($activity['location']))
                                <div class="text-xs text-gray-600 mt-1">📍 {{ $activity['location'] }}</div>
                            @endif
                            @if(!empty($activity['description']))
                                <div class="text-xs text-gray-500 mt-1">{{ $activity['description'] }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-r border-gray-300">
                            {{ isset($activity['cum_minutes']) ? $trailCalculator->formatElapsed($activity['cum_minutes']) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-r border-gray-300">
                            {{ isset($activity['cum_distance_km']) ? $trailCalculator->formatDistanceKm($activity['cum_distance_km']) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm border-r border-gray-300">
                            <div class="flex items-center space-x-2">
                                @php
                                    $weatherParts = explode(' / ', $weatherLabel);
                                    $condition = $weatherParts[0] ?? $weatherLabel;
                                    $temperature = $weatherParts[1] ?? '';
                                @endphp
                                <div class="text-sm text-gray-700">
                                    <div class="font-medium">{{ $condition }}</div>
                                    @if($temperature)
                                        <div class="text-xs text-gray-500">{{ $temperature }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-r border-gray-300">
                            {{ $transportLabel }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="space-y-1">
                                @php
                                    // Use intelligent weather service for consolidated, non-redundant notes
                                    $intelligentWeatherService = app(\App\Services\IntelligentWeatherService::class);
                                    $smartNote = $intelligentWeatherService->generateSmartWeatherNote(
                                        $activity, 
                                        $weatherLabel, 
                                        $trail ?? null, 
                                        $day - 1  // Convert day number to 0-based index
                                    );
                                @endphp
                                
                                @if($smartNote)
                                    <div class="text-xs text-gray-600 leading-relaxed">{{ $smartNote }}</div>
                                @else
                                    <div class="text-xs text-gray-400">Standard hiking conditions - no special preparations needed.</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>