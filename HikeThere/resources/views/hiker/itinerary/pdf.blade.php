<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Itinerary PDF</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 30px;
            line-height: 1.6;
        }
        h1 {
            color: #2d3748;
        }
        .section {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Mt. Pulag Itinerary' }}</h1>

    <div class="section">
        <strong>Date:</strong> {{ $date ?? 'N/A' }}<br>
        <strong>Time:</strong> {{ $time ?? 'N/A' }}<br>
        <strong>Trail:</strong> {{ $trail ?? 'N/A' }}<br>
        <strong>Transportation:</strong> {{ $transportation ?? 'N/A' }}
    </div>

    <div class="section">
        <h3>Route</h3>
        <p><strong>From:</strong> {{ $departure }}</p>
        <p><strong>To:</strong> {{ $arrival }}</p>
    </div>

    <div class="section">
        <h3>Stopovers</h3>
        @if (!empty($stopovers))
            <ul>
                @foreach ($stopovers as $stop)
                    <li>{{ $stop }}</li>
                @endforeach
            </ul>
        @else
            <p>No stopovers listed.</p>
        @endif
    </div>

    <div class="section">
        <h3>Side Trips</h3>
        @if (!empty($sidetrips))
            <ul>
                @foreach ($sidetrips as $trip)
                    <li>{{ $trip }}</li>
                @endforeach
            </ul>
        @else
            <p>No side trips listed.</p>
        @endif
    </div>
</body>
</html>
