@props(['day', 'activities', 'dateInfo', 'weatherData', 'build', 'trail'])

@php
    use App\Services\WeatherHelperService;
    use App\Services\TrailCalculatorService;
    
    $weatherHelper = app(WeatherHelperService::class);
    $trailCalculator = app(TrailCalculatorService::class);
    
    $baseDateForDay = $dateInfo['start_date']->copy()->addDays($day - 1);
@endphp

<div class="bg-white/90 backdrop-blur-sm border-2 border-emerald-200/60 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300">
    <div class="bg-gradient-to-r from-emerald-500 via-teal-600 to-blue-600 px-8 py-6 border-b border-emerald-200 rounded-t-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 mr-4 shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white drop-shadow-lg">Day {{ $day }}</h2>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                <p class="text-sm font-bold text-white">{{ $baseDateForDay->toFormattedDateString() }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-b-2xl">
        <table class="min-w-full table-fixed divide-y-2 divide-emerald-100">
            <thead class="bg-gradient-to-r from-emerald-100 to-teal-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold text-emerald-800 border-r-2 border-emerald-200 w-20">Time</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-emerald-800 border-r-2 border-emerald-200 w-1/4">Activity</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-emerald-800 border-r-2 border-emerald-200 w-16">Elapsed</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-emerald-800 border-r-2 border-emerald-200 w-16">Distance</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-emerald-800 border-r-2 border-emerald-200 w-24">Weather</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-emerald-800 border-r-2 border-emerald-200 w-20">Transport</th>
                    <th class="px-6 py-4 text-left text-sm font-bold text-emerald-800">Notes</th>
                </tr>
            </thead>
            <tbody class="bg-white/80 backdrop-blur-sm divide-y-2 divide-emerald-50">
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
                    <tr class="hover:bg-gradient-to-r hover:from-emerald-25 hover:to-teal-25 transition-all duration-200 border-b-2 border-emerald-50">
                        <td class="px-6 py-4 text-sm font-bold text-emerald-800 border-r-2 border-emerald-100 bg-emerald-50/50">
                            <span class="bg-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm">{{ $timeLabel }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-800 border-r-2 border-emerald-100">
                            <div class="font-bold text-slate-900 mb-1">{{ $activity['title'] ?? 'Activity' }}</div>
                            @if(!empty($activity['location']))
                                <div class="text-xs text-teal-700 font-medium bg-teal-50 px-2 py-1 rounded-full inline-block mb-1">📍 {{ $activity['location'] }}</div>
                            @endif
                            @if(!empty($activity['description']))
                                <div class="text-xs text-slate-600 bg-slate-50 p-2 rounded-lg mt-1">{{ $activity['description'] }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-blue-700 border-r-2 border-emerald-100">
                            <span class="text-blue-700">{{ isset($activity['cum_minutes']) ? $trailCalculator->formatElapsed($activity['cum_minutes']) : '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-indigo-700 border-r-2 border-emerald-100">
                            <span class="text-indigo-700">{{ isset($activity['cum_distance_km']) ? $trailCalculator->formatDistanceKm($activity['cum_distance_km']) : '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm border-r-2 border-emerald-100 bg-orange-50/30">
                            <div class="bg-white/60 backdrop-blur-sm p-3 rounded-lg">
                                @php
                                    $weatherParts = explode(' / ', $weatherLabel);
                                    $condition = $weatherParts[0] ?? $weatherLabel;
                                    $temperature = $weatherParts[1] ?? '';
                                    
                                    // Weather-based color coding
                                    $weatherBgColor = match(strtolower($condition)) {
                                        'sunny', 'clear' => 'bg-yellow-500',
                                        'cloudy', 'overcast' => 'bg-gray-500',
                                        'rainy', 'rain' => 'bg-blue-600',
                                        'stormy' => 'bg-purple-600',
                                        'fair' => 'bg-emerald-500',
                                        default => 'bg-orange-500'
                                    };
                                @endphp
                                <div class="text-center">
                                    <div class="{{ $weatherBgColor }} text-white px-3 py-1 rounded-full text-xs font-bold mb-1">{{ $condition }}</div>
                                    @if($temperature)
                                        <div class="text-xs text-slate-600 font-medium">{{ $temperature }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-teal-700 border-r-2 border-emerald-100 bg-teal-50/30">
                            <span class="bg-teal-600 text-white px-2 py-1 rounded-md text-xs font-bold">{{ $transportLabel }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700 bg-slate-50/30">
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
                                    
                                    // Split notes into bullet points if they contain multiple sentences or key phrases
                                    $notesBullets = [];
                                    if($smartNote) {
                                        // Split by common separators and clean up
                                        $sentences = preg_split('/[.!?]\s*/', $smartNote, -1, PREG_SPLIT_NO_EMPTY);
                                        foreach($sentences as $sentence) {
                                            $sentence = trim($sentence);
                                            if(strlen($sentence) > 0) {
                                                $notesBullets[] = $sentence;
                                            }
                                        }
                                    }
                                @endphp
                                
                                @if(!empty($notesBullets))
                                    <div class="text-xs text-slate-700 leading-relaxed bg-white/60 p-3 rounded-lg border border-slate-200">
                                        <ul class="space-y-1">
                                            @foreach($notesBullets as $bullet)
                                                <li class="flex items-start">
                                                    <span class="text-slate-400 mr-2">•</span>
                                                    <span>{{ $bullet }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="text-xs text-slate-500 italic bg-slate-100/60 p-2 rounded-lg">
                                        <span class="text-slate-400 mr-2">•</span>
                                        Standard hiking conditions - no special preparations needed
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>