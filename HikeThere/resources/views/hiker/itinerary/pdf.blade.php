<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $itinerary->title }} - PDF</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            border-radius: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section h2 {
            color: #10B981;
            border-bottom: 2px solid #10B981;
            padding-bottom: 8px;
            margin-bottom: 15px;
            font-size: 20px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-card {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #10B981;
        }
        
        .info-card h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 16px;
            font-weight: 600;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            color: #6b7280;
            font-weight: 500;
        }
        
        .info-value {
            color: #1f2937;
            font-weight: 600;
        }
        
        .route-summary {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .route-summary h3 {
            margin: 0 0 15px 0;
            color: #065f46;
            font-size: 18px;
        }
        
        .route-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        
        .route-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .route-dot.departure { background: #10B981; }
        .route-dot.destination { background: #3B82F6; }
        .route-dot.transport { background: #8B5CF6; }
        .route-dot.distance { background: #F59E0B; }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .schedule-table th {
            background: #10B981;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            border-right: 1px solid #059669;
        }
        
        .schedule-table th:last-child {
            border-right: none;
        }
        
        .schedule-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        .schedule-table td:last-child {
            border-right: none;
        }
        
        .schedule-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .condition-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .condition-departure { background: #dcfce7; color: #166534; }
        .condition-arrival { background: #dbeafe; color: #1e40af; }
        .condition-travel { background: #f3e8ff; color: #7c3aed; }
        
        .transport-mode {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .mode-departure { background: #dcfce7; color: #166534; }
        .mode-arrival { background: #dbeafe; color: #1e40af; }
        .mode-travel { background: #f3e8ff; color: #7c3aed; }
        
        .waypoints {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
        }
        
        .waypoint-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 12px;
            background: white;
            border-radius: 6px;
            border-left: 3px solid #10B981;
        }
        
        .waypoint-item:last-child {
            margin-bottom: 0;
        }
        
        .waypoint-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10B981;
            margin: 6px 12px 0 0;
            flex-shrink: 0;
        }
        
        .waypoint-content h4 {
            margin: 0 0 5px 0;
            color: #1f2937;
            font-size: 14px;
            font-weight: 600;
        }
        
        .waypoint-content p {
            margin: 0 0 5px 0;
            color: #6b7280;
            font-size: 12px;
        }
        
        .waypoint-meta {
            display: flex;
            gap: 15px;
            font-size: 11px;
            color: #9ca3af;
        }
        
        .gear-list, .safety-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .gear-list li, .safety-list li {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            padding: 8px 12px;
            background: #f0fdf4;
            border-radius: 6px;
            border-left: 3px solid #10B981;
        }
        
        .gear-list li:last-child, .safety-list li:last-child {
            margin-bottom: 0;
        }
        
        .gear-list li::before, .safety-list li::before {
            content: "✓";
            color: #10B981;
            font-weight: bold;
            margin-right: 8px;
        }
        
        .emergency-contacts {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
        }
        
        .emergency-contacts h3 {
            margin: 0 0 15px 0;
            color: #991b1b;
            font-size: 18px;
        }
        
        .contact-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #fecaca;
        }
        
        .contact-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .contact-label {
            color: #7f1d1d;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .contact-value {
            color: #991b1b;
            font-weight: 600;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        
        .route-map-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { margin: 0; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $itinerary->title }}</h1>
        <p>Generated on {{ $itinerary->created_at->format('M d, Y \a\t g:i A') }}</p>
    </div>

    <!-- Route Summary -->
    @if($itinerary->route_summary)
    <div class="section route-summary">
        <h3>🚗 Journey Overview</h3>
        <div class="route-item">
            <div class="route-dot departure"></div>
            <div>
                <strong>Departure:</strong> {{ $itinerary->route_summary['departure'] }}
            </div>
        </div>
        <div class="route-item">
            <div class="route-dot destination"></div>
            <div>
                <strong>Destination:</strong> {{ $itinerary->route_summary['destination'] }}
            </div>
        </div>
        {{-- Transportation removed --}}
        <div class="route-item">
            <div class="route-dot distance"></div>
            <div>
                <strong>Total Distance:</strong> {{ $itinerary->route_summary['total_distance'] }}
            </div>
        </div>
    </div>
    @endif

    <!-- Route Map -->
    @if($itinerary->static_map_url || ($itinerary->route_coordinates && count($itinerary->route_coordinates) > 0))
    <div class="section">
        <h2>🗺️ Route Map</h2>
        <div class="map-container text-center">
            @if($itinerary->static_map_url)
            <img src="{{ $itinerary->static_map_url }}" 
                 alt="Route Map" 
                 class="route-map-image"
                 style="max-width: 100%; height: auto; border: 1px solid #d1d5db; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            @else
            <p class="text-gray-500 py-8">Route map not available</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Basic Information -->
    <div class="section">
        <h2>📋 Trail Information</h2>
        <div class="info-grid">
            <div class="info-card">
                <h3>Trail Details</h3>
                <div class="info-item">
                    <span class="info-label">Trail Name:</span>
                    <span class="info-value">{{ $itinerary->trail_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Difficulty Level:</span>
                    <span class="info-value">{{ $itinerary->difficulty_level }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estimated Duration:</span>
                    <span class="info-value">{{ $itinerary->estimated_duration }}</span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Physical Details</h3>
                <div class="info-item">
                    <span class="info-label">Distance:</span>
                    <span class="info-value">{{ $itinerary->distance }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Elevation Gain:</span>
                    <span class="info-value">{{ $itinerary->elevation_gain }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Best Time:</span>
                    <span class="info-value">{{ $itinerary->best_time_to_hike }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule -->
    @if($itinerary->schedule)
    <div class="section">
        <h2>📅 Schedule</h2>
        <div class="info-grid">
            <div class="info-card">
                <h3>Timing</h3>
                <div class="info-item">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($itinerary->schedule['date'])->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Start Time:</span>
                    <span class="info-value">{{ $itinerary->schedule['start_time'] }}</span>
                </div>
                @if(isset($itinerary->schedule['estimated_arrival']))
                <div class="info-item">
                    <span class="info-label">Estimated Arrival:</span>
                    <span class="info-value">{{ $itinerary->schedule['estimated_arrival'] }}</span>
                </div>
                @endif
            </div>
            
            {{-- Transportation section removed from PDF. Use transport_details only when available. --}}
        </div>
    </div>
    @endif

    <!-- Daily Schedule -->
    @if($itinerary->daily_schedule_with_weather && count($itinerary->daily_schedule_with_weather) > 0)
    <div class="section page-break">
        <h2>🗓️ Detailed Itinerary Schedule</h2>
        
        @foreach($itinerary->daily_schedule_with_weather as $day)
        <div style="margin-bottom: 25px;">
            <h3 style="color: #10B981; margin-bottom: 15px; font-size: 18px;">
                {{ $day['day_label'] }} - {{ \Carbon\Carbon::parse($day['date'])->format('l, M d, Y') }}
            </h3>
            
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Condition</th>
                        <th>Temperature</th>
                        <th>Transport Mode</th>
                        <th>Duration</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($day['activities'] as $activity)
                    <tr>
                        <td><strong>{{ $activity['time'] }}</strong></td>
                        <td>{{ $activity['location'] }}</td>
                        <td>
                            <span class="condition-badge 
                                {{ str_contains(strtolower($activity['condition']), 'departure') ? 'condition-departure' : 
                                   (str_contains(strtolower($activity['condition']), 'arrival') ? 'condition-arrival' : 'condition-travel') }}">
                                {{ $activity['condition'] }}
                            </span>
                        </td>
                        <td>{{ $activity['temperature'] }}</td>
                        <td>
                            <span class="transport-mode 
                                {{ str_contains(strtolower($activity['transport_mode']), 'departure') ? 'mode-departure' : 
                                   (str_contains(strtolower($activity['transport_mode']), 'arrival') ? 'mode-arrival' : 'mode-travel') }}">
                                {{ $activity['transport_mode'] }}
                            </span>
                        </td>
                        <td>{{ $activity['duration'] }}</td>
                        <td>{{ $activity['note'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Transport Details for Commute Mode -->
    @if(isset($itinerary->transport_details) && count($itinerary->transport_details) > 0)
    <div class="section">
        <h2>🚌 Public Transportation Details</h2>
        @foreach($itinerary->transport_details as $transport)
        <div style="background: #f8fafc; border-radius: 8px; padding: 15px; margin-bottom: 15px; border-left: 4px solid #3B82F6;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-weight: 600; color: #1f2937;">Step {{ $transport['step'] }}</span>
                @php
                    $bg = $transport['mode'] === 'Public Transport' ? '#dbeafe' : '#dcfce7';
                    $color = $transport['mode'] === 'Public Transport' ? '#1e40af' : '#166534';
                @endphp
                <span style="background: {{ $bg }}; color: {{ $color }}; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">{{ $transport['mode'] }}</span>
            </div>
            
            @if($transport['mode'] === 'Public Transport')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <strong style="color: #6b7280; font-size: 12px;">Line/Vehicle:</strong><br>
                    <span style="color: #1f2937; font-weight: 600;">{{ $transport['line'] }} ({{ $transport['vehicle'] }})</span>
                </div>
                <div>
                    <strong style="color: #6b7280; font-size: 12px;">Duration:</strong><br>
                    <span style="color: #1f2937; font-weight: 600;">{{ $transport['duration'] }}</span>
                </div>
                <div>
                    <strong style="color: #6b7280; font-size: 12px;">From:</strong><br>
                    <span style="color: #1f2937; font-weight: 600;">{{ $transport['departure_stop'] }}</span>
                </div>
                <div>
                    <strong style="color: #6b7280; font-size: 12px;">To:</strong><br>
                    <span style="color: #1f2937; font-weight: 600;">{{ $transport['arrival_stop'] }}</span>
                </div>
            </div>
            @else
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <strong style="color: #6b7280; font-size: 12px;">Distance:</strong><br>
                    <span style="color: #1f2937; font-weight: 600;">{{ $transport['distance'] }}</span>
                </div>
                <div>
                    <strong style="color: #6b7280; font-size: 12px;">Duration:</strong><br>
                    <span style="color: #1f2937; font-weight: 600;">{{ $transport['duration'] }}</span>
                </div>
            </div>
            @endif
            
            <div style="border-top: 1px solid #e5e7eb; padding-top: 10px;">
                <strong style="color: #6b7280; font-size: 12px;">Instructions:</strong><br>
                <span style="color: #1f2937; font-size: 12px;">{{ $transport['instruction'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Route Waypoints -->
    @if($itinerary->waypoints)
    <div class="section">
        <h2>📍 Route Waypoints</h2>
        <div class="waypoints">
            @foreach($itinerary->waypoints as $waypoint)
            <div class="waypoint-item">
                <div class="waypoint-dot"></div>
                <div class="waypoint-content">
                    <h4>{{ $waypoint['name'] }}</h4>
                    <p>{{ $waypoint['description'] }}</p>
                    <div class="waypoint-meta">
                        <span>{{ $waypoint['distance'] }}</span>
                        <span>{{ $waypoint['elevation'] }}</span>
                        @if(isset($waypoint['time']))
                            <span>{{ $waypoint['time'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Route Description -->
    <div class="section">
        <h2>🗺️ Route Description</h2>
        <p style="background: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #10B981; margin: 0;">
            {{ $itinerary->route_description }}
        </p>
    </div>

    <!-- Weather & Safety -->
    <div class="section">
        <h2>🌤️ Weather & Safety</h2>
        <div class="info-grid">
            <div class="info-card">
                <h3>Weather Conditions</h3>
                <p style="margin: 0; color: #1f2937; font-size: 14px;">{{ $itinerary->weather_conditions }}</p>
            </div>
            
            <div class="info-card">
                <h3>Safety Tips</h3>
                <ul class="safety-list">
                    @foreach($itinerary->safety_tips as $tip)
                        <li>{{ $tip }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Gear Recommendations -->
    @if($itinerary->gear_recommendations)
    <div class="section">
        <h2>🎒 Gear Recommendations</h2>
        <ul class="gear-list">
            @foreach($itinerary->gear_recommendations as $gear)
                <li>{{ $gear }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Emergency Contacts -->
    @if($itinerary->emergency_contacts)
    <div class="section emergency-contacts">
        <h3>🚨 Emergency Contacts</h3>
        @foreach($itinerary->emergency_contacts as $type => $contact)
            <div class="contact-item">
                <span class="contact-label">{{ str_replace('_', ' ', $type) }}:</span>
                <span class="contact-value">{{ $contact }}</span>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Stopovers and Side Trips -->
    @if(($itinerary->stopovers && count($itinerary->stopovers) > 0) || ($itinerary->sidetrips && count($itinerary->sidetrips) > 0))
    <div class="section">
        <h2>🛣️ Additional Stops</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            
            @if($itinerary->stopovers && count($itinerary->stopovers) > 0)
            <div>
                <h3 style="color: #3B82F6; margin-bottom: 15px; font-size: 16px;">Stopovers</h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($itinerary->stopovers as $stopover)
                        <li style="display: flex; align-items: center; margin-bottom: 8px; padding: 8px 12px; background: #eff6ff; border-radius: 6px; border-left: 3px solid #3B82F6;">
                            <span style="color: #3B82F6; margin-right: 8px;">⏰</span>
                            <span style="color: #1f2937;">{{ $stopover }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($itinerary->sidetrips && count($itinerary->sidetrips) > 0)
            <div>
                <h3 style="color: #8B5CF6; margin-bottom: 15px; font-size: 16px;">Side Trips</h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($itinerary->sidetrips as $sidetrip)
                        <li style="display: flex; align-items: center; margin-bottom: 8px; padding: 8px 12px; background: #faf5ff; border-radius: 6px; border-left: 3px solid #8B5CF6;">
                            <span style="color: #8B5CF6; margin-right: 8px;">⚡</span>
                            <span style="color: #1f2937;">{{ $sidetrip }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>HikeThere</strong> - Your Personal Hiking Companion</p>
        <p>Itinerary created on {{ $itinerary->created_at->format('M d, Y \a\t g:i A') }}</p>
        <p>For questions or support, contact our team</p>
    </div>
</body>
</html>
