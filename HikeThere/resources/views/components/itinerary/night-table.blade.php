@props(['night', 'activities', 'dateInfo', 'weatherData', 'build', 'trail'])

@php
    use App\Services\WeatherHelperService;
    use App\Services\TrailCalculatorService;
    
    $weatherHelper = app(WeatherHelperService::class);
    $trailCalculator = app(TrailCalculatorService::class);
    
    $baseDateForNight = $dateInfo['start_date']->copy()->addDays($night - 1);
@endphp

<div class="bg-slate-800/90 backdrop-blur-sm border-2 border-indigo-300/60 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-300">
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-slate-700 px-8 py-6 border-b border-indigo-300 rounded-t-2xl">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-3 mr-4 shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white drop-shadow-lg">Night {{ $night }}</h2>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl px-4 py-2 border border-white/20">
                <p class="text-sm font-bold text-white">{{ $baseDateForNight->toFormattedDateString() }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-b-2xl">
        <table class="min-w-full divide-y-2 divide-indigo-200">
            <thead class="bg-gradient-to-r from-indigo-900 to-purple-900">
                <tr>
                    <th class="px-4 py-4 text-left text-xs font-bold text-white border-r-2 border-indigo-700 w-24">Time</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white border-r-2 border-indigo-700" style="min-width: 200px;">Activity</th>
                    <th class="px-4 py-4 text-left text-xs font-bold text-white border-r-2 border-indigo-700 w-28">Elapsed</th>
                    <th class="px-4 py-4 text-left text-xs font-bold text-white border-r-2 border-indigo-700 w-28">Distance</th>
                    <th class="px-4 py-4 text-left text-xs font-bold text-white border-r-2 border-indigo-700 w-32">Weather</th>
                    <th class="px-4 py-4 text-left text-xs font-bold text-white border-r-2 border-indigo-700 w-32">Transport</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-white" style="min-width: 200px;">Notes</th>
                </tr>
            </thead>
            <tbody class="bg-slate-50/90 backdrop-blur-sm divide-y-2 divide-indigo-100">
                @foreach ($activities as $activity)
                    @php
                        $activity = (array) $activity;
                        $minutes = intval($activity['minutes'] ?? 0);
                        // For night activities, minutes represent absolute time from midnight
                        // Convert directly to HH:MM format instead of using computeTimeForRow
                        $hours = floor($minutes / 60);
                        $mins = $minutes % 60;
                        $timeLabel = sprintf('%02d:%02d', $hours, $mins);
                        
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
                    <tr class="hover:bg-gradient-to-r hover:from-indigo-25 hover:to-purple-25 transition-all duration-200 border-b-2 border-indigo-100">
                        <td class="px-6 py-4 text-sm font-bold text-indigo-900 border-r-2 border-indigo-200 bg-indigo-100/50">
                            <span class="bg-indigo-700 text-white px-3 py-1 rounded-full text-xs font-bold shadow-sm">{{ $timeLabel }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-800 border-r-2 border-indigo-200">
                            <div class="font-bold text-slate-900 mb-1">{{ $activity['title'] ?? 'Night Activity' }}</div>
                            @if(!empty($activity['location']))
                                <div class="text-xs text-purple-700 font-medium bg-purple-100 px-2 py-1 rounded-full inline-block mb-1">🏕️ {{ $activity['location'] }}</div>
                            @endif
                            @if(!empty($activity['description']))
                                <div class="text-xs text-slate-600 bg-slate-100 p-2 rounded-lg mt-1">{{ $activity['description'] }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-blue-800 border-r-2 border-indigo-200">
                            <span class="text-blue-800">{{ isset($elapsedForRow) ? $trailCalculator->formatElapsed($elapsedForRow) : '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-indigo-800 border-r-2 border-indigo-200">
                            <span class="text-indigo-800">{{ isset($distanceForRow) ? $trailCalculator->formatDistanceKm($distanceForRow) : '-' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm border-r-2 border-indigo-200 bg-purple-50/30">
                            <div class="bg-slate-700/60 backdrop-blur-sm p-3 rounded-lg border border-slate-600">
                                @php
                                    $weatherParts = explode(' / ', $weatherLabel);
                                    $condition = $weatherParts[0] ?? $weatherLabel;
                                    $temperature = $weatherParts[1] ?? '';
                                    
                                    // Night weather color coding (darker theme)
                                    $nightWeatherBgColor = match(strtolower($condition)) {
                                        'clear', 'starry' => 'bg-yellow-600',
                                        'cloudy', 'overcast' => 'bg-slate-600',
                                        'rainy', 'rain' => 'bg-blue-700',
                                        'cold' => 'bg-cyan-700',
                                        'cool' => 'bg-indigo-600',
                                        default => 'bg-purple-600'
                                    };
                                @endphp
                                <div class="text-center">
                                    <div class="{{ $nightWeatherBgColor }} text-white px-3 py-1 rounded-full text-xs font-bold mb-1">{{ $condition }}</div>
                                    @if($temperature)
                                        <div class="text-xs text-slate-200 font-medium">{{ $temperature }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-purple-800 border-r-2 border-indigo-200 bg-purple-50/30">
                            <span class="bg-purple-700 text-white px-2 py-1 rounded-md text-xs font-bold">{{ $transportLabel }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-700 bg-slate-100/30">
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
                                    <div class="text-xs text-slate-500 italic bg-slate-200/60 p-2 rounded-lg">
                                        <span class="text-slate-400 mr-2">•</span>
                                        Standard camping conditions - no special preparations needed
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