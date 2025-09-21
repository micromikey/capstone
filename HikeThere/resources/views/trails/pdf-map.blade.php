<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $trail->trail_name }} - Trail Map</title>
    <style>
        /* Enhanced single-page PDF layout */
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            margin: 10px;
            font-size: 12px;
            color: #2d3748;
            line-height: 1.4;
            background: #ffffff;
        }

        /* Header with better visual hierarchy */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #2d7a3d;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .site-name {
            font-size: 18px;
            font-weight: 700;
            color: #2d7a3d;
            letter-spacing: 0.5px;
        }

        .meta {
            font-size: 10px;
            color: #718096;
            text-align: right;
        }

        /* Trail title section */
        .title-section {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid #2d7a3d;
        }

        h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #1a202c;
        }

        .subtitle {
            font-size: 13px;
            color: #4a5568;
            margin: 0;
            font-weight: 500;
        }

        /* Content sections */
        .section {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 4px;
            padding-bottom: 1px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Description styling */
        .description {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            border-left: 3px solid #4299e1;
            font-size: 11px;
            line-height: 1.5;
        }

        /* Enhanced map container */
        .map-container {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin: 6px 0;
            text-align: center;
        }

        .map {
            display: block;
            width: 100%;
            height: auto;
            max-height: 400px;
            max-width: 100%;
            border: 0;
        }

        .map-unavailable {
            background: #f7fafc;
            color: #718096;
            text-align: center;
            padding: 30px 20px;
            font-style: italic;
            border: 2px dashed #cbd5e0;
            border-radius: 4px;
            font-size: 11px;
        }

        /* Enhanced metrics table */
        .metrics-container {
            background: #f8f9fa;
            border-radius: 4px;
            padding: 8px;
            margin: 8px 0;
        }

        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .metrics-table td {
            vertical-align: middle;
            padding: 8px 6px;
            text-align: center;
        }

        .metrics-label {
            font-weight: 700;
            font-size: 11px;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metrics-value {
            font-size: 14px;
            font-weight: 600;
            color: #1a202c;
            margin-top: 2px;
        }

        /* Trail information grid */
        .trail-info {
            display: table;
            width: 100%;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
        }

        .info-item {
            display: table-cell;
            padding: 0 10px;
            vertical-align: middle;
        }
        
        .info-item:first-child {
            padding-left: 0;
        }

        .info-label {
            font-weight: 600;
            font-size: 11px;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-value {
            font-size: 12px;
            color: #2d3748;
            font-weight: 500;
        }

        /* Enhanced difficulty badges */
        .difficulty {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .difficulty-easy {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .difficulty-moderate {
            background: #fef5e7;
            color: #744210;
            border: 1px solid #f6e05e;
        }

        .difficulty-hard {
            background: #fed7d7;
            color: #742a2a;
            border: 1px solid #fc8181;
        }

        .difficulty-expert {
            background: #e9d5ff;
            color: #553c9a;
            border: 1px solid #b794f6;
        }

        /* Enhanced footer */
        .footer {
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        .safety-note {
            font-size: 11px;
            color: #e53e3e;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .powered-by {
            font-size: 9px;
            color: #a0aec0;
        }

        /* Quick stats highlight */
        .quick-stats {
            display: table;
            width: 100%;
            background: #2d7a3d;
            color: white;
            border-radius: 4px;
            margin: 8px 0;
            overflow: hidden;
        }

        .quick-stat {
            display: table-cell;
            text-align: center;
            padding: 10px 6px;
            vertical-align: middle;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        
        .quick-stat:last-child {
            border-right: none;
        }

        .quick-stat-value {
            font-size: 16px;
            font-weight: 700;
            display: block;
        }

        .quick-stat-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="site-name">HikeThere</div>
        <div class="meta">
            Generated {{ date('M d, Y') }}<br>
            <strong>Trail Map & Guide</strong>
        </div>
    </div>

    <div class="title-section">
        <h1>{{ $trail->trail_name }}</h1>
        <div class="subtitle">
            {{ $trail->mountain_name }}
            @if(!empty($trail->location) && !empty($trail->location->address))
                • {{ $trail->location->address }}
            @elseif(!empty($trail->location->name))
                • {{ $trail->location->name }}
            @endif
        </div>
    </div>

    <!-- Quick Stats Highlight -->
    <div class="quick-stats">
        <div class="quick-stat">
            <span class="quick-stat-value">{{ $trail->length ?? 'N/A' }}</span>
            <span class="quick-stat-label">Kilometers</span>
        </div>
        <div class="quick-stat">
            <span class="quick-stat-value">{{ $trail->elevation_gain ?? 'N/A' }}</span>
            <span class="quick-stat-label">Meters Gain</span>
        </div>
        <div class="quick-stat">
            <span class="quick-stat-value">{{ $trail->estimated_time_formatted ?? ($trail->duration ?? 'N/A') }}</span>
            <span class="quick-stat-label">Duration</span>
        </div>
    </div>

    @if($trail->summary && trim($trail->summary))
    <div class="section">
        <div class="section-title">Trail Description</div>
        <div class="description">{!! nl2br(e($trail->summary)) !!}</div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Trail Route Map</div>
        @php 
            $mapSrc = null;
            // Check for different possible map sources
            if (!empty($staticMapDataUri)) {
                $mapSrc = $staticMapDataUri;
            } elseif (!empty($staticMapUrl)) {
                $mapSrc = $staticMapUrl;
            }
        @endphp
        
        @if($mapSrc)
        <div class="map-container">
            <img src="{{ $mapSrc }}" alt="Trail Map for {{ $trail->trail_name }}" class="map" style="max-width: 100%; height: auto;">
        </div>
        @else
        <div class="map-unavailable">
            Trail map image not available at this time<br>
            <small>Map may be loading or coordinates unavailable</small>
        </div>
        @endif
    </div>

    <!-- Detailed Metrics -->
    <div class="section">
        <div class="section-title">Trail Metrics</div>
        <div class="metrics-container">
            <table class="metrics-table" role="table" aria-label="Detailed trail metrics">
                <tr>
                    <td class="metrics-label">Total Distance</td>
                    <td class="metrics-label">Elevation Gain</td>
                    <td class="metrics-label">Trail Duration</td>
                    <td class="metrics-label">Est. Hiking Time</td>
                </tr>
                <tr>
                    <td class="metrics-value">{{ $trail->length ?? 'N/A' }} km</td>
                    <td class="metrics-value">{{ $trail->elevation_gain ?? 'N/A' }} m</td>
                    <td class="metrics-value">{{ $trail->duration ?? 'N/A' }}</td>
                    <td class="metrics-value">{{ $trail->estimated_time_formatted ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Trail Information</div>
        <div class="trail-info">
            <div class="info-item">
                <span class="info-label">Difficulty Level:</span>
                <span class="difficulty difficulty-{{ strtolower($trail->difficulty) }}">
                    {{ $trail->difficulty_label ?? $trail->difficulty ?? 'Not Specified' }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Best Hiking Season:</span>
                <span class="info-value">{{ $trail->best_season ?? 'Year-round (verify conditions)' }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="safety-note">Always check weather conditions and trail status before hiking</div>
        <div class="powered-by">
            Plan safe, hike responsibly • Generated by HikeThere Trail System
        </div>
    </div>
</body>

</html>