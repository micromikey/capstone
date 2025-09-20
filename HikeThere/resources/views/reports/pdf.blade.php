PDF TEMPLATE


{{-- resources/views/reports/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportData['title'] ?? 'Report' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #065f46;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #065f46;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #065f46;
        }
        .summary h3 {
            margin-top: 0;
            color: #065f46;
            font-size: 16px;
        }
        .summary-item {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
        }
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        .summary-value {
            color: #666;
        }
        .data-section {
            margin: 30px 0;
        }
        .data-section h3 {
            color: #065f46;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-size: 16px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        .data-table th {
            background-color: #065f46;
            color: white;
            font-weight: bold;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $reportData['title'] ?? 'Platform Report' }}</h1>
        <p><strong>Period:</strong> {{ $reportData['period'] ?? 'N/A' }}</p>
        <p><strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if(isset($reportData['summary']) && !empty($reportData['summary']))
    <div class="summary">
        <h3>Executive Summary</h3>
        @foreach($reportData['summary'] as $key => $value)
            <div class="summary-item">
                <span class="summary-label">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                <span class="summary-value">
                    @if(is_array($value))
                        {{ json_encode($value) }}
                    @else
                        {{ $value }}
                    @endif
                </span>
            </div>
        @endforeach
    </div>
    @endif

    @if(isset($reportData['data']) && !empty($reportData['data']))
    <div class="data-section">
        <h3>Detailed Data Analysis</h3>
        
        @if(count($reportData['data']) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        @foreach(array_keys((array)$reportData['data'][0]) as $header)
                            <th>{{ ucwords(str_replace('_', ' ', $header)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['data'] as $index => $row)
                        @if($index > 0 && $index % 25 == 0)
                            </tbody></table>
                            <div class="page-break"></div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        @foreach(array_keys((array)$reportData['data'][0]) as $header)
                                            <th>{{ ucwords(str_replace('_', ' ', $header)) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                        @endif
                        <tr>
                            @foreach((array)$row as $value)
                                <td>
                                    @if(is_array($value))
                                        {{ json_encode($value) }}
                                    @elseif(strlen($value) > 50)
                                        {{ substr($value, 0, 47) }}...
                                    @else
                                        {{ $value }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <p style="margin-top: 15px; font-size: 11px; color: #666;">
                <strong>Total Records:</strong> {{ count($reportData['data']) }}
            </p>
        @else
            <div class="no-data">
                No data available for the selected criteria and time period.
            </div>
        @endif
    </div>
    @else
    <div class="no-data">
        No detailed data available for this report type.
    </div>
    @endif

    <div class="footer">
        <p>Hiking Platform Report System - Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>