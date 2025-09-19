<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $trail->trail_name }} - Trail Map</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #2d5016;
        }
        .header h2 {
            margin: 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        .trail-info {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 3px 0;
        }
        .info-value {
            display: table-cell;
            padding: 3px 0;
        }
        .map-container {
            text-align: center;
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .map-container img {
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .elevation-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .elevation-stats {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .stat-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        .difficulty-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .difficulty-easy { background: #d4edda; color: #155724; }
        .difficulty-moderate { background: #fff3cd; color: #856404; }
        .difficulty-difficult { background: #f8d7da; color: #721c24; }
        .legend {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .legend h4 {
            margin: 0 0 8px 0;
            font-size: 14px;
        }
        .legend-item {
            margin-bottom: 5px;
        }
        .trail-notes {
            margin-top: 15px;
            background: #fff3cd;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        .emergency-info {
            margin-top: 15px;
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
        }
        .coordinate-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        .coordinate-table th,
        .coordinate-table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        .coordinate-table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $trail->trail_name }}</h1>
        <h2>{{ $trail->mountain_name }} - {{ $trail->location->name ?? 'Location' }}</h2>
    </div>

    <!-- Trail Information -->
    <div class="trail-info">
        <div class="info-row">
            <div class="info-label">Difficulty:</div>
            <div class="info-value">
                <span class="difficulty-badge difficulty-{{ strtolower($trail->difficulty) }}">
                    {{ $trail->difficulty_label }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Length:</div>
            <div class="info-value">{{ $trail->length ?? 'N/A' }} km</div>
        </div>
        <div class="info-row">
            <div class="info-label">Estimated Time:</div>
            <div class="info-value">{{ $trail->estimated_time_formatted ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Elevation Gain:</div>
            <div class="info-value">{{ $trail->elevation_gain ?? 'N/A' }} m</div>
        </div>
        <div class="info-row">
            <div class="info-label">Highest Point:</div>
            <div class="info-value">{{ $trail->elevation_high ?? 'N/A' }} m</div>
        </div>
        <div class="info-row">
            <div class="info-label">Lowest Point:</div>
            <div class="info-value">{{ $trail->elevation_low ?? 'N/A' }} m</div>
        </div>
        <div class="info-row">
            <div class="info-label">Best Season:</div>
            <div class="info-value">{{ $trail->best_season ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Trail Description -->
    @if($trail->summary)
    <div>
        <h3>Description</h3>
        <p>{{ $trail->summary }}</p>
    </div>
    @endif

    <!-- Trail Map -->
    <div class="map-container">
        <h3>Trail Route Map</h3>
        <img src="{{ $staticMapUrl }}" alt="Trail Map for {{ $trail->trail_name }}">
    </div>

    <!-- Elevation Profile -->
    @if(!empty($elevationData))
    <div class="elevation-section">
        <h3>Elevation Profile</h3>
        <div class="elevation-stats">
            @php
                $elevations = array_column($elevationData, 'elevation');
                $maxElev = !empty($elevations) ? max($elevations) : 0;
                $minElev = !empty($elevations) ? min($elevations) : 0;
                $totalGain = $maxElev - $minElev;
            @endphp
            <div class="stat-item"><strong>Max Elevation:</strong> {{ round($maxElev) }} m</div>
            <div class="stat-item"><strong>Min Elevation:</strong> {{ round($minElev) }} m</div>
            <div class="stat-item"><strong>Total Elevation Gain:</strong> {{ round($totalGain) }} m</div>
            <div class="stat-item"><strong>Sample Points:</strong> {{ count($elevationData) }}</div>
        </div>

        <!-- Simple text-based elevation visualization -->
        <div style="margin-top: 10px;">
            <strong>Elevation Points (every ~{{ round(($trail->length ?? 1) / count($elevationData), 1) }} km):</strong>
            <br>
            @foreach(array_chunk($elevationData, 10) as $chunk)
                @foreach($chunk as $index => $point)
                    {{ round($point['elevation']) }}m{{ !$loop->last ? ', ' : '' }}
                @endforeach
                @if(!$loop->last)<br>@endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Trail Features -->
    @if($trail->features && count($trail->features) > 0)
    <div>
        <h3>Trail Features</h3>
        <ul>
            @foreach($trail->features as $feature)
                <li>{{ $feature }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Legend and Safety Information -->
    <div class="legend">
        <h4>Map Legend</h4>
        <div class="legend-item"><strong>Green Marker (S):</strong> Trail Start Point</div>
        <div class="legend-item"><strong>Red Marker (E):</strong> Trail End Point</div>
        <div class="legend-item"><strong>Red Line:</strong> Trail Path/Route</div>
        <div class="legend-item"><strong>Terrain:</strong> Topographical map showing elevation changes</div>
    </div>

    <!-- Important Notes -->
    @if($trail->terrain_notes || $trail->other_trail_notes)
    <div class="trail-notes">
        <h4>⚠️ Important Trail Notes</h4>
        @if($trail->terrain_notes)
            <p><strong>Terrain:</strong> {{ $trail->terrain_notes }}</p>
        @endif
        @if($trail->other_trail_notes)
            <p><strong>Additional Notes:</strong> {{ $trail->other_trail_notes }}</p>
        @endif
    </div>
    @endif

    <!-- Emergency Information -->
    @if($trail->emergency_contacts)
    <div class="emergency-info">
        <h4>🚨 Emergency Contacts</h4>
        <p>{{ $trail->emergency_contacts }}</p>
    </div>
    @endif

    <!-- GPS Coordinates (Page 2) -->
    <div class="page-break">
        <h3>GPS Coordinates for Trail Route</h3>
        <p><strong>Use these coordinates for GPS navigation and tracking your progress:</strong></p>
        
        <table class="coordinate-table">
            <thead>
                <tr>
                    <th>Point #</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coordinates as $index => $coord)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ number_format($coord['lat'], 6) }}</td>
                    <td>{{ number_format($coord['lng'], 6) }}</td>
                    <td>
                        @if($index === 0)
                            Trail Start
                        @elseif($index === count($coordinates) - 1)
                            Trail End
                        @else
                            Waypoint
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 15px;">
            <h4>Using GPS Coordinates:</h4>
            <ol>
                <li>Load these coordinates into your GPS device or smartphone app</li>
                <li>Follow the waypoints in sequence from Start to End</li>
                <li>Use the coordinates to track your current position on the trail</li>
                <li>If you get lost, find the nearest waypoint to get back on track</li>
            </ol>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Generated on {{ date('Y-m-d H:i:s') }} | HikeThere Trail Map | For hiking purposes only - Always inform others of your hiking plans
    </div>
</body>
</html>
