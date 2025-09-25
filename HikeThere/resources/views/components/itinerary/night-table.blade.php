@props(['night', 'activities', 'dateInfo', 'weatherData', 'build', 'trail'])

@php
    use App\Services\WeatherHelperService;
    use App\Services\TrailCalculatorService;
    
    $weatherHelper = app(WeatherHelperService::class);
    $trailCalculator = app(TrailCalculatorService::class);
    
    $baseDateForNight = $dateInfo['start_date']->copy()->addDays($night - 1);
@endphp

<div class="bg-white border border-gray-200 rounded-lg shadow-sm">
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">🌙 Night {{ $night }}</h2>
            <p class="text-sm font-medium text-gray-600">{{ $baseDateForNight->toFormattedDateString() }}</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full table-auto divide-y divide-gray-200 border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300">Time</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300">Activity</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300">Elapsed</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300">Distance</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300">Weather</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 border-r border-gray-300">Transport</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Notes</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($activities as $activity)
                    @php
                        $activity = (array) $activity;
                        $minutes = intval($activity['minutes'] ?? 0);
                        $timeLabel = $weatherHelper->computeTimeForRow($baseDateForNight, $dateInfo['start_time'], $night, $minutes);
                        
                        // Get dynamic weather based on activity position and time
                        $weatherLabel = $weatherHelper->getWeatherFor($weatherData, $night, $timeLabel, $activity, $trail) ?? 'N/A';
                        
                        // Night transport: usually same as day's pickup/vehicle
                        $transportLabel = $build['vehicle'] ?? ($build['transport_mode'] ?? 'TBD');
                        
                        // Generate comprehensive notes including weather preparation advice
                        $activityNotes = $weatherHelper->generateIntelligentNote($activity, $weatherLabel);
                        $weatherAdvice = $weatherHelper->getWeatherPreparationAdvice($weatherLabel, $activity['type'] ?? '', $activity['title'] ?? '');
                        
                        // Combine activity notes with weather advice
                        $notesParts = array_filter([$activityNotes, $weatherAdvice]);
                        $notes = implode(' ', $notesParts);
                        
                        $elapsedForRow = isset($activity['cum_minutes']) ? $activity['cum_minutes'] : $minutes;
                        $distanceForRow = $activity['cum_distance_km'] ?? null;
                    @endphp
                    <tr class="hover:bg-gray-50 border-b border-gray-200">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 border-r border-gray-300">
                            {{ $timeLabel }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 border-r border-gray-300">
                            <div class="font-semibold text-gray-900">{{ $activity['title'] ?? 'Night Activity' }}</div>
                            @if(!empty($activity['location']))
                                <div class="text-xs text-gray-600 mt-1">🏕️ {{ $activity['location'] }}</div>
                            @endif
                            @if(!empty($activity['description']))
                                <div class="text-xs text-gray-500 mt-1">{{ $activity['description'] }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-r border-gray-300">
                            {{ isset($elapsedForRow) ? $trailCalculator->formatElapsed($elapsedForRow) : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-r border-gray-300">
                            {{ isset($distanceForRow) ? $trailCalculator->formatDistanceKm($distanceForRow) : '-' }}
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
                                        $night - 1  // Convert night number to 0-based index
                                    );
                                @endphp
                                
                                @if($smartNote)
                                    <div class="text-xs text-gray-600 leading-relaxed">{{ $smartNote }}</div>
                                @else
                                    <div class="text-xs text-gray-400">Standard camping conditions - no special preparations needed.</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>