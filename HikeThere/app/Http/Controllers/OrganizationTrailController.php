<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\TrailImage;
use App\Models\Location;
use App\Services\GoogleDirectionsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Jobs\EnrichTrailData;

class OrganizationTrailController extends Controller
{
    public function index()
    {
        $trails = Trail::where('user_id', Auth::id())
            ->with(['location'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('org.trails.index', compact('trails'));
    }

    public function create()
    {
        // Debug: Log user information
    Log::info('Trail creation page accessed', [
            'user_id' => Auth::id(),
            'user_type' => Auth::user()->user_type ?? 'unknown',
            'approval_status' => Auth::user()->approval_status ?? 'unknown',
            'is_authenticated' => Auth::check()
        ]);

        return view('org.trails.create');
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request data
    Log::info('Trail creation attempt', [
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
            'user_type' => Auth::user()->user_type ?? 'unknown'
        ]);

        $request->validate([
            'mountain_name' => 'required|string|max:255',
            'trail_name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'price' => 'required|numeric|min:0',
            'package_inclusions' => 'required|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'difficulty_description' => 'nullable|string',
            'duration' => 'required|string|max:255',
            'best_season' => 'required|string|max:255',
            'terrain_notes' => 'required|string',
            'other_trail_notes' => 'nullable|string',
            'permit_required' => 'boolean',
            'permit_process' => 'nullable|string',
            'departure_point' => 'required|string|max:255',
            'transport_options' => 'required|string',
            'side_trips' => 'nullable|string',
            'packing_list' => 'required|string',
            'health_fitness' => 'required|string',
            'requirements' => 'nullable|string',
            'emergency_contacts' => 'required|string',
            'campsite_info' => 'nullable|string',
            'guide_info' => 'nullable|string',
            'environmental_practices' => 'nullable|string',
            'customers_feedback' => 'nullable|string',
            'testimonials_faqs' => 'nullable|string',
            'length' => 'nullable|numeric|min:0',
            'elevation_gain' => 'nullable|integer|min:0',
            'elevation_high' => 'nullable|integer|min:0',
            'elevation_low' => 'nullable|integer|min:0',
            'estimated_time' => 'nullable|integer|min:0',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'trail_coordinates' => 'nullable|string',
            'gpx_file' => 'nullable|file|mimes:gpx,kml,kmz|max:10240', // 10MB max
            'activities' => 'nullable|array',
            'activities.*' => 'string',
        ]);

        try {
            $input = $request->all();

            // Remove trail_coordinates from input as it's not a database field
            // It will be processed separately into the coordinates field
            unset($input['trail_coordinates']);

            // Ensure nullable numeric metrics remain null if not provided (avoid misleading zeroes)
            foreach (['length','elevation_gain','elevation_high','elevation_low','estimated_time'] as $metric) {
                if (!isset($input[$metric]) || $input[$metric] === null || $input[$metric] === '') {
                    unset($input[$metric]);
                }
            }

            // Normalize features: empty array -> null
            if (empty($input['features'])) {
                $input['features'] = null;
            }

            $trail = new Trail($input);
            $trail->user_id = Auth::id();
            $trail->slug = $this->generateUniqueSlug(Str::slug($request->trail_name . '-' . $request->mountain_name));

            // Explicit boolean handling
            $trail->permit_required = $request->has('permit_required');

            // Handle trail coordinates
            $trailData = $this->processTrailCoordinates($request);
            if ($trailData) {
                $trail->coordinates = $trailData['coordinates'];
                
                // Extract representative latitude and longitude from coordinates
                if (isset($trailData['coordinates']) && !empty($trailData['coordinates'])) {
                    // Use the first coordinate as the starting point (trailhead)
                    $startPoint = $trailData['coordinates'][0];
                    $trail->latitude = $startPoint['lat'];
                    $trail->longitude = $startPoint['lng'];
                }
                
                // Auto-populate fields from GPX data if not provided
                if (!$trail->length && isset($trailData['total_distance_km'])) {
                    $trail->length = $trailData['total_distance_km'];
                }
                if (!$trail->elevation_gain && isset($trailData['elevation_gain_m'])) {
                    $trail->elevation_gain = $trailData['elevation_gain_m'];
                }
                if (!$trail->elevation_high && isset($trailData['max_elevation_m'])) {
                    $trail->elevation_high = $trailData['max_elevation_m'];
                }
                if (!$trail->elevation_low && isset($trailData['min_elevation_m'])) {
                    $trail->elevation_low = $trailData['min_elevation_m'];
                }
                
                // Store GPX file if uploaded
                if ($request->hasFile('gpx_file') && isset($trailData['gpx_content'])) {
                    $gpxPath = $this->storeGPXFile($request->file('gpx_file'), $trail);
                    $trail->gpx_file = $gpxPath;
                }
            }

            $trail->save();

            // Save activities explicitly (ensure array or null)
            if ($request->has('activities')) {
                $trail->activities = array_values(array_filter((array) $request->input('activities')));
                $trail->save();
            }

            // Dispatch async enrichment if metrics/coordinates missing
            if (!$trail->coordinates || $trail->length === null) {
                EnrichTrailData::dispatch($trail->id)->onQueue('trails');
            }
            
            // Handle image uploads
            $this->handleTrailImages($request, $trail);

                        Log::info('Trail created successfully', ['trail_id' => $trail->id]);

            return redirect()->route('org.trails.index')
                ->with('success', 'Trail created successfully!');
        } catch (\Exception $e) {
            Log::error('Trail creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()->withErrors(['error' => 'Failed to create trail: ' . $e->getMessage()]);
        }
    }

    public function show(Trail $trail)
    {
        // Ensure the trail belongs to the authenticated organization
        if ($trail->user_id !== Auth::id()) {
            abort(403);
        }

        $trail->load(['location', 'images']);
        return view('org.trails.show', compact('trail'));
    }

    public function edit(Trail $trail)
    {
        // Ensure the trail belongs to the authenticated organization
        if ($trail->user_id !== Auth::id()) {
            abort(403);
        }

        return view('org.trails.edit', compact('trail'));
    }

    public function update(Request $request, Trail $trail)
    {
        // Ensure the trail belongs to the authenticated organization
        if ($trail->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'mountain_name' => 'required|string|max:255',
            'trail_name' => 'required|string|max:255',
            'location_id' => 'required|exists:locations,id',
            'price' => 'required|numeric|min:0',
            'package_inclusions' => 'required|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'difficulty_description' => 'nullable|string',
            'duration' => 'required|string|max:255',
            'best_season' => 'required|string|max:255',
            'terrain_notes' => 'required|string',
            'other_trail_notes' => 'nullable|string',
            'permit_required' => 'boolean',
            'permit_process' => 'nullable|string',
            'departure_point' => 'required|string|max:255',
            'transport_options' => 'required|string',
            'side_trips' => 'nullable|string',
            'packing_list' => 'required|string',
            'health_fitness' => 'required|string',
            'requirements' => 'nullable|string',
            'emergency_contacts' => 'required|string',
            'campsite_info' => 'nullable|string',
            'guide_info' => 'nullable|string',
            'environmental_practices' => 'nullable|string',
            'customers_feedback' => 'nullable|string',
            'testimonials_faqs' => 'nullable|string',
            'length' => 'nullable|numeric|min:0',
            'elevation_gain' => 'nullable|integer|min:0',
            'elevation_high' => 'nullable|integer|min:0',
            'elevation_low' => 'nullable|integer|min:0',
            'estimated_time' => 'nullable|integer|min:0',
            'summary' => 'nullable|string',
            'description' => 'nullable|string',
            'activities' => 'nullable|array',
            'activities.*' => 'string',
        ]);

        $input = $request->all();
        
        // Remove trail_coordinates from input as it's not a database field
        unset($input['trail_coordinates']);
        
        foreach (['length','elevation_gain','elevation_high','elevation_low','estimated_time'] as $metric) {
            if (array_key_exists($metric,$input) && ($input[$metric] === '' || $input[$metric] === null)) {
                $input[$metric] = null; // preserve null
            }
        }
        if (empty($input['features'])) {
            $input['features'] = null;
        }

        $trail->fill($input);
        // Ensure activities array is preserved/updated
        if ($request->has('activities')) {
            $trail->activities = array_values(array_filter((array) $request->input('activities')));
        }
        $newBaseSlug = Str::slug($request->trail_name . '-' . $request->mountain_name);
        if ($trail->isDirty('trail_name') || $trail->isDirty('mountain_name')) {
            $trail->slug = $this->generateUniqueSlug($newBaseSlug, $trail->id);
        }
        $trail->save();

        return redirect()->route('org.trails.index')
            ->with('success', 'Trail updated successfully!');
    }

    public function destroy(Trail $trail)
    {
        // Ensure the trail belongs to the authenticated organization
        if ($trail->user_id !== Auth::id()) {
            abort(403);
        }

        $trail->delete();

        return redirect()->route('org.trails.index')
            ->with('success', 'Trail deleted successfully!');
    }

    public function toggleStatus(Trail $trail)
    {
        // Ensure the trail belongs to the authenticated organization
        if ($trail->user_id !== Auth::id()) {
            abort(403);
        }

        $trail->update(['is_active' => !$trail->is_active]);

        return redirect()->route('org.trails.index')
            ->with('success', 'Trail status updated successfully!');
    }

    /**
     * Handle trail image uploads
     */
    protected function handleTrailImages(Request $request, Trail $trail)
    {
        try {
            // Handle primary image
            if ($request->hasFile('primary_image')) {
                $primaryFile = $request->file('primary_image');
                $primaryPath = $primaryFile->store('trail-images/primary', 'public');
                
                TrailImage::create([
                    'trail_id' => $trail->id,
                    'image_path' => $primaryPath,
                    'image_type' => 'primary',
                    'caption' => 'Main trail photo',
                    'sort_order' => 1,
                    'is_primary' => true,
                ]);
                
                Log::info('Primary image uploaded', ['path' => $primaryPath]);
            }

            // Handle additional images
            if ($request->hasFile('additional_images')) {
                $sortOrder = 2;
                foreach ($request->file('additional_images') as $file) {
                    if ($file) {
                        $path = $file->store('trail-images/additional', 'public');
                        
                        TrailImage::create([
                            'trail_id' => $trail->id,
                            'image_path' => $path,
                            'image_type' => 'photo',
                            'caption' => "Trail view {$sortOrder}",
                            'sort_order' => $sortOrder,
                            'is_primary' => false,
                        ]);
                        
                        $sortOrder++;
                        Log::info('Additional image uploaded', ['path' => $path]);
                    }
                }
            }

            // Handle map image
            if ($request->hasFile('map_image')) {
                $mapFile = $request->file('map_image');
                $mapPath = $mapFile->store('trail-images/maps', 'public');
                
                TrailImage::create([
                    'trail_id' => $trail->id,
                    'image_path' => $mapPath,
                    'image_type' => 'map',
                    'caption' => 'Trail map',
                    'sort_order' => 99,
                    'is_primary' => false,
                ]);
                
                Log::info('Map image uploaded', ['path' => $mapPath]);
            }
            
        } catch (\Exception $e) {
            Log::error('Image upload error', [
                'trail_id' => $trail->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Removed auto coordinate generation helper.

    /**
     * Process trail coordinates from manual drawing, GPX upload, or preview
     */
    private function processTrailCoordinates(Request $request)
    {
        try {
            // Priority: GPX file upload first
            if ($request->hasFile('gpx_file')) {
                $gpxService = app(\App\Services\GPXService::class);
                $gpxData = $gpxService->processGPXUpload($request->file('gpx_file'));
                
                Log::info('GPX file processed', [
                    'points' => $gpxData['total_points'],
                    'distance' => $gpxData['total_distance_km'],
                    'elevation_gain' => $gpxData['elevation_gain_m']
                ]);
                
                return $gpxData;
            }
            
            // Second priority: Manual drawing or preview coordinates
            if ($request->filled('trail_coordinates')) {
                $coordinates = json_decode($request->trail_coordinates, true);
                
                if (is_array($coordinates) && count($coordinates) > 1) {
                    // Calculate basic statistics for manually drawn trails
                    $totalDistance = 0;
                    for ($i = 1; $i < count($coordinates); $i++) {
                        $totalDistance += $this->calculateDistance(
                            $coordinates[$i-1]['lat'], 
                            $coordinates[$i-1]['lng'],
                            $coordinates[$i]['lat'], 
                            $coordinates[$i]['lng']
                        );
                    }
                    
                    Log::info('Manual coordinates processed', [
                        'points' => count($coordinates),
                        'distance_km' => round($totalDistance / 1000, 2)
                    ]);
                    
                    return [
                        'coordinates' => $coordinates,
                        'total_distance_km' => round($totalDistance / 1000, 2),
                        'total_points' => count($coordinates)
                    ];
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error processing trail coordinates', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Store GPX file for download
     */
    private function storeGPXFile($file, $trail)
    {
        $filename = Str::slug($trail->trail_name . '-' . $trail->mountain_name) . '.gpx';
        $path = $file->storeAs('trail-gpx', $filename, 'public');
        
        Log::info('GPX file stored', ['path' => $path]);
        
        return $path;
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
     * Generate a unique slug by appending an incrementing suffix if needed.
     */
    private function generateUniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug;
        $counter = 2;
        while (Trail::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id','!=',$ignoreId))
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }
        return $slug;
    }
}
