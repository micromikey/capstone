<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrganizationLocationController extends Controller
{
    /**
     * List locations (simple list for now)
     */
    public function index()
    {
        $locations = Location::orderBy('name')->paginate(20);
        return view('org.locations.index', compact('locations'));
    }

    /**
     * Store a new location (supports AJAX / JSON and standard form)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $i = 1;
        while (Location::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        $location = Location::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'province' => $validated['province'],
            'region' => $validated['region'],
            'country' => $validated['country'] ?? 'Philippines',
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'location' => $location,
                'message' => 'Location created successfully.',
            ]);
        }

        return redirect()->back()->with('status', 'Location created');
    }

    /**
     * Edit form (AJAX consumption optional)
     */
    public function edit(Location $location)
    {
        if (request()->wantsJson()) {
            return response()->json(['location' => $location]);
        }
        return view('org.locations.edit', compact('location'));
    }

    /**
     * Update an existing location
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
        ]);

        // If name changed, maybe regenerate slug (only if unique)
        if ($location->name !== $validated['name']) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $i = 1;
            while (Location::where('slug', $slug)->where('id', '!=', $location->id)->exists()) {
                $slug = $baseSlug.'-'.$i++;
            }
            $location->slug = $slug;
        }

        $location->fill([
            'name' => $validated['name'],
            'province' => $validated['province'],
            'region' => $validated['region'],
            'country' => $validated['country'] ?? 'Philippines',
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'description' => $validated['description'] ?? null,
        ])->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'location' => $location,
                'message' => 'Location updated successfully.',
            ]);
        }

        return redirect()->back()->with('status', 'Location updated');
    }

    /**
     * Delete a location (only if no trails reference it)
     */
    public function destroy(Location $location)
    {
        if ($location->trails()->exists()) {
            $msg = 'Cannot delete – trails reference this location.';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $location->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back()->with('status', 'Location deleted');
    }
}
