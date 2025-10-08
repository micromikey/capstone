<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #10b981;
        }
        .header h1 {
            color: #059669;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0;
        }
        .meta-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .meta-info p {
            margin: 5px 0;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary h2 {
            color: #059669;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #d1d5db;
            padding-bottom: 5px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            background-color: #ecfdf5;
            border: 1px solid #d1fae5;
            text-align: center;
        }
        .summary-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #10b981;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        table tr:hover {
            background-color: #f3f4f6;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #d1d5db;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
            font-style: italic;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>HikeThere - Trail Management System</p>
    </div>

    <!-- Meta Information -->
    <div class="meta-info">
        <p><strong>Report Period:</strong> {{ $period }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F d, Y h:i A') }}</p>
        @if(isset($trail_name))
        <p><strong>Trail:</strong> {{ $trail_name }}</p>
        @endif
        @if(isset($organization_name))
        <p><strong>Organization:</strong> {{ $organization_name }}</p>
        @endif
    </div>

    <!-- Summary Section -->
    @if(isset($summary) && !empty($summary))
    <div class="summary">
        <h2>Summary Statistics</h2>
        <div class="summary-grid">
            @php
                $summaryItems = [];
                foreach($summary as $key => $value) {
                    if (!is_array($value) && !is_object($value)) {
                        $summaryItems[] = ['key' => $key, 'value' => $value];
                    }
                }
                $chunks = array_chunk($summaryItems, 4);
            @endphp
            
            @foreach($chunks as $chunkIndex => $chunk)
                @if($chunkIndex > 0)
                    </div><div class="summary-grid">
                @endif
                @foreach($chunk as $item)
                    <div class="summary-item">
                        <div class="summary-label">{{ str_replace('_', ' ', $item['key']) }}</div>
                        <div class="summary-value">{{ $item['value'] }}</div>
                    </div>
                @endforeach
                @php
                    $remaining = 4 - count($chunk);
                    for ($i = 0; $i < $remaining; $i++) {
                        echo '<div class="summary-item" style="visibility: hidden;"></div>';
                    }
                @endphp
            @endforeach
        </div>
    </div>
    @endif

    <!-- Detailed Data Table -->
    <div class="details">
        <h2 style="color: #059669; font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #d1d5db; padding-bottom: 5px;">Detailed Data</h2>
        
        @if(isset($data) && count($data) > 0)
            <table>
                <thead>
                    <tr>
                        @foreach(array_keys($data[0]) as $header)
                            <th>{{ str_replace('_', ' ', $header) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            @foreach($row as $value)
                                <td>{{ $value ?? 'N/A' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>No data available for the selected period and filters.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report is confidential and intended for authorized personnel only.</p>
        <p>Generated by HikeThere Report System &copy; {{ date('Y') }}</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
