<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
// Avoid hard dependency on the Image facade to keep static analysis happy; check class existence before using

class TrailPdfController extends Controller
{
    public function downloadMap(Trail $trail)
    {
        $apiKey = config('services.google.maps_api_key');
        if (!$apiKey) {
            return back()->with('error', 'Google Maps API key not configured');
        }

        $coordinates = $trail->coordinates;
        if (!$coordinates || empty($coordinates)) {
            return back()->with('error', 'Trail coordinates not available. Please generate coordinates first.');
        }

        $staticMapUrl = $this->generateStaticMapUrl($coordinates, $apiKey);
        $localMapUrl = $this->fetchAndCacheMap($staticMapUrl, $trail->slug ?? str_replace(' ', '_', $trail->trail_name));

        $elevationData = $this->getElevationData($coordinates, $apiKey);

        try {
            $pdf = Pdf::loadView('trails.pdf-map', [
                'trail' => $trail,
                'staticMapUrl' => $localMapUrl ?? $staticMapUrl,
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

    /**
     * Display a print-friendly HTML page for the trail map so users can use the browser's Print dialog (Ctrl+P).
     */
    public function printMap(Trail $trail)
    {
        $apiKey = config('services.google.maps_api_key');
        if (!$apiKey) {
            return back()->with('error', 'Google Maps API key not configured');
        }

        $coordinates = $trail->coordinates;
        if (!$coordinates || empty($coordinates)) {
            return back()->with('error', 'Trail coordinates not available. Please generate coordinates first.');
        }

        $staticMapUrl = $this->generateStaticMapUrl($coordinates, $apiKey);
        $localMapUrl = $this->fetchAndCacheMap($staticMapUrl, $trail->slug ?? str_replace(' ', '_', $trail->trail_name));

        $elevationData = $this->getElevationData($coordinates, $apiKey);

        // Resolve up to two trail photos by image id (or path) and prepare final src strings
        $photoSrcs = [];
        $max = 2;

        if (!empty($trail->images) && count($trail->images)) {
            foreach ($trail->images as $img) {
                if (count($photoSrcs) >= $max) break;
                $path = null;

                // numeric id -> resolve TrailImage record
                if (is_numeric($img)) {
                    $ti = \App\Models\TrailImage::find(intval($img));
                    if ($ti) $path = $ti->image_path;

                } elseif (is_array($img) && isset($img['path'])) {
                    $path = $img['path'];

                } elseif (is_object($img)) {
                    $imgId = $img->id ?? null;
                    if ($imgId) {
                        $ti = \App\Models\TrailImage::find($imgId);
                        if ($ti) $path = $ti->image_path;
                    }
                    if (!$path) {
                        $path = $img->image_path ?? $img->path ?? null;
                    }

                } elseif (is_string($img)) {
                    $path = $img;
                }

                if ($path) {
                    // Absolute URL
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        $photoSrcs[] = $path;
                        continue;
                    }

                    // Normalize storage-relative path
                    $rel = preg_replace('#^.*/storage/#', '', $path);
                    $rel = ltrim($rel, '/');

                    try {
                        $disk = \Illuminate\Support\Facades\Storage::disk('public');
                        if ($disk->exists($rel)) {
                            // Use filesystem path and finfo to detect MIME type reliably without relying on disk driver methods
                            $fullPath = $disk->path($rel);
                            $contents = @file_get_contents($fullPath);
                            $mime = 'image/jpeg';
                            if ($contents && file_exists($fullPath)) {
                                $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                                if ($finfo) {
                                    $detected = @finfo_file($finfo, $fullPath);
                                    if ($detected) $mime = $detected;
                                    @finfo_close($finfo);
                                }
                            }
                            $photoSrcs[] = 'data:' . $mime . ';base64,' . base64_encode((string)$contents);
                        } else {
                            // fallback to public URL via Storage facade
                            $photoSrcs[] = \Illuminate\Support\Facades\Storage::url($rel);
                        }
                    } catch (\Exception $e) {
                        $photoSrcs[] = asset('storage/' . $rel);
                    }
                }
            }
        }

        // Fallback: scan storage folders for candidate images
        if (count($photoSrcs) < $max) {
            try {
                $disk = \Illuminate\Support\Facades\Storage::disk('public');
                $prim = $disk->files('trail-images/primary');
                $add = $disk->files('trail-images/additional');
                $candidates = array_merge($prim ?? [], $add ?? []);
            } catch (\Exception $e) {
                $candidates = [];
            }

            foreach ($candidates as $c) {
                if (count($photoSrcs) >= $max) break;
                try {
                    $fullPath = $disk->path($c);
                    $contents = @file_get_contents($fullPath);
                    $mime = 'image/jpeg';
                    if ($contents && file_exists($fullPath)) {
                        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
                        if ($finfo) {
                            $detected = @finfo_file($finfo, $fullPath);
                            if ($detected) $mime = $detected;
                            @finfo_close($finfo);
                        }
                    }
                    $photoSrcs[] = 'data:' . $mime . ';base64,' . base64_encode((string)$contents);
                } catch (\Exception $e) {
                    $photoSrcs[] = \Illuminate\Support\Facades\Storage::url($c);
                }
            }
        }

        return view('trails.print-map', [
            'trail' => $trail,
            'staticMapUrl' => $localMapUrl ?? $staticMapUrl,
            'elevationData' => $elevationData,
            'coordinates' => $coordinates,
            'photoSrcs' => $photoSrcs,
        ]);
    }

    private function generateStaticMapUrl($coordinates, $apiKey)
    {
        $baseUrl = 'https://maps.googleapis.com/maps/api/staticmap';

        $pathCoords = [];
        foreach ($coordinates as $coord) {
            $pathCoords[] = $coord['lat'] . ',' . $coord['lng'];
        }
        $path = 'color:0xff0000ff|weight:5|' . implode('|', $pathCoords);

        // Request a wider map to improve PDF rendering (wider images display larger)
        $size = '1200x600';

        $endCoord = end($coordinates);

        $url = $baseUrl . '?size=' . $size .
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

    /**
     * Fetch a remote static map image and cache it locally for reliable print/PDF rendering.
     * Returns a local URL (public storage) on success or null on failure.
     */
    private function fetchAndCacheMap(string $url, string $slug)
    {
        try {
            $response = Http::get($url);
            if (!$response->successful()) {
                return null;
            }

            $contentType = $response->header('Content-Type', 'image/png');
            $ext = 'png';
            if (str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg')) {
                $ext = 'jpg';
            }

            $dir = 'public/pdf_maps';
            $filename = $slug . '.' . $ext;
            $path = $dir . '/' . $filename;

            // Save raw bytes
            Storage::put($path, $response->body());

            // Optionally resize if Intervention Image is available
            if (class_exists(\Intervention\Image\ImageManagerStatic::class)) {
                try {
                    $fullPath = Storage::path($path);
                    $img = \Intervention\Image\ImageManagerStatic::make($fullPath)->resize(1200, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->encode($ext, 85);
                    file_put_contents($fullPath, (string) $img);
                } catch (\Exception $e) {
                    Log::warning('Image resize failed: ' . $e->getMessage());
                }
            }

            return Storage::url($path);
        } catch (\Exception $e) {
            Log::warning('Failed to fetch static map: ' . $e->getMessage());
            return null;
        }
    }
}