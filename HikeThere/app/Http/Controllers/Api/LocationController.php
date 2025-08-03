<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('trails')
            ->having('trails_count', '>', 0)
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'slug' => $location->slug,
                    'full_name' => $location->name . ', ' . $location->province,
                    'trail_count' => $location->trails_count,
                ];
            });

        return response()->json($locations);
    }
}
