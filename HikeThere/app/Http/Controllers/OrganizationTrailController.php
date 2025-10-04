<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\TrailImage;
use App\Models\Location;
use App\Services\GoogleDirectionsService;
use App\Services\TrailMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Jobs\EnrichTrailData;
use Illuminate\Support\Facades\DB;
use App\Models\TrailPackage;

class OrganizationTrailController extends Controller
{
    public function index(Request $request)
    {
        $query = Trail::where('user_id', Auth::id())
            ->with(['location', 'package']);

        // Filters
        if ($request->filled('mountain')) {
            $query->where('mountain_name', 'like', '%' . $request->mountain . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('price_min') || $request->filled('price_max')) {
            $query->whereHas('package', function($q) use ($request) {
                if ($request->filled('price_min')) {
                    $q->where('price', '>=', $request->price_min);
                }
                if ($request->filled('price_max')) {
                    $q->where('price', '<=', $request->price_max);
                }
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'popularity':
                $query->withCount('reviews')
                    ->orderBy('reviews_count', $sortOrder);
                break;
            case 'price':
                $query->leftJoin('trail_packages', 'trails.id', '=', 'trail_packages.trail_id')
                    ->orderBy('trail_packages.price', $sortOrder)
                    ->select('trails.*');
                break;
            case 'length':
                $query->orderBy('length', $sortOrder);
                break;
            case 'name':
                $query->orderBy('trail_name', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'updated_at':
                $query->orderBy('updated_at', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $trails = $query->paginate(10)->appends($request->query());

        // Get unique mountains for filter dropdown
        $mountains = Trail::where('user_id', Auth::id())
            ->select('mountain_name')
            ->distinct()
            ->whereNotNull('mountain_name')
            ->pluck('mountain_name');

        return view('org.trails.index', compact('trails', 'mountains'));
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

    // Build validation rules; support side_trips as either string (legacy textarea) or array (new UI)
    $updateRules = [
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
            // departure_point removed; organizations will store transport details instead
            'transport_details' => 'required|string',
            'transport_included' => 'nullable|in:0,1',
            'transportation_details' => 'nullable|string|max:2000',
            // Use stable keys for vehicle values - restrict to known keys
            'transportation_vehicle' => 'nullable|in:van,jeep,bus,car,motorbike',
            'side_trips' => 'nullable',
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
            // Accept common GPX/KML/KMZ files. Some GPX files may be detected as 'xml' by the OS/clients,
            // so allow .gpx, .kml, .kmz and .xml extensions and common MIME types as a fallback.
            'gpx_file' => ['nullable','file','max:10240',
                // allow standard extensions
                function($attribute, $value, $fail) {
                    $allowedExt = ['gpx','kml','kmz','xml'];
                    $extension = strtolower($value->getClientOriginalExtension() ?: '');
                    if (!in_array($extension, $allowedExt)) {
                        return $fail('The '.$attribute.' must be a file of type: gpx, kml, kmz.');
                    }
                    // basic mime check: accept xml/gpx/kml types
                    $mime = $value->getMimeType();
                    $allowedMimes = ['application/gpx+xml','application/gpx','application/xml','text/xml','application/vnd.google-earth.kml+xml','application/octet-stream'];
                    if ($mime && !in_array($mime, $allowedMimes)) {
                        // allow unknown mime for recognized extensions (some environments return octet-stream)
                        if (!in_array($extension, ['gpx','kml','kmz'])) {
                            return $fail('The '.$attribute.' must be a GPX/KML/KMZ file.');
                        }
                    }
                }
            ],
            'activities' => 'nullable|array',
            'activities.*' => 'string',
        ];

        // time fields validation (HH:MM)
        $updateRules['opening_time'] = 'nullable|date_format:H:i';
        $updateRules['closing_time'] = 'nullable|date_format:H:i';
        $updateRules['pickup_time'] = 'nullable|date_format:H:i';
        $updateRules['departure_time'] = 'nullable|date_format:H:i';

        // If an array was submitted for side_trips, validate its items
        if (is_array($request->input('side_trips'))) {
            $updateRules['side_trips'] = 'nullable|array';
            $updateRules['side_trips.*'] = 'nullable|string';
        }

        $request->validate($updateRules);

        try {
            $input = $request->all();

            // Normalize side_trips: if an array was submitted, convert to a single string for storage
            if (isset($input['side_trips']) && is_array($input['side_trips'])) {
                $input['side_trips'] = implode(', ', array_values(array_filter($input['side_trips'], function($v){
                    return $v !== null && $v !== '';
                })));
                if ($input['side_trips'] === '') {
                    $input['side_trips'] = null;
                }
            }

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

            // Remove package-related fields that belong to the `trail_packages` table
            // so they are not mass-assigned to the `trails` table (avoid SQL errors).
            $packageFields = [
                'price', 'package_inclusions', 'duration', 'permit_required', 'permit_process',
                'transport_included', 'transport_details', 'transportation_details', 'transportation_vehicle',
                'commute_legs', 'commute_summary', 'opening_time', 'closing_time', 'pickup_time', 'departure_time'
            ];
            foreach ($packageFields as $k) {
                if (array_key_exists($k, $input)) {
                    unset($input[$k]);
                }
            }

            $trail = new Trail($input);
            $trail->user_id = Auth::id();
            $trail->slug = $this->generateUniqueSlug(Str::slug($request->trail_name . '-' . $request->mountain_name));

            // Build package data separately so we do not attempt to mass-assign package fields to Trail
            $packageData = [];
            $packageData['price'] = $request->input('price');
            $packageData['package_inclusions'] = $request->input('package_inclusions');
            $packageData['duration'] = $request->input('duration');
            $packageData['permit_required'] = $request->has('permit_required');
            $packageData['permit_process'] = $request->input('permit_process');
            $packageData['transport_included'] = $request->input('transport_included') === '1' || $request->has('transport_included');

            // schedule/time fields
            $packageData['opening_time'] = $request->input('opening_time');
            $packageData['closing_time'] = $request->input('closing_time');
            $packageData['pickup_time'] = $request->input('pickup_time');
            $packageData['departure_time'] = $request->input('departure_time');

            // transport details / legacy transportation_details
            if ($request->filled('transport_details')) {
                $packageData['transport_details'] = $request->input('transport_details');
                $packageData['transportation_details'] = $request->input('transport_details');
            } elseif ($request->filled('transportation_details')) {
                $raw = $request->input('transportation_details');
                $decoded = null;
                if ($raw) {
                    try { $decoded = json_decode($raw, true); } catch (\Throwable $e) { $decoded = null; }
                }

                if (is_array($decoded) && isset($decoded['type']) && $decoded['type'] === 'commute' && isset($decoded['legs'])) {
                    $packageData['transportation_details'] = $raw;
                    $packageData['transport_details'] = $raw;

                    $legs = array_values(array_filter($decoded['legs'], function($l){
                        return (isset($l['from']) && trim($l['from']) !== '') || (isset($l['to']) && trim($l['to']) !== '') || (isset($l['vehicle']) && trim($l['vehicle']) !== '');
                    }));

                    $allowedVehicleKeys = ['van','jeep','bus','car','motorbike'];
                    foreach ($legs as $idx => $leg) {
                        if (isset($leg['vehicle']) && trim($leg['vehicle']) !== '') {
                            $key = trim($leg['vehicle']);
                            if (!in_array($key, $allowedVehicleKeys, true)) {
                                return redirect()->back()->withInput()->withErrors(['transportation_details' => "Invalid vehicle type in commute leg #".($idx+1).": {$key}"]);
                            }
                        }
                    }

                    // store legs as JSON text
                    $packageData['commute_legs'] = json_encode($legs);

                    $vehicleLabelMap = [
                        'van' => __('Van'), 'jeep' => __('Jeep'), 'bus' => __('Bus'), 'car' => __('Car'), 'motorbike' => __('Motorbike'),
                    ];
                    $summaryParts = [];
                    foreach ($legs as $leg) {
                        $from = trim($leg['from'] ?? '');
                        $to = trim($leg['to'] ?? '');
                        $vehicleKey = isset($leg['vehicle']) && trim($leg['vehicle']) !== '' ? trim($leg['vehicle']) : '';
                        $vehicle = $vehicleKey ? ' (' . ($vehicleLabelMap[$vehicleKey] ?? $vehicleKey) . ')' : '';
                        if ($from && $to) $summaryParts[] = $from . ' → ' . $to . $vehicle;
                        elseif ($from) $summaryParts[] = $from . ' → (unknown)' . $vehicle;
                        elseif ($to) $summaryParts[] = '(unknown) → ' . $to . $vehicle;
                    }
                    $packageData['commute_summary'] = count($summaryParts) ? implode('; ', $summaryParts) : null;
                } else {
                    $packageData['transportation_details'] = $raw;
                    $packageData['transport_details'] = $raw;
                    $packageData['commute_legs'] = null;
                    $packageData['commute_summary'] = null;
                }
            }

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
                
                // If a GPX/KML/KMZ was uploaded, processTrailCoordinates will store it under public/geojson and
                // return its path inside $trailData['gpx_path']. Use that path regardless of whether parsing succeeded.
                if (isset($trailData['gpx_path'])) {
                    $trail->gpx_file = $trailData['gpx_path'];
                }
            }
            
            // Always compute and overwrite estimated_time on the server when we have a derived length
            // (and optional elevation_gain). The server should be the canonical source of truth for
            // trail time estimates so we ignore any client-supplied value when sufficient metrics exist.
            if ($trail->length) {
                try {
                    $metrics = new TrailMetricsService();
                    $est = $metrics->estimateTime($trail->length, $trail->elevation_gain ?? null);
                    if ($est !== null) {
                        $trail->estimated_time = $est;
                    }
                } catch (\Throwable $e) {
                    // non-fatal: log and continue, leave estimated_time as provided (if any)
                    Log::warning('Failed to compute estimated_time: ' . $e->getMessage());
                }
            }

            DB::beginTransaction();
            $trail->save();

            // Create or update TrailPackage linked to this trail
            $packagePayload = array_filter([
                'price' => $packageData['price'] ?? null,
                'package_inclusions' => $packageData['package_inclusions'] ?? null,
                'duration' => $packageData['duration'] ?? null,
                'permit_required' => $packageData['permit_required'] ?? false,
                'permit_process' => $packageData['permit_process'] ?? null,
                'transport_included' => $packageData['transport_included'] ?? false,
                'transport_details' => $packageData['transport_details'] ?? null,
                'transportation_details' => $packageData['transportation_details'] ?? null,
                'commute_legs' => $packageData['commute_legs'] ?? null,
                'commute_summary' => $packageData['commute_summary'] ?? null,
                'side_trips' => isset($input['side_trips']) ? $input['side_trips'] : null,
                // times
                'opening_time' => $packageData['opening_time'] ?? null,
                'closing_time' => $packageData['closing_time'] ?? null,
                'pickup_time' => $packageData['pickup_time'] ?? null,
                'departure_time' => $packageData['departure_time'] ?? null,
            ], function($v){ return $v !== null; });

            // Create package
            $package = new TrailPackage($packagePayload);
            $package->trail_id = $trail->id;
            $package->save();

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

            DB::commit();

            Log::info('Trail and package created successfully', ['trail_id' => $trail->id, 'package_id' => $package->id]);

            // Clear cached enhanced map trails so newly created trail appears immediately
            try {
                Cache::forget('enhanced_map_trails');
            } catch (\Exception $e) {
                Log::warning('Failed to clear enhanced_map_trails cache: ' . $e->getMessage());
            }

            return redirect()->route('org.trails.index')
                ->with('success', 'Trail created successfully!')
                ->with('new_trail_id', $trail->id)
                ->with('new_trail_name', $trail->trail_name)
                ->with('show_event_prompt', true);
        } catch (\Exception $e) {
            DB::rollBack();
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

        // Load the package relationship to access package data like side_trips
        $trail->load('package');

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
            // departure_point removed; accept transport_details instead
            'transport_details' => 'required|string',
            'transportation_vehicle' => 'nullable|string|max:100',
            'side_trips' => 'nullable|array',
            'side_trips.*' => 'nullable|string',
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
            'opening_time' => 'nullable|date_format:H:i',
            'closing_time' => 'nullable|date_format:H:i',
            'pickup_time' => 'nullable|date_format:H:i',
            'departure_time' => 'nullable|date_format:H:i',
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

        // Normalize side_trips for update: accept array inputs from form and convert to stored string
        if (isset($input['side_trips']) && is_array($input['side_trips'])) {
            $input['side_trips'] = implode(', ', array_values(array_filter($input['side_trips'], function($v){
                return $v !== null && $v !== '';
            })));
            if ($input['side_trips'] === '') {
                $input['side_trips'] = null;
            }
        }

        // Remove package-related fields before filling Trail model (those belong to trail_packages table)
        $packageFields = [
            'price', 'package_inclusions', 'duration', 'permit_required', 'permit_process',
            'transport_included', 'transport_details', 'transportation_details', 'transportation_vehicle',
            'commute_legs', 'commute_summary', 'side_trips', 'opening_time', 'closing_time', 'pickup_time', 'departure_time'
        ];
        foreach ($packageFields as $k) {
            if (array_key_exists($k, $input)) {
                unset($input[$k]);
            }
        }

        $trail->fill($input);
        // Ensure activities array is preserved/updated
        if ($request->has('activities')) {
            $trail->activities = array_values(array_filter((array) $request->input('activities')));
        }
        // Always recompute and overwrite estimated_time on update when length exists. Server should be
        // authoritative for time estimates and therefore any client-provided value is replaced when
        // sufficient metrics are available.
        if ($trail->length) {
            try {
                $metrics = new TrailMetricsService();
                $est = $metrics->estimateTime($trail->length, $trail->elevation_gain ?? null);
                if ($est !== null) {
                    $trail->estimated_time = $est;
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to compute estimated_time during update: ' . $e->getMessage());
            }
        }
        $newBaseSlug = Str::slug($request->trail_name . '-' . $request->mountain_name);
        if ($trail->isDirty('trail_name') || $trail->isDirty('mountain_name')) {
            $trail->slug = $this->generateUniqueSlug($newBaseSlug, $trail->id);
        }
        $trail->save();

        // Clear cached enhanced map trails so updates are reflected
        try {
            Cache::forget('enhanced_map_trails');
        } catch (\Exception $e) {
            Log::warning('Failed to clear enhanced_map_trails cache after update: ' . $e->getMessage());
        }

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

        // Clear cache so deleted trail no longer appears on maps
        try {
            Cache::forget('enhanced_map_trails');
        } catch (\Exception $e) {
            Log::warning('Failed to clear enhanced_map_trails cache after delete: ' . $e->getMessage());
        }

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

        // Clear cache so status changes reflect on maps
        try {
            Cache::forget('enhanced_map_trails');
        } catch (\Exception $e) {
            Log::warning('Failed to clear enhanced_map_trails cache after toggleStatus: ' . $e->getMessage());
        }

        return redirect()->route('org.trails.index')
            ->with('success', 'Trail status updated successfully!');
    }

    /**
     * Handle trail image uploads with quality preservation
     */
    protected function handleTrailImages(Request $request, Trail $trail)
    {
        try {
            // Handle primary image
            if ($request->hasFile('primary_image')) {
                $primaryFile = $request->file('primary_image');
                
                // Store with high quality preservation (avoid compression)
                $primaryPath = $primaryFile->storeAs(
                    'trail-images/primary',
                    $primaryFile->hashName(),
                    ['disk' => 'public', 'quality' => 100]
                );
                
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
                        // Store with high quality preservation
                        $path = $file->storeAs(
                            'trail-images/additional',
                            $file->hashName(),
                            ['disk' => 'public', 'quality' => 100]
                        );
                        
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
                
                // Store with high quality preservation
                $mapPath = $mapFile->storeAs(
                    'trail-images/maps',
                    $mapFile->hashName(),
                    ['disk' => 'public', 'quality' => 100]
                );
                
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
                // Store uploaded file in public/geojson for later community/outsourced usage
                try {
                    $uploaded = $request->file('gpx_file');
                    $originalName = pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME);
                    $ext = strtolower($uploaded->getClientOriginalExtension());
                    $safeName = Str::slug($originalName) . '.' . $ext;
                    $publicDir = public_path('geojson');
                    if (!is_dir($publicDir)) {
                        mkdir($publicDir, 0755, true);
                    }
                    $destinationPath = $publicDir . DIRECTORY_SEPARATOR . $safeName;
                    $uploaded->move($publicDir, $safeName);

                    // Attempt to parse file but do not fail creation if parsing fails
                    $gpxService = app(\App\Services\GPXService::class);
                    try {
                        $gpxData = $gpxService->processGPXUpload($request->file('gpx_file'));
                        // attach the stored path so caller can persist it
                        $gpxData['gpx_path'] = 'geojson/' . $safeName;

                        Log::info('GPX file processed', [
                            'points' => $gpxData['total_points'] ?? 0,
                            'distance' => $gpxData['total_distance_km'] ?? 0,
                            'elevation_gain' => $gpxData['elevation_gain_m'] ?? 0,
                            'stored_path' => $gpxData['gpx_path']
                        ]);

                        return $gpxData;
                    } catch (\Exception $e) {
                        // Parsing failed, but we should still return the stored path so the trail record can reference the uploaded file
                        Log::warning('GPX parsing failed after storing file: ' . $e->getMessage());
                        return [
                            'coordinates' => [],
                            'total_distance_km' => null,
                            'elevation_gain_m' => null,
                            'min_elevation_m' => null,
                            'max_elevation_m' => null,
                            'total_points' => 0,
                            'gpx_path' => 'geojson/' . $safeName
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to store uploaded GPX file: ' . $e->getMessage());
                    // continue to other sources
                }
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
