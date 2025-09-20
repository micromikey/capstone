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

            // Set margins and page break
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);

            // Add a page
            $pdf->AddPage();

            // Generate static map URL
            $staticMapUrl = $this->generateStaticMapUrl($coordinates, $apiKey);

            // Try to fetch the static map image and convert to base64 so it can be embedded in the PDF
            $staticMapDataUri = null;
            try {
                $mapResp = Http::get($staticMapUrl);
                if ($mapResp->successful()) {
                    $imageBinary = $mapResp->body();
                    $base64 = base64_encode($imageBinary);
                    $staticMapDataUri = 'data:image/png;base64,' . $base64;
                } else {
                    Log::warning('Static map fetch failed: HTTP ' . $mapResp->status());
                }
            } catch (\Exception $e) {
                Log::error('Static map fetch error: ' . $e->getMessage());
            }

            // Get elevation data
            $elevationData = $this->getElevationData($coordinates, $apiKey);

            // Render HTML using the Blade view (existing: resources/views/trails/pdf-map.blade.php)
            // Pass the data; Blade uses `staticMapUrl` for the <img> src so prefer the data URI when available
            $html = view('trails.pdf-map', [
                'trail' => $trail,
                'staticMapUrl' => $staticMapDataUri ?? $staticMapUrl,
                'staticMapDataUri' => $staticMapDataUri,
                'elevationData' => $elevationData,
                'coordinates' => $coordinates,
            ])->render();

            // Write HTML to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            $filename = str_replace(' ', '_', $trail->trail_name) . '_trail_map.pdf';

            // Capture PDF output as string and return as response
            $pdfContent = $pdf->Output($filename, 'S');

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

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
                'key' => $apiKey,
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

        $step = max(1, floor($total / $maxPoints));
        $sampled = [];

        for ($i = 0; $i < $total; $i += $step) {
            $sampled[] = $coordinates[intval($i)];
        }

        return $sampled;
    }

}
