<?php

namespace App\Http\Controllers;

use App\Models\Trail;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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
        $locations = Location::all();
        return view('org.trails.create', compact('locations'));
    }

    public function store(Request $request)
    {
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
        ]);

        $trail = new Trail($request->all());
        $trail->user_id = Auth::id();
        $trail->slug = Str::slug($request->trail_name . '-' . $request->mountain_name);
        $trail->save();

        return redirect()->route('org.trails.index')
            ->with('success', 'Trail created successfully!');
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
        ]);

        $trail->update($request->all());
        $trail->slug = Str::slug($request->trail_name . '-' . $request->mountain_name);
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
}
