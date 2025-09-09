<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GPXService
{
    /**
     * Parse GPX file content and extract trail coordinates
     */
    public function parseGPXContent($gpxContent)
    {
        try {
            // Clean the content
            $gpxContent = trim($gpxContent);
            
            // Parse XML
            $xml = simplexml_load_string($gpxContent);
            if ($xml === false) {
                throw new \Exception('Invalid XML format');
            }

            $coordinates = [];
            $elevationGain = 0;
            $totalDistance = 0;
            $minElevation = null;
            $maxElevation = null;
            $lastElevation = null;
            $lastCoord = null;

            // Try to find track points first (most common in GPS tracks)
            $trackPoints = $xml->xpath('//trkpt');
            
            if (empty($trackPoints)) {
                // If no track points, try route points
                $trackPoints = $xml->xpath('//rtept');
            }
            
            if (empty($trackPoints)) {
                // If no route points, try waypoints
                $trackPoints = $xml->xpath('//wpt');
            }

            foreach ($trackPoints as $point) {
                $lat = (float) $point['lat'];
                $lng = (float) $point['lon'];
                
                if ($lat === 0.0 || $lng === 0.0) {
                    continue; // Skip invalid coordinates
                }

                $coord = [
                    'lat' => $lat,
                    'lng' => $lng
                ];

                // Extract elevation if available
                if (isset($point->ele)) {
                    $elevation = (float) $point->ele;
                    $coord['elevation'] = $elevation;

                    // Calculate elevation statistics
                    if ($minElevation === null || $elevation < $minElevation) {
                        $minElevation = $elevation;
                    }
                    if ($maxElevation === null || $elevation > $maxElevation) {
                        $maxElevation = $elevation;
                    }

                    // Calculate elevation gain
                    if ($lastElevation !== null && $elevation > $lastElevation) {
                        $elevationGain += $elevation - $lastElevation;
                    }
                    $lastElevation = $elevation;
                }

                // Calculate distance
                if ($lastCoord !== null) {
                    $totalDistance += $this->calculateDistance(
                        $lastCoord['lat'], 
                        $lastCoord['lng'], 
                        $lat, 
                        $lng
                    );
                }

                $coordinates[] = $coord;
                $lastCoord = $coord;
            }

            if (empty($coordinates)) {
                throw new \Exception('No valid coordinates found in GPX file');
            }

            // Return parsed data
            return [
                'coordinates' => $coordinates,
                'total_distance_km' => round($totalDistance / 1000, 2),
                'elevation_gain_m' => round($elevationGain),
                'min_elevation_m' => $minElevation ? round($minElevation) : null,
                'max_elevation_m' => $maxElevation ? round($maxElevation) : null,
                'total_points' => count($coordinates),
                'bounds' => $this->calculateBounds($coordinates)
            ];

        } catch (\Exception $e) {
            Log::error('GPX parsing error: ' . $e->getMessage());
            throw new \Exception('Error parsing GPX file: ' . $e->getMessage());
        }
    }

    /**
     * Validate uploaded GPX file
     */
    public function validateGPXFile($file)
    {
        // Check file extension
        $allowedExtensions = ['gpx', 'kml', 'kmz'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Invalid file format. Only GPX, KML, and KMZ files are supported.');
        }

        // Check file size (max 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw new \Exception('File too large. Maximum size is 10MB.');
        }

        // Check if file is readable
        if (!$file->isValid()) {
            throw new \Exception('Invalid or corrupted file.');
        }

        return true;
    }

    /**
     * Process uploaded GPX file and return trail data
     */
    public function processGPXUpload($file)
    {
        $this->validateGPXFile($file);

        $content = file_get_contents($file->getRealPath());
        
        // Handle KMZ files (ZIP containing KML)
        if (strtolower($file->getClientOriginalExtension()) === 'kmz') {
            $content = $this->extractKMLFromKMZ($file->getRealPath());
        }

        // Convert KML to GPX-like format for parsing
        if (strtolower($file->getClientOriginalExtension()) === 'kml') {
            $content = $this->convertKMLToGPXFormat($content);
        }

        return $this->parseGPXContent($content);
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta/2) * sin($lngDelta/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Calculate bounding box for coordinates
     */
    private function calculateBounds($coordinates)
    {
        if (empty($coordinates)) {
            return null;
        }

        $minLat = $maxLat = $coordinates[0]['lat'];
        $minLng = $maxLng = $coordinates[0]['lng'];

        foreach ($coordinates as $coord) {
            $minLat = min($minLat, $coord['lat']);
            $maxLat = max($maxLat, $coord['lat']);
            $minLng = min($minLng, $coord['lng']);
            $maxLng = max($maxLng, $coord['lng']);
        }

        return [
            'north' => $maxLat,
            'south' => $minLat,
            'east' => $maxLng,
            'west' => $minLng
        ];
    }

    /**
     * Extract KML from KMZ file
     */
    private function extractKMLFromKMZ($kmzPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($kmzPath) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'kml') {
                    $content = $zip->getFromIndex($i);
                    $zip->close();
                    return $content;
                }
            }
            $zip->close();
        }
        throw new \Exception('No KML file found in KMZ archive');
    }

    /**
     * Convert KML format to GPX-like format for unified parsing
     */
    private function convertKMLToGPXFormat($kmlContent)
    {
        // This is a basic conversion - you might want to use a more robust KML parser
        try {
            $xml = simplexml_load_string($kmlContent);
            if ($xml === false) {
                throw new \Exception('Invalid KML format');
            }

            // Register KML namespace
            $xml->registerXPathNamespace('kml', 'http://www.opengis.net/kml/2.2');

            $gpxContent = '<?xml version="1.0" encoding="UTF-8"?><gpx><trk><trkseg>';

            // Extract coordinates from KML
            $coordinates = $xml->xpath('//kml:coordinates');
            
            if (!empty($coordinates)) {
                $coordString = (string) $coordinates[0];
                $coordPairs = preg_split('/\s+/', trim($coordString));
                
                foreach ($coordPairs as $pair) {
                    $coords = explode(',', $pair);
                    if (count($coords) >= 2) {
                        $lng = $coords[0];
                        $lat = $coords[1];
                        $ele = isset($coords[2]) ? $coords[2] : '';
                        
                        $gpxContent .= "<trkpt lat=\"{$lat}\" lon=\"{$lng}\">";
                        if ($ele) {
                            $gpxContent .= "<ele>{$ele}</ele>";
                        }
                        $gpxContent .= "</trkpt>";
                    }
                }
            }

            $gpxContent .= '</trkseg></trk></gpx>';
            return $gpxContent;

        } catch (\Exception $e) {
            throw new \Exception('Error converting KML: ' . $e->getMessage());
        }
    }

    /**
     * Generate GPX file content from coordinates
     */
    public function generateGPXContent($coordinates, $trailName = 'Trail', $description = '')
    {
        $gpxContent = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $gpxContent .= '<gpx version="1.1" creator="HikeThere" xmlns="http://www.topografix.com/GPX/1/1">' . "\n";
        $gpxContent .= '  <metadata>' . "\n";
        $gpxContent .= '    <name>' . htmlspecialchars($trailName) . '</name>' . "\n";
        if ($description) {
            $gpxContent .= '    <desc>' . htmlspecialchars($description) . '</desc>' . "\n";
        }
        $gpxContent .= '    <time>' . date('c') . '</time>' . "\n";
        $gpxContent .= '  </metadata>' . "\n";
        $gpxContent .= '  <trk>' . "\n";
        $gpxContent .= '    <name>' . htmlspecialchars($trailName) . '</name>' . "\n";
        $gpxContent .= '    <trkseg>' . "\n";

        foreach ($coordinates as $coord) {
            $gpxContent .= '      <trkpt lat="' . $coord['lat'] . '" lon="' . $coord['lng'] . '">' . "\n";
            if (isset($coord['elevation'])) {
                $gpxContent .= '        <ele>' . $coord['elevation'] . '</ele>' . "\n";
            }
            $gpxContent .= '      </trkpt>' . "\n";
        }

        $gpxContent .= '    </trkseg>' . "\n";
        $gpxContent .= '  </trk>' . "\n";
        $gpxContent .= '</gpx>';

        return $gpxContent;
    }
}
