<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    /**
     * Search locations by name
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $locations = Location::where('name', 'like', "%{$query}%")
            ->orWhere('province', 'like', "%{$query}%")
            ->orWhere('region', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'name', 'province', 'region']);

        return response()->json(['data' => $locations]);
    }

    /**
     * Get all locations for dropdown
     */
    public function index(): JsonResponse
    {
        $locations = Location::orderBy('name')->get(['id', 'name', 'slug', 'province', 'region']);
        
        return response()->json($locations);
    }

    /**
     * Get location details by ID
     */
    public function show($id): JsonResponse
    {
        $location = Location::find($id);
        
        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }
        
        return response()->json(['data' => $location]);
    }
}
