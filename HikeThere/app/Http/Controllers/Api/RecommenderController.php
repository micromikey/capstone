<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Trail;

class RecommenderController extends Controller
{
    /**
     * Proxy a recommendation request to the local ML service.
     * Example: GET /api/recommender/user/123?k=5
     */
    public function forUser(Request $request, $userId)
    {
        $k = (int) $request->query('k', 5);

        // Build user profile from query params, hiker profile, and preferences
        $user = \App\Models\User::find($userId);
        $hikingPrefs = $user && $user->hiking_preferences ? $user->hiking_preferences : [];
        $dbPrefs = $user && $user->preferences ? $user->preferences->toArray() : [];

        $userProfile = [
            // Query param overrides
            'preferred_difficulty' => $request->query('difficulty', $hikingPrefs['difficulty'] ?? null),
            'preferred_tags' => $request->query('tags') ? explode(',', $request->query('tags')) : ($hikingPrefs['tags'] ?? []),
            'location' => $user->location ?? null,
            'liked_trail_ids' => $user ? $user->trailReviews()->pluck('trail_id')->unique()->values()->all() : [],
            // Merge in all hiking_preferences fields
            'hiking_preferences' => $hikingPrefs,
            // Merge in all UserPreference fields
            'user_preferences' => $dbPrefs,
        ];

        // Normalize user-friendly difficulty values (onboard uses easy/moderate/challenging)
        $difficultyMap = [
            'easy' => 'beginner',
            'moderate' => 'intermediate',
            'challenging' => 'advanced',
            // accept synonyms
            'beginner' => 'beginner',
            'intermediate' => 'intermediate',
            'advanced' => 'advanced',
            'hard' => 'advanced',
            'difficult' => 'advanced',
            'very_hard' => 'advanced',
        ];

        $rawPref = strtolower(trim((string)($userProfile['preferred_difficulty'] ?? '')));
        $normalizedPref = $difficultyMap[$rawPref] ?? null;
        if ($normalizedPref) {
            $userProfile['preferred_difficulty'] = $normalizedPref;
        }

        // ML service endpoint
        $mlHost = config('app.ml_recommender_host');

        // Cache key and TTL (seconds)
        $cacheTtl = (int) config('app.ml_recommender_cache_ttl');
        $cacheKey = 'recommender:user:' . $userId . ':k:' . $k . ':profile:' . md5(json_encode($userProfile));

        // Return cached response if present, but validate shape to avoid serving stale payloads
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            // Basic validation: recommendations should be an array; if present and non-empty, ensure first item has name/trail_name or images
            $ok = false;
            if (isset($cached['recommendations']) && is_array($cached['recommendations'])) {
                if (count($cached['recommendations']) === 0) {
                    $ok = true; // empty list is valid
                } else {
                    $first = $cached['recommendations'][0];
                    if (is_array($first) && (
                        !empty($first['name']) || !empty($first['trail_name']) || !empty($first['primary_image']) || isset($first['slug'])
                    )) {
                        $ok = true;
                    }
                }
            }

            if ($ok) {
                Log::debug('Recommender cache hit (valid)', ['key' => $cacheKey]);
                return response()->json($cached);
            }

            Log::info('Recommender cache present but missing expected fields; regenerating', ['key' => $cacheKey]);
        }

        try {
            // Give the ML service a little more time (cold-starts), but don't block the UI for too long.
            $resp = Http::timeout(10)->post(rtrim($mlHost, '/') . '/recommend', [
                'user_profile' => $userProfile,
                'k' => $k,
            ]);

            if ($resp->successful()) {
                $payload = $resp->json();

                // Normalize into a unified recommendations array containing full trail objects
                $recommendations = [];

                // ML-style results: [{trail_id, score, explanation}, ...]
                if (isset($payload['results']) && is_array($payload['results'])) {
                    $ids = array_values(array_filter(array_map(function ($r) {
                        return isset($r['trail_id']) ? $r['trail_id'] : null;
                    }, $payload['results'])));

                    // Eager load primary image, location and reviews_count for frontend
                    $trails = Trail::whereIn('id', $ids)->with(['primaryImage', 'location'])->withCount('reviews')->get()->keyBy('id');

                    foreach ($payload['results'] as $res) {
                        $tid = $res['trail_id'] ?? null;
                        if (!$tid || !isset($trails[$tid])) {
                            continue;
                        }
                        $t = $trails[$tid];
                        $images = $t->images->map(function ($img) {
                            return [
                                'url' => $img->url ?? null,
                                'image_path' => $img->image_path ?? null,
                                'caption' => $img->caption ?? null,
                                'is_primary' => (bool) ($img->is_primary ?? false),
                            ];
                        })->values()->all();

                        // Build formatted location string if available
                        $loc = $t->location;
                        $locationLabel = null;
                        if ($loc) {
                            $parts = [];
                            if (!empty($loc->name)) $parts[] = $loc->name;
                            if (!empty($loc->province)) $parts[] = $loc->province;
                            if (!empty($loc->country)) $parts[] = $loc->country;
                            $locationLabel = implode(', ', $parts);
                        }

                        $recommendations[] = [
                            'id' => $t->id,
                            // Display name preference: trail_name then name
                            'name' => $t->trail_name ?? $t->name ?? null,
                            'trail_name' => $t->trail_name ?? null,
                            'slug' => $t->slug,
                            'average_rating' => $t->average_rating,
                            'reviews_count' => $t->reviews_count ?? 0,
                            'primary_image' => $t->primaryImage ? ($t->primaryImage->url ?? null) : null,
                            'mountain_name' => $t->mountain_name ?? null,
                            'location_label' => $locationLabel,
                        ];
                    }

                // Some services may already return full recommendations
                } elseif (isset($payload['recommendations']) && is_array($payload['recommendations'])) {
                    foreach ($payload['recommendations'] as $r) {
                        $recommendations[] = [
                            'id' => $r['id'] ?? $r['trail_id'] ?? null,
                            'name' => $r['trail_name'] ?? $r['name'] ?? null,
                            'trail_name' => $r['trail_name'] ?? $r['name'] ?? null,
                            'slug' => $r['slug'] ?? null,
                            'average_rating' => $r['average_rating'] ?? $r['averageRating'] ?? null,
                            'reviews_count' => $r['reviews_count'] ?? 0,
                            'primary_image' => $r['primary_image'] ?? ($r['images'] && is_array($r['images']) && count($r['images']) ? ($r['images'][0]['url'] ?? null) : null),
                            'mountain_name' => $r['mountain_name'] ?? null,
                            'location_label' => $r['location_label'] ?? null,
                        ];
                    }
                }

                $out = ['recommendations' => $recommendations];

                // Cache payload
                try {
                    Cache::put($cacheKey, $out, $cacheTtl);
                } catch (\Throwable $e) {
                    Log::warning('Failed to cache recommender response', ['error' => $e->getMessage(), 'key' => $cacheKey]);
                }

                return response()->json($out);
            }

            Log::warning('ML service returned non-success', ['status' => $resp->status()]);
            // Try returning cached value even if ML returned non-success
            if (Cache::has($cacheKey)) {
                Log::debug('Recommender returning stale cache after ML non-success', ['key' => $cacheKey]);
                return response()->json(Cache::get($cacheKey));
            }

            // Graceful fallback: if we have no cache, return a small list of popular active trails from the DB
            Log::info('Recommender ML non-success; attempting DB fallback', ['status' => $resp->status()]);
            $fallbackQuery = Trail::query()->active()->withCount('reviews');

            // If user specified a preferred difficulty (normalized above), prefer those
            if (!empty($userProfile['preferred_difficulty'])) {
                $fallbackQuery->where('difficulty', $userProfile['preferred_difficulty']);
            }

            $fallbackTrails = $fallbackQuery
                ->orderByDesc('reviews_count')
                ->orderByDesc('elevation_gain')
                ->limit($k)
                ->with(['primaryImage', 'location'])
                ->withCount('reviews')
                ->get()
                ->map(function ($t) {
                    $loc = $t->location;
                    $locationLabel = null;
                    if ($loc) {
                        $parts = [];
                        if (!empty($loc->name)) $parts[] = $loc->name;
                        if (!empty($loc->province)) $parts[] = $loc->province;
                        if (!empty($loc->country)) $parts[] = $loc->country;
                        $locationLabel = implode(', ', $parts);
                    }

                    return [
                        'id' => $t->id,
                        'name' => $t->trail_name ?? $t->name ?? null,
                        'trail_name' => $t->trail_name ?? null,
                        'slug' => $t->slug,
                        'average_rating' => $t->average_rating,
                        'reviews_count' => $t->reviews_count ?? 0,
                        'primary_image' => $t->primaryImage ? ($t->primaryImage->url ?? null) : null,
                        'mountain_name' => $t->mountain_name ?? null,
                        'location_label' => $locationLabel,
                    ];
                })->values();

            return response()->json(['recommendations' => $fallbackTrails, 'warning' => 'ML service returned an error, returning popular trails from DB'], 200);
        } catch (\Throwable $e) {
            Log::error('Failed to contact ML service', ['message' => $e->getMessage()]);
            // On failure, return cached value if available
            if (Cache::has($cacheKey)) {
                Log::debug('Recommender returning stale cache after exception', ['key' => $cacheKey]);
                return response()->json(Cache::get($cacheKey));
            }

            // Graceful fallback: DB-based popular trails when ML is unreachable and no cache
            Log::info('Recommender ML unreachable; attempting DB fallback (exception)', ['error' => $e->getMessage()]);
            $fallbackTrails = Trail::query()
                ->active()
                ->with(['primaryImage', 'location'])
                ->withCount('reviews')
                ->orderByDesc('reviews_count')
                ->orderByDesc('elevation_gain')
                ->limit($k)
                ->get()
                ->map(function ($t) {
                    $loc = $t->location;
                    $locationLabel = null;
                    if ($loc) {
                        $parts = [];
                        if (!empty($loc->name)) $parts[] = $loc->name;
                        if (!empty($loc->province)) $parts[] = $loc->province;
                        if (!empty($loc->country)) $parts[] = $loc->country;
                        $locationLabel = implode(', ', $parts);
                    }

                    return [
                        'id' => $t->id,
                        'name' => $t->trail_name ?? $t->name ?? null,
                        'trail_name' => $t->trail_name ?? null,
                        'slug' => $t->slug,
                        'average_rating' => $t->average_rating,
                        'reviews_count' => $t->reviews_count ?? 0,
                        'primary_image' => $t->primaryImage ? ($t->primaryImage->url ?? null) : null,
                        'mountain_name' => $t->mountain_name ?? null,
                        'location_label' => $locationLabel,
                        'score' => null,
                        'explanation' => null,
                    ];
                })->values();

            return response()->json(['recommendations' => $fallbackTrails, 'warning' => 'Failed to contact ML service, returning popular trails from DB', 'error_message' => $e->getMessage()], 200);
        }
    }
}
