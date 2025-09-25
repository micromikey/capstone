<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GPXService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class GPXController extends Controller
{
    protected $gpxService;

    public function __construct(GPXService $gpxService)
    {
        $this->gpxService = $gpxService;
    }

    /**
     * Process uploaded GPX file and return trail data
     */
    public function processGPX(Request $request): JsonResponse
    {
        try {
            $request->validate([
                // Accept GPX/KML/KMZ and xml variations (clients may send xml mime)
                'gpx_file' => ['required','file','max:10240', function($attribute, $value, $fail) {
                    $allowedExt = ['gpx','kml','kmz','xml'];
                    $extension = strtolower($value->getClientOriginalExtension() ?: '');
                    if (!in_array($extension, $allowedExt)) {
                        return $fail('The '.$attribute.' must be a file of type: gpx, kml, kmz.');
                    }
                    $mime = $value->getMimeType();
                    $allowedMimes = ['application/gpx+xml','application/gpx','application/xml','text/xml','application/vnd.google-earth.kml+xml','application/octet-stream'];
                    if ($mime && !in_array($mime, $allowedMimes)) {
                        if (!in_array($extension, ['gpx','kml','kmz'])) {
                            return $fail('The '.$attribute.' must be a GPX/KML/KMZ file.');
                        }
                    }
                }], // 10MB max
            ]);

            $gpxData = $this->gpxService->processGPXUpload($request->file('gpx_file'));
            
            return response()->json([
                'success' => true,
                'message' => 'GPX file processed successfully',
                'data' => $gpxData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing GPX file: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate GPX file from coordinates
     */
    public function generateGPX(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'coordinates' => 'required|array|min:2',
                'coordinates.*.lat' => 'required|numeric',
                'coordinates.*.lng' => 'required|numeric',
                'coordinates.*.elevation' => 'nullable|numeric',
                'trail_name' => 'nullable|string|max:255',
                'description' => 'nullable|string'
            ]);

            $coordinates = $request->coordinates;
            $trailName = $request->trail_name ?? 'Trail';
            $description = $request->description ?? '';

            $gpxContent = $this->gpxService->generateGPXContent($coordinates, $trailName, $description);
            
            return response()->json([
                'success' => true,
                'message' => 'GPX file generated successfully',
                'gpx_content' => $gpxContent,
                'filename' => Str::slug($trailName) . '.gpx'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating GPX file: ' . $e->getMessage()
            ], 400);
        }
    }
}
