<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\TrailImage;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        \Log::info('Trail creation page accessed', [
            'user_id' => Auth::id(),
            'user_type' => Auth::user()->user_type ?? 'unknown',
            'approval_status' => Auth::user()->approval_status ?? 'unknown',
            'is_authenticated' => Auth::check()
        ]);

        // Get all locations for the dropdown
        $locations = Location::orderBy('name')->get(['id', 'name', 'province', 'region']);

        return view('org.trails.create', compact('locations'));
    }

    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        \Log::info('Trail creation attempt', [
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
        ]);

        try {
            $trail = new Trail($request->all());
            $trail->user_id = Auth::id();
            $trail->slug = Str::slug($request->trail_name . '-' . $request->mountain_name);
            
            // Set default values for required fields if not provided
            $trail->length = $request->length ?? 0;
            $trail->elevation_gain = $request->elevation_gain ?? 0;
            $trail->elevation_high = $request->elevation_high ?? 0;
            $trail->elevation_low = $request->elevation_low ?? 0;
            $trail->estimated_time = $request->estimated_time ?? 0;
            $trail->summary = $request->summary ?? '';
            $trail->description = $request->description ?? '';
            $trail->features = $request->features ?? [];
            
            // Handle checkbox field properly
            $trail->permit_required = $request->has('permit_required');
            
            $trail->save();
            
            // Handle image uploads
            $this->handleTrailImages($request, $trail);

            \Log::info('Trail created successfully', ['trail_id' => $trail->id]);

            return redirect()->route('org.trails.index')
                ->with('success', 'Trail created successfully!');
        } catch (\Exception $e) {
            \Log::error('Trail creation failed', [
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

        $locations = Location::all();
        return view('org.trails.edit', compact('trail', 'locations'));
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
        ]);

        $trail->update($request->all());
        $trail->slug = Str::slug($request->trail_name . '-' . $request->mountain_name);
        
        // Set default values for required fields if not provided
        $trail->length = $request->length ?? 0;
        $trail->elevation_gain = $request->elevation_gain ?? 0;
        $trail->elevation_high = $request->elevation_high ?? 0;
        $trail->elevation_low = $request->elevation_low ?? 0;
        $trail->estimated_time = $request->estimated_time ?? 0;
        $trail->summary = $request->summary ?? '';
        $trail->description = $request->description ?? '';
        $trail->features = $request->features ?? [];
        
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
                
                \Log::info('Primary image uploaded', ['path' => $primaryPath]);
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
                        \Log::info('Additional image uploaded', ['path' => $path]);
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
                
                \Log::info('Map image uploaded', ['path' => $mapPath]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Image upload error', [
                'trail_id' => $trail->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
