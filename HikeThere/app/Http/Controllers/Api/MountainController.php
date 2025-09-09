<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mountain;
use Illuminate\Http\Request;

class MountainController extends Controller
{
    public function index(Request $request)
    {
        $query = Mountain::query();

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where('name', 'like', "%{$q}%");
        }
        if ($request->filled('island_group')) {
            $query->where('island_group', $request->string('island_group'));
        }
        if ($request->filled('region')) {
            $region = $request->string('region');
            $query->whereJsonContains('regions', $region);
        }
        if ($request->filled('min_elev')) {
            $query->where('elevation', '>=', (int)$request->min_elev);
        }
        if ($request->filled('max_elev')) {
            $query->where('elevation', '<=', (int)$request->max_elev);
        }

        $mountains = $query->limit(1000)->get();

        if ($request->wantsJson() || $request->query('format') === 'geojson') {
            return $this->toGeoJson($mountains);
        }

        return response()->json($mountains);
    }

    public function show(string $slug, Request $request)
    {
        $mountain = Mountain::where('slug', $slug)->firstOrFail();
        if ($request->wantsJson() || $request->query('format') === 'geojson') {
            return $this->toGeoJson(collect([$mountain]));
        }
        return response()->json($mountain);
    }

    private function toGeoJson($collection)
    {
        return [
            'type' => 'FeatureCollection',
            'features' => $collection->map(function (Mountain $m) {
                $style = $m->style ?? [];
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$m->longitude, (float)$m->latitude]
                    ],
                    'properties' => array_filter([
                        'slug' => $m->slug,
                        'name' => $m->name,
                        'elev' => $m->elevation,
                        'prom' => $m->prominence,
                        'isl_grp' => $m->island_group,
                        'regions' => $m->regions,
                        'provinces' => $m->provinces,
                    ]) + $style
                ];
            })->values()->all()
        ];
    }
}
