@php
/**
 * Print-Optimized Itinerary View
 * Designed to fit on 2 pages with condensed layout
 */

use App\Services\ItineraryGeneratorService;
use App\Services\WeatherHelperService;

// Handle variables that may not be passed from controller
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
$preHikeActivities = $generatedData['preHikeActivities'] ?? [];
$emergencyInfo = $generatedData['emergencyInfo'] ?? [];

// Initialize weather helper for time calculations
$weatherHelper = app(WeatherHelperService::class);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $titleName = is_array($trail) ? ($trail['name'] ?? 'Itinerary') : ($trail->name ?? 'Itinerary');
    @endphp
    <title>{{ $titleName }} - Print View</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
        }
        
        h1 { font-size: 16pt; margin-bottom: 8px; }
        h2 { font-size: 12pt; margin-top: 10px; margin-bottom: 6px; }
        h3 { font-size: 10pt; margin-bottom: 4px; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            page-break-inside: avoid;
        }
        
        th, td {
            padding: 4px 6px;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: 600;
        }
        
        .summary-box {
            display: inline-block;
            padding: 6px 10px;
            margin: 4px;
            border-radius: 4px;
            background: #f0f9ff;
            border: 1px solid #3b82f6;
            font-size: 8pt;
        }
        
        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .no-print {
            display: none;
        }
        
        @media screen {
            body {
                max-width: 210mm;
                margin: 20px auto;
                padding: 20px;
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }
            
            .no-print {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }
            
            .print-btn {
                background: #3b82f6;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
                margin: 0 5px;
            }
            
            .print-btn:hover {
                background: #2563eb;
            }
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <!-- Print/Close Buttons (only visible on screen) -->
    <div class="no-print">
        @if(!Auth::check())
            <!-- Shared Itinerary Banner for guests -->
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 20px; border-radius: 10px; margin-bottom: 15px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">📤 Shared Hiking Itinerary</div>
                <div style="font-size: 14px; opacity: 0.9;">Someone shared their hiking adventure with you!</div>
                <div style="margin-top: 10px;">
                    <a href="{{ route('register') }}" style="display: inline-block; background: white; color: #667eea; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 0 5px;">
                        Create Your Own Itinerary
                    </a>
                    <a href="{{ route('login') }}" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 8px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; margin: 0 5px;">
                        Login
                    </a>
                </div>
            </div>
        @endif
        <button onclick="window.print()" class="print-btn">🖨️ Print / Save as PDF</button>
        <button onclick="window.close()" class="print-btn" style="background: #64748b;">✕ Close</button>
    </div>

    <!-- Header -->
    <div class="section" style="border-bottom: 3px solid #4CAF50; padding-bottom: 10px;">
        @php
            $trailName = is_array($trail) ? ($trail['name'] ?? 'Trail Name') : ($trail->name ?? 'Trail Name');
            
            // Comprehensive location display with fallbacks (same as header component)
            $locationName = 'Location'; // Default fallback
            
            if (is_array($trail)) {
                if (!empty($trail['location']['name']) && !empty($trail['location']['province'])) {
                    $locationName = $trail['location']['name'] . ', ' . $trail['location']['province'];
                } elseif (!empty($trail['location']['name'])) {
                    $locationName = $trail['location']['name'];
                } elseif (!empty($trail['location_name'])) {
                    $locationName = $trail['location_name'];
                } elseif (!empty($trail['region'])) {
                    $locationName = $trail['region'];
                } elseif (!empty($trail['mountain_name'])) {
                    $locationName = $trail['mountain_name'];
                }
            } else {
                if (!empty($trail->location->name) && !empty($trail->location->province)) {
                    $locationName = $trail->location->name . ', ' . $trail->location->province;
                } elseif (!empty($trail->location->name)) {
                    $locationName = $trail->location->name;
                } elseif (!empty($trail->location_name)) {
                    $locationName = $trail->location_name;
                } elseif (!empty($trail->region)) {
                    $locationName = $trail->region;
                } elseif (!empty($trail->mountain_name)) {
                    $locationName = $trail->mountain_name;
                }
            }
        @endphp
        <h1 style="color: #059669; margin: 0;">{{ $trailName }}</h1>
        <div style="color: #64748b; font-size: 9pt;">
            📍 {{ $locationName }} | 
            📅 {{ isset($dateInfo['start_date']) ? \Carbon\Carbon::parse($dateInfo['start_date'])->format('M j, Y') : 'N/A' }} to {{ isset($dateInfo['end_date']) ? \Carbon\Carbon::parse($dateInfo['end_date'])->format('M j, Y') : 'N/A' }} ({{ $dateInfo['duration_days'] ?? 1 }} {{ ($dateInfo['duration_days'] ?? 1) > 1 ? 'days' : 'day' }})
        </div>
    </div>

    <!-- Summary Section -->
    <div class="section">
        <h2 style="color: #059669;">📊 Trail Summary</h2>
        <div>
            @php
                $difficulty = is_array($trail) ? ($trail['difficulty'] ?? 'N/A') : ($trail->difficulty ?? 'N/A');
                $elevationGain = is_array($trail) ? ($trail['elevation_gain'] ?? 'N/A') : ($trail->elevation_gain ?? 'N/A');
                
                // Get duration from trail database field
                $trailDuration = 'N/A';
                if (is_array($trail)) {
                    $trailDuration = $trail['duration'] ?? $trail['package']['duration'] ?? 'N/A';
                } else {
                    $trailDuration = $trail->duration ?? $trail->package->duration ?? 'N/A';
                }
            @endphp
            <span class="summary-box">⛰️ <strong>Difficulty:</strong> {{ $difficulty }}</span>
            <span class="summary-box">📏 <strong>Distance:</strong> {{ number_format($routeData['total_distance_km'] ?? 0, 1) }} km</span>
            <span class="summary-box">⏱️ <strong>Duration:</strong> {{ $trailDuration }}</span>
            <span class="summary-box">⬆️ <strong>Elevation:</strong> {{ $elevationGain }}m</span>
            @php
                $assessmentResult = is_array($build) ? ($build['assessment_result'] ?? null) : ($build->assessment_result ?? null);
            @endphp
            @if($assessmentResult)
                <span class="summary-box" style="background: {{ $assessmentResult >= 80 ? '#d1fae5' : ($assessmentResult >= 60 ? '#fef3c7' : '#fee2e2') }}; border-color: {{ $assessmentResult >= 80 ? '#059669' : ($assessmentResult >= 60 ? '#f59e0b' : '#dc2626') }};">
                    ✅ <strong>Readiness:</strong> {{ $assessmentResult }}%
                </span>
            @endif
        </div>
    </div>

    <!-- Trail Map Visualization -->
    @if(!empty($generatedData['staticMapUrl']))
    <div class="section">
        <h2 style="color: #059669;">🗺️ Trail Path Visualization</h2>
        <div style="background: #f0f9ff; padding: 8px; border-radius: 6px; border: 1px solid #3b82f6;">
            <img src="{{ $generatedData['staticMapUrl'] }}" 
                 alt="Trail Map" 
                 style="width: 100%; height: auto; border-radius: 4px; display: block;">
        </div>
        @php
            $routeDescription = is_array($trail) ? ($trail['route_description'] ?? null) : ($trail->route_description ?? null);
        @endphp
        @if($routeDescription)
        <div style="margin-top: 6px; font-size: 8pt; color: #475569; background: #f8fafc; padding: 6px; border-radius: 4px; border-left: 3px solid #059669;">
            <strong style="color: #059669;">Route Details:</strong> {{ $routeDescription }}
        </div>
        @endif
    </div>
    @endif

    <!-- Emergency Information -->
    @if (!empty($emergencyInfo))
    <div class="section">
        <h2 style="color: #dc2626;">🚨 Emergency Information</h2>
        <div style="background: #fef2f2; border: 2px solid #fca5a5; padding: 8px; border-radius: 6px;">
            <div class="grid-2" style="gap: 8px;">
                <!-- Emergency Numbers -->
                <div style="background: white; padding: 6px; border-radius: 4px; border: 1px solid #fca5a5;">
                    <h3 style="color: #dc2626; font-size: 9pt; margin-bottom: 4px;">📞 Emergency Numbers</h3>
                    <div style="font-size: 7pt; line-height: 1.4;">
                        @foreach ($emergencyInfo['emergency_numbers'] ?? [] as $number)
                            <div style="margin-bottom: 2px;">
                                <strong>{{ $number['service'] }}:</strong> {{ $number['number'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Your Emergency Contact -->
                @php
                    $hikerEmergencyContact = Auth::check() ? Auth::user() : null;
                @endphp
                <div style="background: white; padding: 6px; border-radius: 4px; border: 1px solid #fca5a5;">
                    <h3 style="color: #dc2626; font-size: 9pt; margin-bottom: 4px;">👤 Your Emergency Contact</h3>
                    <div style="font-size: 7pt; line-height: 1.4;">
                        @if ($hikerEmergencyContact && ($hikerEmergencyContact->emergency_contact_name || $hikerEmergencyContact->emergency_contact_phone))
                            @if (!empty($hikerEmergencyContact->emergency_contact_name))
                                <strong>{{ $hikerEmergencyContact->emergency_contact_name }}</strong><br>
                            @endif
                            @if (!empty($hikerEmergencyContact->emergency_contact_relationship))
                                <span style="color: #64748b;">{{ $hikerEmergencyContact->emergency_contact_relationship }}</span><br>
                            @endif
                            @if (!empty($hikerEmergencyContact->emergency_contact_phone))
                                <strong style="color: #2563eb;">{{ $hikerEmergencyContact->emergency_contact_phone }}</strong>
                            @endif
                        @else
                            <span style="color: #64748b; font-style: italic;">No emergency contact configured</span>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="grid-2" style="gap: 8px; margin-top: 8px;">
                <!-- Hospitals -->
                <div style="background: white; padding: 6px; border-radius: 4px; border: 1px solid #fca5a5;">
                    <h3 style="color: #dc2626; font-size: 9pt; margin-bottom: 4px;">🏥 Nearest Hospitals</h3>
                    <div style="font-size: 7pt; line-height: 1.4;">
                        @foreach (array_slice($emergencyInfo['hospitals'] ?? [], 0, 2) as $hospital)
                            <div style="margin-bottom: 4px;">
                                <strong>{{ $hospital['name'] }}</strong><br>
                                <span style="color: #64748b;">{{ $hospital['address'] }}</span>
                                @if (!empty($hospital['distance']))
                                    <br><strong style="color: #dc2626;">~{{ $hospital['distance'] }} away</strong>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Trail Emergency Contacts -->
                @php
                    $trailEmergencyContacts = is_array($trail) ? ($trail['emergency_contacts'] ?? null) : ($trail->emergency_contacts ?? null);
                @endphp
                <div style="background: white; padding: 6px; border-radius: 4px; border: 1px solid #fca5a5;">
                    <h3 style="color: #dc2626; font-size: 9pt; margin-bottom: 4px;">📋 Trail Contact Info</h3>
                    <div style="font-size: 7pt; line-height: 1.4;">
                        @if (!empty($trailEmergencyContacts))
                            {{ $trailEmergencyContacts }}
                        @else
                            <span style="color: #64748b; font-style: italic;">No trail-specific emergency contacts provided</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Pre-hike Transportation -->
    @if (!empty($preHikeActivities))
    <div class="section">
        <h2 style="color: #3b82f6;">🚌 Pre-hike Transportation</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Time</th>
                    <th style="width: 22%;">Activity</th>
                    <th style="width: 15%;">Location</th>
                    <th style="width: 15%;">Transport</th>
                    <th style="width: 38%;">Description / Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($preHikeActivities as $activity)
                <tr>
                    <td style="font-weight: 600; font-size: 7pt;">
                        @php
                            $minutes = $activity['minutes'] ?? 0;
                            $isMultiDay = $minutes >= 1440; // 24 hours or more
                            
                            if ($isMultiDay) {
                                // For multi-day: get actual time on previous day (modulo 24 hours)
                                $actualMinutes = $minutes % 1440;
                                $hours = intval($actualMinutes / 60);
                                $mins = $actualMinutes % 60;
                                $dayLabel = ' (Day Before)';
                            } else {
                                // For same day: normal calculation
                                $hours = intval($minutes / 60);
                                $mins = $minutes % 60;
                                $dayLabel = '';
                            }
                            
                            $timeLabel = sprintf('%02d:%02d%s', $hours, $mins, $dayLabel);
                            
                            // Transport for pre-hike
                            $buildArray = is_array($build) ? $build : (array)$build;
                            $transportLabel = $buildArray['vehicle'] ?? 'Transport';
                            
                            // Description - mirroring the generated view
                            $description = $activity['description'] ?? '';
                        @endphp
                        {{ $timeLabel }}
                    </td>
                    <td style="font-size: 7pt;">{{ $activity['title'] }}</td>
                    <td style="font-size: 7pt;">{{ $activity['location'] }}</td>
                    <td style="font-size: 7pt;">{{ $transportLabel }}</td>
                    <td style="font-size: 6pt;">{{ $description }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Daily Itinerary -->
    @for ($day = 1; $day <= $dateInfo['duration_days']; $day++)
        <div class="section">
            <h2 style="color: #f59e0b;">☀️ Day {{ $day }} - {{ \Carbon\Carbon::parse($dateInfo['start_date'])->addDays($day - 1)->format('D, M j, Y') }}</h2>
            
            @if(isset($weatherData[$day]))
            <div style="font-size: 8pt; color: #64748b; margin-bottom: 4px;">
                🌤️ Weather: {{ $weatherData[$day]['condition'] ?? 'N/A' }} | 🌡️ {{ $weatherData[$day]['temp_min'] ?? 'N/A' }}°C - {{ $weatherData[$day]['temp_max'] ?? 'N/A' }}°C
            </div>
            @endif

            <table>
                <thead>
                    <tr>
                        <th style="width: 7%;">Time</th>
                        <th style="width: 15%;">Activity</th>
                        <th style="width: 7%;">Elapsed</th>
                        <th style="width: 7%;">Distance</th>
                        <th style="width: 10%;">Weather</th>
                        <th style="width: 9%;">Transport</th>
                        <th style="width: 45%;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dayActivities[$day] ?? [] as $activity)
                    <tr>
                        <td style="font-weight: 600; font-size: 7pt;">
                            @php
                                $minutes = $activity['minutes'] ?? 0;
                                $baseDateForDay = \Carbon\Carbon::parse($dateInfo['start_date'])->addDays($day - 1);
                                $timeLabel = $weatherHelper->computeTimeForRow($baseDateForDay, $dateInfo['start_time'], $day, $minutes);
                                
                                // Get weather for this activity
                                $weatherLabel = $weatherHelper->getWeatherFor($weatherData, $day, $timeLabel, $activity, $trail) ?? 'Fair / 25°C';
                                
                                // Use intelligent weather service for consolidated notes (same as original generated view)
                                $intelligentWeatherService = app(\App\Services\IntelligentWeatherService::class);
                                $notes = $intelligentWeatherService->generateSmartWeatherNote(
                                    $activity, 
                                    $weatherLabel, 
                                    $trail ?? null, 
                                    $day - 1  // Convert day number to 0-based index
                                );
                                
                                // Calculate transport
                                $trailCalculator = app(\App\Services\TrailCalculatorService::class);
                                $transportLabel = 'N/A';
                                $activityType = $activity['type'] ?? '';
                                $activityLocation = strtolower($activity['location'] ?? '');
                                
                                if (in_array($activityType, ['meal', 'overnight', 'rest', 'photo', 'checkpoint'])) {
                                    $transportLabel = 'N/A';
                                } elseif ($activityType === 'prep' || str_contains($activityLocation, 'trailhead')) {
                                    $transportLabel = is_array($build) ? ($build['vehicle'] ?? 'Transport') : ($build->vehicle ?? 'Transport');
                                } elseif (in_array($activityType, ['hike', 'climb', 'descent']) || str_contains($activityLocation, 'trail')) {
                                    $transportLabel = 'On foot';
                                } elseif (in_array($activityType, ['summit', 'camp'])) {
                                    $transportLabel = 'On foot';
                                } elseif ($activityType === 'finish' || str_contains($activityLocation, 'transfer')) {
                                    $transportLabel = is_array($build) ? ($build['vehicle'] ?? 'Transport') : ($build->vehicle ?? 'Transport');
                                } else {
                                    $buildArray = is_array($build) ? $build : (array)$build;
                                    $transportMode = strtolower($buildArray['transport_mode'] ?? 'commute');
                                    if (in_array($transportMode, ['pickup', 'pick up', 'meet', 'meeting'])) {
                                        $transportLabel = $buildArray['vehicle'] ?? 'Pickup vehicle';
                                    } else {
                                        $legs = $buildArray['legs'] ?? [];
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
                                        $transportLabel = $found ?? ($buildArray['vehicle'] ?? 'Varies');
                                    }
                                }
                            @endphp
                            {{ $timeLabel }}
                        </td>
                        <td style="font-size: 7pt;">
                            <strong>{{ $activity['title'] }}</strong>
                            @if(!empty($activity['location']))
                                <br><span style="color: #059669;">📍 {{ $activity['location'] }}</span>
                            @endif
                        </td>
                        <td style="font-size: 7pt;">{{ isset($activity['cum_minutes']) ? $trailCalculator->formatElapsed($activity['cum_minutes']) : '-' }}</td>
                        <td style="font-size: 7pt;">{{ isset($activity['cum_distance_km']) ? $trailCalculator->formatDistanceKm($activity['cum_distance_km']) : '-' }}</td>
                        <td style="font-size: 7pt;">{{ $weatherLabel }}</td>
                        <td style="font-size: 7pt;">{{ $transportLabel }}</td>
                        <td style="font-size: 6pt;">{{ $notes }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align: center; color: #94a3b8;">No activities scheduled</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Night Activities --}}
        @if ($day <= $dateInfo['nights'])
        <div class="section">
            <h3 style="color: #6366f1;">🌙 Night {{ $day }}</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 7%;">Time</th>
                        <th style="width: 15%;">Activity</th>
                        <th style="width: 7%;">Elapsed</th>
                        <th style="width: 7%;">Distance</th>
                        <th style="width: 10%;">Weather</th>
                        <th style="width: 9%;">Transport</th>
                        <th style="width: 45%;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nightActivities[$day] ?? [] as $activity)
                    <tr>
                        <td style="font-weight: 600; font-size: 7pt;">
                            @php
                                $minutes = $activity['minutes'] ?? 0;
                                // For night activities, minutes represent absolute time from midnight
                                // Convert directly to HH:MM format (same as night-table component)
                                $hours = floor($minutes / 60);
                                $mins = $minutes % 60;
                                $timeLabel = sprintf('%02d:%02d', $hours, $mins);
                                
                                // Get weather for night activity
                                $weatherLabel = $weatherHelper->getWeatherFor($weatherData, $day, $timeLabel, $activity, $trail) ?? 'Clear / 20°C';
                                
                                // Use intelligent weather service for consolidated notes (same as original generated view)
                                $intelligentWeatherService = app(\App\Services\IntelligentWeatherService::class);
                                $notes = $intelligentWeatherService->generateSmartWeatherNote(
                                    $activity, 
                                    $weatherLabel, 
                                    $trail ?? null, 
                                    $day - 1  // Convert day number to 0-based index
                                );
                                
                                // Calculate transport - Night transport: usually same as day's pickup/vehicle
                                $trailCalculator = app(\App\Services\TrailCalculatorService::class);
                                $buildArray = is_array($build) ? $build : (array)$build;
                                $transportLabel = $buildArray['vehicle'] ?? ($buildArray['transport_mode'] ?? 'N/A');
                            @endphp
                            {{ $timeLabel }}
                        </td>
                        <td style="font-size: 7pt;">
                            <strong>{{ $activity['title'] }}</strong>
                            @if(!empty($activity['location']))
                                <br><span style="color: #6366f1;">📍 {{ $activity['location'] }}</span>
                            @endif
                        </td>
                        <td style="font-size: 7pt;">{{ isset($activity['cum_minutes']) ? $trailCalculator->formatElapsed($activity['cum_minutes']) : '-' }}</td>
                        <td style="font-size: 7pt;">{{ isset($activity['cum_distance_km']) ? $trailCalculator->formatDistanceKm($activity['cum_distance_km']) : '-' }}</td>
                        <td style="font-size: 7pt;">{{ $weatherLabel }}</td>
                        <td style="font-size: 7pt;">{{ $transportLabel }}</td>
                        <td style="font-size: 6pt;">{{ $notes }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align: center; color: #94a3b8;">No activities scheduled</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    @endfor

    <!-- Additional Trail Information -->
    <div class="section" style="margin-top: 15px;">
        <h2 style="color: #059669; border-bottom: 2px solid #059669; padding-bottom: 4px;">📋 Additional Information</h2>
        
        <div class="grid-2" style="margin-top: 8px;">
            <!-- Basic Info Column -->
            <div>
                <h3 style="color: #059669; font-size: 10pt;">Basic Information</h3>
                <div style="font-size: 8pt; line-height: 1.5; background: #f0fdfa; padding: 6px; border-radius: 4px; margin-bottom: 8px;">
                    <strong>Best Season:</strong> {{ is_array($trail) ? ($trail['best_season'] ?? 'N/A') : ($trail->best_season ?? 'N/A') }}<br>
                    <strong>Departure Point:</strong> 
                    @php
                        $departurePoint = null;
                        if (!empty($build) && is_array($build)) {
                            $departurePoint = $build['meeting_point'] ?? $build['pickup_point'] ?? $build['pickup'] ?? $build['meeting'] ?? null;
                            if (empty($departurePoint) && !empty($build['legs']) && is_array($build['legs'])) {
                                $firstLeg = $build['legs'][0] ?? null;
                                if (is_array($firstLeg)) {
                                    $departurePoint = $firstLeg['from'] ?? $firstLeg['to'] ?? null;
                                }
                            }
                        }
                        if (empty($departurePoint) && !empty($build['vehicle'])) {
                            $departurePoint = $build['vehicle'];
                        }
                        if (empty($departurePoint)) {
                            $departurePoint = is_array($trail) ? ($trail['departure_point'] ?? null) : ($trail->departure_point ?? null);
                        }
                    @endphp
                    {{ $departurePoint ?? 'N/A' }}<br>
                    <strong>Permit Required:</strong> {{ is_array($trail) ? (!empty($trail['permit_required']) ? 'Yes' : 'No') : (!empty($trail->permit_required) ? 'Yes' : 'No') }}
                </div>

                <h3 style="color: #3b82f6; font-size: 10pt;">Package Inclusions</h3>
                <div style="font-size: 8pt; line-height: 1.4; background: #eff6ff; padding: 6px; border-radius: 4px; margin-bottom: 8px;">
                    {{ is_array($trail) ? ($trail['package_inclusions'] ?? 'Not specified.') : ($trail->package_inclusions ?? 'Not specified.') }}
                </div>

                <h3 style="color: #f59e0b; font-size: 10pt;">Terrain & Notes</h3>
                <div style="font-size: 8pt; line-height: 1.4; background: #fef3c7; padding: 6px; border-radius: 4px;">
                    {{ is_array($trail) ? ($trail['terrain_notes'] ?? 'No specific terrain notes.') : ($trail->terrain_notes ?? 'No specific terrain notes.') }}
                </div>
            </div>

            <!-- Health & Safety Column -->
            <div>
                <h3 style="color: #14b8a6; font-size: 10pt;">Health & Fitness</h3>
                <div style="font-size: 8pt; line-height: 1.4; background: #f0fdfa; padding: 6px; border-radius: 4px; margin-bottom: 8px;">
                    {{ is_array($trail) ? ($trail['health_fitness'] ?? 'General fitness recommended.') : ($trail->health_fitness ?? 'General fitness recommended.') }}
                </div>

                <h3 style="color: #8b5cf6; font-size: 10pt;">Packing List</h3>
                <div style="font-size: 8pt; line-height: 1.4; background: #f5f3ff; padding: 6px; border-radius: 4px; margin-bottom: 8px;">
                    {{ is_array($trail) ? ($trail['packing_list'] ?? 'Bring standard hiking gear.') : ($trail->packing_list ?? 'Bring standard hiking gear.') }}
                </div>

                @php
                    $sideTrips = is_array($trail) ? ($trail['side_trips'] ?? null) : ($trail->side_trips ?? null);
                    $campsiteInfo = is_array($trail) ? ($trail['campsite_info'] ?? null) : ($trail->campsite_info ?? null);
                @endphp
                
                @if($sideTrips)
                <h3 style="color: #8b5cf6; font-size: 10pt;">Side Trips</h3>
                <div style="font-size: 8pt; line-height: 1.4; background: #f5f3ff; padding: 6px; border-radius: 4px; margin-bottom: 8px;">
                    {{ $sideTrips }}
                </div>
                @endif

                @if($campsiteInfo)
                <h3 style="color: #059669; font-size: 10pt;">Campsite Info</h3>
                <div style="font-size: 8pt; line-height: 1.4; background: #f0fdfa; padding: 6px; border-radius: 4px;">
                    {{ $campsiteInfo }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Essential Information -->
    <div class="section" style="margin-top: 15px; border-top: 2px solid #e5e7eb; padding-top: 10px;">
        <div class="grid-2">
            <div>
                <h3 style="color: #dc2626;">🚨 Emergency Contacts</h3>
                <div style="font-size: 8pt; line-height: 1.5;">
                    @php
                        $emergencyContacts = is_array($trail) ? ($trail['emergency_contacts'] ?? null) : ($trail->emergency_contacts ?? null);
                    @endphp
                    @if($emergencyContacts)
                        {{ $emergencyContacts }}
                    @else
                        • Emergency: 911<br>
                        • Park Rangers: (to be updated)
                    @endif
                </div>
            </div>
            <div>
                <h3 style="color: #059669;">🎒 Essential Gear</h3>
                <div style="font-size: 8pt; line-height: 1.5;">
                    @php
                        $gearList = is_array($build) ? ($build['gear_list'] ?? null) : ($build->gear_list ?? null);
                    @endphp
                    @if($gearList)
                        {!! nl2br(e(substr($gearList, 0, 200))) !!}...
                    @else
                        Water, food, first aid, flashlight, map, whistle
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 15px; padding-top: 8px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 7pt; color: #94a3b8;">
        Generated by HikeThere on {{ now()->format('F j, Y \a\t g:i A') }} | For: {{ Auth::check() ? Auth::user()->name : 'Hiker' }}
    </div>

    <script>
        // Auto-print when page loads (only for authenticated users)
        @if(Auth::check())
        window.addEventListener('load', function() {
            // Small delay to ensure content is fully rendered
            setTimeout(function() {
                window.print();
            }, 500);
        });
        @endif
        
        // Close window after print dialog is closed (optional)
        window.addEventListener('afterprint', function() {
            // Uncomment the line below if you want to auto-close after printing
            // window.close();
        });
    </script>
</body>
</html>
