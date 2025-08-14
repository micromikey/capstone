<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trail;
use App\Services\TrailImageService;
use Illuminate\Http\Request;

class TrailController extends Controller
{
    protected $imageService;

    public function __construct(TrailImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index(Request $request)
    {
        $query = Trail::active()
            ->with(['location', 'primaryImage', 'mapImage', 'user']);

        // Filter trails based on user authentication and following relationships
        if (auth()->check() && auth()->user()->user_type === 'hiker') {
            // For hikers, only show trails from organizations they follow
            $followingIds = auth()->user()->following()->pluck('users.id')->toArray();
            if (!empty($followingIds)) {
                $query->whereIn('user_id', $followingIds);
            } else {
                // If not following any organizations, return empty result
                return response()->json([]);
            }
        } elseif (auth()->check() && auth()->user()->user_type === 'organization') {
            // For organizations, only show their own trails
            $query->where('user_id', auth()->id());
        } else {
            // For unauthenticated users, return empty result
            return response()->json([]);
        }

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
            $query->where(function($q) use ($request) {
                $q->where('trail_name', 'like', '%' . $request->search . '%')
                  ->orWhere('mountain_name', 'like', '%' . $request->search . '%');
            });
        }

        $trails = $query->get()->map(function ($trail) {
            return [
                'id' => $trail->id,
                'name' => $trail->trail_name,
                'mountain_name' => $trail->mountain_name,
                'slug' => $trail->slug,
                'difficulty' => $trail->difficulty,
                'length' => $trail->length,
                'elevation_gain' => $trail->elevation_gain,
                'elevation_high' => $trail->elevation_high,
                'elevation_low' => $trail->elevation_low,
                'estimated_time' => $trail->estimated_time_formatted,
                'summary' => $trail->summary,
                'description' => $trail->description,
                'average_rating' => number_format($trail->average_rating, 1),
                'total_reviews' => $trail->total_reviews,
                'location' => $trail->location->name . ', ' . $trail->location->province,
                'location_id' => $trail->location->id,
                'location_slug' => $trail->location->slug,
                'primary_image' => $trail->primaryImage?->url ?? $this->imageService->getTrailImage($trail, 'primary', 'medium'),
                'map_image' => $trail->mapImage?->url ?? $this->imageService->getTrailImage($trail, 'map', 'medium'),
                'features' => $trail->features,
                'organization' => $trail->user->display_name,
                'organization_id' => $trail->user->id,
                'price' => $trail->price,
                'duration' => $trail->duration,
                'best_season' => $trail->best_season,
                'coordinates' => $trail->coordinates,
                'gpx_file' => $trail->gpx_file ? \Storage::url($trail->gpx_file) : null,
            ];
        });

        return response()->json($trails);
    }

    public function show(Trail $trail)
    {
        $trail->load(['location', 'images', 'reviews.user', 'user']);

        return response()->json([
            'id' => $trail->id,
            'name' => $trail->trail_name,
            'mountain_name' => $trail->mountain_name,
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
            'organization' => [
                'name' => $trail->user->display_name,
                'id' => $trail->user->id,
            ],
            'images' => $trail->images->map(fn($img) => [
                'url' => $img->url,
                'type' => $img->image_type,
                'caption' => $img->caption,
            ]),
            'features' => $trail->features,
            'coordinates' => $trail->coordinates,
            'gpx_file' => $trail->gpx_file ? \Storage::url($trail->gpx_file) : null,
            'price' => $trail->price,
            'duration' => $trail->duration,
            'best_season' => $trail->best_season,
            'package_inclusions' => $trail->package_inclusions,
        ]);
    }
}