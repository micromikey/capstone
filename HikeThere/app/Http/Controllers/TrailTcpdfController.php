<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use TCPDF;

class TrailTcpdfController extends Controller
{
    public function downloadMap(Trail $trail)
    {
        // Get Google Maps API key
        $apiKey = config('services.google.maps_api_key');
        
        if (!$apiKey) {
            return back()->with('error', 'Google Maps API key not configured');
        }

        // Get trail coordinates
        $coordinates = $trail->coordinates;
        if (!$coordinates || empty($coordinates)) {
            return back()->with('error', 'Trail coordinates not available. Please generate coordinates first.');
        }

        try {
            // Create PDF using TCPDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator('HikeThere');
            $pdf->SetAuthor('HikeThere');
            $pdf->SetTitle($trail->trail_name . ' - Trail Map');
            $pdf->SetSubject('Trail Map');
            $pdf->SetKeywords('trail, hiking, map, coordinates');

            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            // Set margins
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(TRUE, 15);

            // Add a page
            $pdf->AddPage();

            // Generate static map URL
            $staticMapUrl = $this->generateStaticMapUrl($coordinates, $apiKey);
            
            // Get elevation data
            $elevationData = $this->getElevationData($coordinates, $apiKey);

            // Generate HTML content
            $html = $this->generatePdfHtml($trail, $staticMapUrl, $elevationData, $coordinates);

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Add coordinates page
            $pdf->AddPage();
            $coordinatesHtml = $this->generateCoordinatesHtml($coordinates);
            $pdf->writeHTML($coordinatesHtml, true, false, true, false, '');

            $filename = str_replace(' ', '_', $trail->trail_name) . '_trail_map.pdf';
            
            // Output PDF
            $pdf->Output($filename, 'D');
            
        } catch (\Exception $e) {
            Log::error('TCPDF generation error: ' . $e->getMessage());
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    private function generateStaticMapUrl($coordinates, $apiKey)
    {
        $baseUrl = 'https://maps.googleapis.com/maps/api/staticmap';
        
        // Create path parameter from coordinates
        $pathCoords = [];
        foreach ($coordinates as $coord) {
            $pathCoords[] = $coord['lat'] . ',' . $coord['lng'];
        }
        $path = 'color:0xff0000ff|weight:5|' . implode('|', $pathCoords);
        
        $endCoord = end($coordinates);
        
        // Build URL manually to handle multiple markers
        $url = $baseUrl . '?size=800x600' . 
               '&path=' . urlencode($path) .
               '&markers=' . urlencode('color:green|label:S|' . $coordinates[0]['lat'] . ',' . $coordinates[0]['lng']) .
               '&markers=' . urlencode('color:red|label:E|' . $endCoord['lat'] . ',' . $endCoord['lng']) .
               '&maptype=terrain' .
               '&format=png' .
               '&key=' . $apiKey;

        return $url;
    }

    private function getElevationData($coordinates, $apiKey)
    {
        try {
            // Sample coordinates for elevation (Google has a limit on points)
            $sampleCoords = $this->sampleCoordinates($coordinates, 50);
            
            $locations = [];
            foreach ($sampleCoords as $coord) {
                $locations[] = $coord['lat'] . ',' . $coord['lng'];
            }
            
            $response = Http::get('https://maps.googleapis.com/maps/api/elevation/json', [
                'locations' => implode('|', $locations),
                'key' => $apiKey
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                return $data['results'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Elevation API error: ' . $e->getMessage());
        }
        
        return [];
    }

    private function sampleCoordinates($coordinates, $maxPoints)
    {
        $total = count($coordinates);
        if ($total <= $maxPoints) {
            return $coordinates;
        }
        
        $step = $total / $maxPoints;
        $sampled = [];
        
        for ($i = 0; $i < $total; $i += $step) {
            $sampled[] = $coordinates[intval($i)];
        }
        
        return $sampled;
    }

    private function generatePdfHtml($trail, $staticMapUrl, $elevationData, $coordinates)
    {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .header h1 { margin: 0 0 5px 0; font-size: 20px; color: #2d5016; }
            .header h2 { margin: 0; font-size: 14px; color: #666; }
            .info-table { width: 100%; margin-bottom: 15px; }
            .info-table td { padding: 3px 0; }
            .label { font-weight: bold; width: 30%; }
            .difficulty-badge { padding: 2px 6px; border-radius: 3px; font-weight: bold; text-transform: uppercase; font-size: 9px; }
            .difficulty-easy { background: #d4edda; color: #155724; }
            .difficulty-moderate { background: #fff3cd; color: #856404; }
            .difficulty-difficult { background: #f8d7da; color: #721c24; }
            .map-container { text-align: center; margin: 15px 0; }
            .elevation-stats { background: #f8f9fa; padding: 8px; border-radius: 4px; margin: 10px 0; }
            .stat-grid { display: table; width: 100%; }
            .stat-item { display: table-cell; text-align: center; padding: 5px; }
            .legend { background: #f8f9fa; padding: 8px; border-radius: 4px; margin: 10px 0; }
            .legend-item { margin: 3px 0; }
            .important-notes { background: #fff3cd; padding: 8px; border-radius: 4px; border-left: 4px solid #ffc107; margin: 10px 0; }
            .emergency-info { background: #f8d7da; padding: 8px; border-radius: 4px; border-left: 4px solid #dc3545; margin: 10px 0; }
        </style>
        
        <div class="header">
            <h1>' . htmlspecialchars($trail->trail_name) . '</h1>
            <h2>' . htmlspecialchars($trail->mountain_name) . ' - ' . htmlspecialchars($trail->location->name ?? 'Location') . '</h2>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Difficulty:</td>
                <td><span class="difficulty-badge difficulty-' . strtolower($trail->difficulty) . '">' . ucfirst($trail->difficulty) . '</span></td>
            </tr>
            <tr>
                <td class="label">Length:</td>
                <td>' . ($trail->length ?? 'N/A') . ' km</td>
            </tr>
            <tr>
                <td class="label">Estimated Time:</td>
                <td>' . ($trail->estimated_time_formatted ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td class="label">Elevation Gain:</td>
                <td>' . ($trail->elevation_gain ?? 'N/A') . ' m</td>
            </tr>
            <tr>
                <td class="label">Highest Point:</td>
                <td>' . ($trail->elevation_high ?? 'N/A') . ' m</td>
            </tr>
            <tr>
                <td class="label">Lowest Point:</td>
                <td>' . ($trail->elevation_low ?? 'N/A') . ' m</td>
            </tr>
            <tr>
                <td class="label">Best Season:</td>
                <td>' . ($trail->best_season ?? 'N/A') . '</td>
            </tr>
        </table>';

        if ($trail->summary) {
            $html .= '<h3>Description</h3><p>' . htmlspecialchars($trail->summary) . '</p>';
        }

        $html .= '
        <div class="map-container">
            <h3>Trail Route Map</h3>
            <p><strong>Note:</strong> This map shows the complete trail route with start (green) and end (red) markers.</p>
            <p style="color: #666; font-size: 10px;">Map URL: ' . htmlspecialchars($staticMapUrl) . '</p>
        </div>';

        if (!empty($elevationData)) {
            $elevations = array_column($elevationData, 'elevation');
            $maxElev = !empty($elevations) ? max($elevations) : 0;
            $minElev = !empty($elevations) ? min($elevations) : 0;
            $totalGain = $maxElev - $minElev;

            $html .= '
            <h3>Elevation Profile</h3>
            <div class="elevation-stats">
                <div class="stat-grid">
                    <div class="stat-item"><strong>Max:</strong> ' . round($maxElev) . 'm</div>
                    <div class="stat-item"><strong>Min:</strong> ' . round($minElev) . 'm</div>
                    <div class="stat-item"><strong>Gain:</strong> ' . round($totalGain) . 'm</div>
                    <div class="stat-item"><strong>Points:</strong> ' . count($elevationData) . '</div>
                </div>
            </div>
            <p><strong>Elevation Points:</strong> ';
            
            $elevationPoints = [];
            foreach(array_chunk($elevationData, 10) as $chunk) {
                foreach($chunk as $point) {
                    $elevationPoints[] = round($point['elevation']) . 'm';
                }
            }
            $html .= implode(', ', $elevationPoints);
            $html .= '</p>';
        }

        if ($trail->features && count($trail->features) > 0) {
            $html .= '<h3>Trail Features</h3><ul>';
            foreach($trail->features as $feature) {
                $html .= '<li>' . htmlspecialchars($feature) . '</li>';
            }
            $html .= '</ul>';
        }

        $html .= '
        <div class="legend">
            <h4>Map Legend</h4>
            <div class="legend-item"><strong>Green Marker (S):</strong> Trail Start Point</div>
            <div class="legend-item"><strong>Red Marker (E):</strong> Trail End Point</div>
            <div class="legend-item"><strong>Red Line:</strong> Trail Path/Route</div>
            <div class="legend-item"><strong>Terrain:</strong> Topographical map showing elevation changes</div>
        </div>';

        if ($trail->terrain_notes || $trail->other_trail_notes) {
            $html .= '<div class="important-notes"><h4>⚠️ Important Trail Notes</h4>';
            if ($trail->terrain_notes) {
                $html .= '<p><strong>Terrain:</strong> ' . htmlspecialchars($trail->terrain_notes) . '</p>';
            }
            if ($trail->other_trail_notes) {
                $html .= '<p><strong>Additional Notes:</strong> ' . htmlspecialchars($trail->other_trail_notes) . '</p>';
            }
            $html .= '</div>';
        }

        if ($trail->emergency_contacts) {
            $html .= '<div class="emergency-info"><h4>🚨 Emergency Contacts</h4><p>' . htmlspecialchars($trail->emergency_contacts) . '</p></div>';
        }

        return $html;
    }

    private function generateCoordinatesHtml($coordinates)
    {
        $html = '
        <style>
            .coord-table { width: 100%; border-collapse: collapse; font-size: 10px; }
            .coord-table th, .coord-table td { border: 1px solid #ddd; padding: 4px; text-align: left; }
            .coord-table th { background: #f8f9fa; font-weight: bold; }
        </style>
        
        <h3>GPS Coordinates for Trail Route</h3>
        <p><strong>Use these coordinates for GPS navigation and tracking your progress:</strong></p>
        
        <table class="coord-table">
            <thead>
                <tr>
                    <th>Point #</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>';

        foreach($coordinates as $index => $coord) {
            $notes = '';
            if ($index === 0) {
                $notes = 'Trail Start';
            } elseif ($index === count($coordinates) - 1) {
                $notes = 'Trail End';
            } else {
                $notes = 'Waypoint';
            }

            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . number_format($coord['lat'], 6) . '</td>
                <td>' . number_format($coord['lng'], 6) . '</td>
                <td>' . $notes . '</td>
            </tr>';
        }

        $html .= '</tbody></table>
        
        <div style="margin-top: 15px;">
            <h4>Using GPS Coordinates:</h4>
            <ol>
                <li>Load these coordinates into your GPS device or smartphone app</li>
                <li>Follow the waypoints in sequence from Start to End</li>
                <li>Use the coordinates to track your current position on the trail</li>
                <li>If you get lost, find the nearest waypoint to get back on track</li>
            </ol>
        </div>
        
        <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
            Generated on ' . date('Y-m-d H:i:s') . ' | HikeThere Trail Map | For hiking purposes only - Always inform others of your hiking plans
        </div>';

        return $html;
    }
}
