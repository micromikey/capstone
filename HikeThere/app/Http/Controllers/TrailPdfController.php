<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrailPdfController extends Controller
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

        // Generate static map URL with trail path
        $staticMapUrl = $this->generateStaticMapUrl($coordinates, $apiKey);
        
        // Get elevation data
        $elevationData = $this->getElevationData($coordinates, $apiKey);
        
        try {
            // Generate PDF
            $pdf = Pdf::loadView('trails.pdf-map', [
                'trail' => $trail,
                'staticMapUrl' => $staticMapUrl,
                'elevationData' => $elevationData,
                'coordinates' => $coordinates
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => false,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);
            
            $filename = str_replace(' ', '_', $trail->trail_name) . '_trail_map.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage());
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
        
        // Get bounds for the map
        $bounds = $this->calculateBounds($coordinates);
        
        $params = [
            'size' => '800x600',
            'path' => $path,
            'markers' => 'color:green|label:S|' . $coordinates[0]['lat'] . ',' . $coordinates[0]['lng'],
            'maptype' => 'terrain',
            'format' => 'png',
            'key' => $apiKey
        ];

        // Add end marker separately to avoid conflict
        $endCoord = end($coordinates);
        $params['markers'] = [
            'color:green|label:S|' . $coordinates[0]['lat'] . ',' . $coordinates[0]['lng'],
            'color:red|label:E|' . $endCoord['lat'] . ',' . $endCoord['lng']
        ];

        // Build URL manually to handle multiple markers
        $url = $baseUrl . '?size=' . $params['size'] . 
               '&path=' . urlencode($path) .
               '&markers=' . urlencode('color:green|label:S|' . $coordinates[0]['lat'] . ',' . $coordinates[0]['lng']) .
               '&markers=' . urlencode('color:red|label:E|' . $endCoord['lat'] . ',' . $endCoord['lng']) .
               '&maptype=' . $params['maptype'] .
               '&format=' . $params['format'] .
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

    private function calculateBounds($coordinates)
    {
        $lats = array_column($coordinates, 'lat');
        $lngs = array_column($coordinates, 'lng');
        
        return [
            'north' => max($lats),
            'south' => min($lats),
            'east' => max($lngs),
            'west' => min($lngs)
        ];
    }

    public function getElevationProfile(Trail $trail)
    {
        $apiKey = config('services.google.maps_api_key');
        
        if (!$apiKey || !$trail->coordinates) {
            return response()->json(['error' => 'Data not available'], 400);
        }
        
        $elevationData = $this->getElevationData($trail->coordinates, $apiKey);
        
        return response()->json([
            'elevations' => $elevationData,
            'trail_length' => $trail->length
        ]);
    }
}
