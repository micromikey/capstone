<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $itinerary->title ?? 'Hiking Itinerary' }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9fafb;
        }
        .header {
            text-align: center;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .section {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section h2 {
            color: #10b981;
            border-bottom: 2px solid #10b981;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
        }
        .info-value {
            font-weight: 500;
            color: #111827;
        }
        .waypoint {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        .waypoint h4 {
            margin: 0 0 8px 0;
            color: #10b981;
            font-size: 16px;
        }
        .waypoint-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            font-size: 14px;
        }
        .waypoint-detail {
            display: flex;
            justify-content: space-between;
        }
        .gear-list, .safety-list {
            list-style: none;
            padding: 0;
        }
        .gear-list li, .safety-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
        }
        .gear-list li:before, .safety-list li:before {
            content: "✓";
            color: #10b981;
            font-weight: bold;
            margin-right: 10px;
        }
        .emergency-contacts {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
        }
        .emergency-contacts h3 {
            color: #dc2626;
            margin-top: 0;
        }
        .contact-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #fecaca;
        }
        .contact-item:last-child {
            border-bottom: none;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .section {
                box-shadow: none;
                border: 1px solid #e5e7eb;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $itinerary->title ?? 'Hiking Itinerary' }}</h1>
        <p>Generated on {{ $itinerary->created_at->format('M d, Y \a\t g:i A') ?? date('M d, Y \a\t g:i A') }}</p>
    </div>

    <!-- Trail Information -->
    <div class="section">
        <h2>Trail Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Trail Name:</span>
                <span class="info-value">{{ $itinerary->trail_name ?? 'Not specified' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Difficulty Level:</span>
                <span class="info-value">{{ $itinerary->difficulty_level ?? 'Not specified' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Estimated Duration:</span>
                <span class="info-value">{{ $itinerary->estimated_duration ?? 'Not specified' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Distance:</span>
                <span class="info-value">{{ $itinerary->distance ?? 'Not specified' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Elevation Gain:</span>
                <span class="info-value">{{ $itinerary->elevation_gain ?? 'Not specified' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Best Time to Hike:</span>
                <span class="info-value">{{ $itinerary->best_time_to_hike ?? 'Not specified' }}</span>
            </div>
        </div>
    </div>

    <!-- Schedule -->
    @if($itinerary->schedule)
    <div class="section">
        <h2>Schedule</h2>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($itinerary->schedule['date'])->format('M d, Y') }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Start Time:</span>
                <span class="info-value">{{ $itinerary->schedule['start_time'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Estimated Completion:</span>
                <span class="info-value">{{ $itinerary->schedule['estimated_completion'] }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Transportation:</span>
                <span class="info-value">{{ $itinerary->transportation ?? 'Not specified' }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Route Description -->
    <div class="section">
        <h2>Route Description</h2>
        <p>{{ $itinerary->route_description ?? 'No route description available.' }}</p>
    </div>

    <!-- Waypoints -->
    @if($itinerary->waypoints && count($itinerary->waypoints) > 0)
    <div class="section">
        <h2>Route Waypoints</h2>
        @foreach($itinerary->waypoints as $waypoint)
        <div class="waypoint">
            <h4>{{ $waypoint['name'] }}</h4>
            <p>{{ $waypoint['description'] }}</p>
            <div class="waypoint-details">
                <div class="waypoint-detail">
                    <span class="info-label">Distance:</span>
                    <span class="info-value">{{ $waypoint['distance'] }}</span>
                </div>
                <div class="waypoint-detail">
                    <span class="info-label">Elevation:</span>
                    <span class="info-value">{{ $waypoint['elevation'] }}</span>
                </div>
                @if(isset($waypoint['time']))
                <div class="waypoint-detail">
                    <span class="info-label">Time:</span>
                    <span class="info-value">{{ $waypoint['time'] }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Gear Recommendations -->
    @if($itinerary->gear_recommendations && count($itinerary->gear_recommendations) > 0)
    <div class="section">
        <h2>Gear Recommendations</h2>
        <ul class="gear-list">
            @foreach($itinerary->gear_recommendations as $gear)
            <li>{{ $gear }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Safety Tips -->
    @if($itinerary->safety_tips && count($itinerary->safety_tips) > 0)
    <div class="section">
        <h2>Safety Tips</h2>
        <ul class="safety-list">
            @foreach($itinerary->safety_tips as $tip)
            <li>{{ $tip }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Weather Conditions -->
    @if($itinerary->weather_conditions)
    <div class="section">
        <h2>Weather Conditions</h2>
        <p>{{ $itinerary->weather_conditions }}</p>
    </div>
    @endif

    <!-- Emergency Contacts -->
    @if($itinerary->emergency_contacts && count($itinerary->emergency_contacts) > 0)
    <div class="section">
        <div class="emergency-contacts">
            <h3>Emergency Contacts</h3>
            @foreach($itinerary->emergency_contacts as $type => $contact)
            <div class="contact-item">
                <span class="info-label">{{ ucwords(str_replace('_', ' ', $type)) }}:</span>
                <span class="info-value">{{ $contact }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Additional Stops -->
    @if(($itinerary->stopovers && count($itinerary->stopovers) > 0) || ($itinerary->sidetrips && count($itinerary->sidetrips) > 0))
    <div class="section">
        <h2>Additional Stops</h2>
        
        @if($itinerary->stopovers && count($itinerary->stopovers) > 0)
        <div style="margin-bottom: 20px;">
            <h3 style="color: #2563eb; margin-bottom: 10px;">Stopovers</h3>
            <ul class="safety-list">
                @foreach($itinerary->stopovers as $stopover)
                <li>{{ $stopover }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($itinerary->sidetrips && count($itinerary->sidetrips) > 0)
        <div>
            <h3 style="color: #7c3aed; margin-bottom: 10px;">Side Trips</h3>
            <ul class="safety-list">
                @foreach($itinerary->sidetrips as $sidetrip)
                <li>{{ $sidetrip }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>&copy; {{ date('Y') }} HikeThere • Hiking Itinerary Planner</p>
        <p>Generated on {{ $itinerary->created_at->format('M d, Y \a\t g:i A') ?? date('M d, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
