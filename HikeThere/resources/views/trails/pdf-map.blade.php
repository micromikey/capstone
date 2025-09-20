<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $trail->trail_name }} - Trail Map</title>
    <style>
        /* Modern, print-friendly styles (keep TCPDF compatibility in mind) */
        body {
            font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
            margin: 0;
            padding: 18px;
            font-size: 12px;
            color: #222;
            background: #fff;
            -webkit-font-smoothing: antialiased;
        }
        .container { width: 100%; max-width: 820px; margin: 0 auto; }
        .card { background: #fff; border-radius: 6px; padding: 12px; box-shadow: 0 1px 0 rgba(0,0,0,0.05); margin-bottom: 12px; border: 1px solid #eee; }
        .header {
            display: table; width: 100%; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #e6e6e6;
        }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; width: 260px; }
        .logo { height: 48px; display: inline-block; vertical-align: middle; }
        .title { margin: 0; font-size: 20px; color: #1f6f2e; }
        .subtitle { margin: 2px 0 0 0; font-size: 12px; color: #6b6b6b; }
        .meta { font-size: 11px; color: #666; }
        /* Primary header styles (kept simple for TCPDF compatibility) */
        .header-center {
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
        .trail-info { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 6px; }
        .info-col { flex: 1 1 200px; }
        .info-label { font-weight: 700; color: #333; display:block; font-size:11px; }
        .info-value { display:block; color: #333; margin-top: 2px; }
        .map-container { text-align:center; page-break-inside: avoid; }
        .map-image { width: 100%; max-width: 520px; border-radius:6px; border:1px solid #e2e2e2; }
        .elevation-section { margin-top: 12px; page-break-inside: avoid; }
        .elevation-stats { background:#fbfbfb; padding:8px; border-radius:6px; border:1px solid #f0f0f0; }
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
        .legend { background: #f7f7f8; padding: 8px; border-radius:6px; margin-top:10px; border:1px solid #f0f0f0; }
        .legend h4 {
            margin: 0 0 8px 0;
            font-size: 14px;
        }
        .legend-item {
            margin-bottom: 5px;
        }
        .trail-notes { margin-top: 10px; background: #fff8e5; padding: 10px; border-radius: 6px; border-left: 4px solid #ffcf67; }
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
        .footer { margin-top: 14px; border-top: 1px solid #eaeaea; padding-top: 8px; font-size:10px; color:#666; text-align:center; }
        .footer .left { float:left; font-size:10px; color:#666; }
        .footer .right { float:right; font-size:10px; color:#666; }
        .clear { clear: both; }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="container">
    <!-- Header -->
    <div class="header card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center;">
                @php
                    // Check if the logo file exists in the public path; fall back to text if not.
                    $logoPath = public_path('img/icon1.png');
                    $logoExists = file_exists($logoPath);
                @endphp

                @if($logoExists)
                    <img class="logo" src="{{ asset('img/hikethere-logo.png') }}" alt="HikeThere logo">
                @else
                    <div style="font-weight:700; font-size:18px; color:#1f6f2e;">HikeThere</div>
                @endif

                <div style="display:inline-block; vertical-align: middle; margin-left:8px;">
                    <h1 class="title">{{ $trail->trail_name }}</h1>
                    <div class="subtitle">{{ $trail->mountain_name }} • {{ $trail->location->name ?? 'Location' }}</div>
                </div>
            </div>

            <div style="text-align:right; width:260px;">
                <div class="meta">Generated: {{ date('Y-m-d') }}</div>
                <div style="height:8px"></div>
                @if(!empty($staticMapDataUri) || !empty($staticMapUrl))
                    <img class="map-image" src="{{ $staticMapDataUri ?? $staticMapUrl }}" style="max-width:240px;" alt="Map preview">
                @endif
            </div>
        </div>
    </div>

    <!-- Trail Information -->
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div class="info-col"><span class="info-label">Difficulty</span><span class="info-value"><span class="difficulty-badge difficulty-{{ strtolower($trail->difficulty) }}">{{ $trail->difficulty_label }}</span></span></div>
            </div>
            <div style="text-align:right;">
                <div class="info-col"><span class="info-label">Length</span><span class="info-value">{{ $trail->length ?? 'N/A' }} km</span></div>
                <div class="info-col"><span class="info-label">Estimated Time</span><span class="info-value">{{ $trail->estimated_time_formatted ?? 'N/A' }}</span></div>
            </div>
        </div>
        <div style="display:flex; gap:12px; margin-top:8px;">
            <div class="info-col"><span class="info-label">Elevation Gain</span><span class="info-value">{{ $trail->elevation_gain ?? 'N/A' }} m</span></div>
            <div class="info-col"><span class="info-label">Highest Point</span><span class="info-value">{{ $trail->elevation_high ?? 'N/A' }} m</span></div>
            <div class="info-col"><span class="info-label">Lowest Point</span><span class="info-value">{{ $trail->elevation_low ?? 'N/A' }} m</span></div>
            <div class="info-col"><span class="info-label">Best Season</span><span class="info-value">{{ $trail->best_season ?? 'N/A' }}</span></div>
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
        @php
            // Prefer the embedded base64 image when available (passed as staticMapDataUri by controller)
            $mapSrc = $staticMapDataUri ?? $staticMapUrl ?? '';
        @endphp
        @if($mapSrc)
            <img src="{{ $mapSrc }}" alt="Trail Map for {{ $trail->trail_name }}">
        @else
            <div style="font-size:12px; color:#666;">Map image not available.</div>
        @endif
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

    <!-- GPS Coordinates removed for a cleaner PDF; coordinates remain available in the app -->

    <!-- Footer -->
    <div class="footer">
        Generated on {{ date('Y-m-d H:i:s') }} | HikeThere Trail Map | For hiking purposes only - Always inform others of your hiking plans
    </div>
</body>
</html>
