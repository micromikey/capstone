<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trail;
use Illuminate\Http\Request;

class TrailController extends Controller
{
    public function index(Request $request)
    {
        $query = Trail::active()
            ->with(['location', 'primaryImage', 'mapImage']);

        // Filter by location
        if ($request->has('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('slug', $request->location);
            });
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $trails = $query->get()->map(function ($trail) {
            return [
                'id' => $trail->id,
                'name' => $trail->name,
                'slug' => $trail->slug,
                'difficulty' => $trail->difficulty_label,
                'length' => $trail->length,
                'elevation_gain' => $trail->elevation_gain,
                'elevation_high' => $trail->elevation_high,
                'elevation_low' => $trail->elevation_low,
                'estimated_time' => $trail->estimated_time_formatted,
                'summary' => $trail->summary,
                'average_rating' => $trail->average_rating,
                'total_reviews' => $trail->total_reviews,
                'location' => $trail->location->name . ', ' . $trail->location->province,
                'primary_image' => $trail->primaryImage?->url,
                'map_image' => $trail->mapImage?->url,
                'features' => $trail->features,
            ];
        });

        return response()->json($trails);
    }

    public function show(Trail $trail)
    {
        $trail->load(['location', 'images', 'reviews.user']);

        return response()->json([
            'id' => $trail->id,
            'name' => $trail->name,
            'slug' => $trail->slug,
            'difficulty' => $trail->difficulty_label,
            'length' => $trail->length,
            'elevation_gain' => $trail->elevation_gain,
            'elevation_high' => $trail->elevation_high,
            'elevation_low' => $trail->elevation_low,
            'estimated_time' => $trail->estimated_time_formatted,
            'summary' => $trail->summary,
            'description' => $trail->description,
            'average_rating' => $trail->average_rating,
            'total_reviews' => $trail->total_reviews,
            'location' => [
                'name' => $trail->location->name,
                'full_name' => $trail->location->name . ', ' . $trail->location->province,
                'coordinates' => [
                    'lat' => $trail->location->latitude,
                    'lng' => $trail->location->longitude,
                ]
            ],
            'images' => $trail->images->map(fn($img) => [
                'url' => $img->url,
                'type' => $img->image_type,
                'caption' => $img->caption,
            ]),
            'features' => $trail->features,
            'coordinates' => $trail->coordinates,
            'gpx_file' => $trail->gpx_file ? \Storage::url($trail->gpx_file) : null,
        ]);
    }
}