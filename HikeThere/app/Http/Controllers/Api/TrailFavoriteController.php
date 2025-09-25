<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trail;

class TrailFavoriteController extends Controller
{
    /** Toggle favorite for the authenticated user */
    public function toggle(Request $request)
    {
        // Try several ways to resolve the authenticated user (session or sanctum)
        $user = $request->user() ?: Auth::user() ?: Auth::guard('sanctum')->user() ?: Auth::guard('web')->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $trailId = $request->input('trail_id');
        $trail = Trail::find($trailId);
        if (! $trail) {
            return response()->json(['success' => false, 'message' => 'Trail not found'], 404);
        }

        $isFavorited = $user->favoriteTrails()->where('trail_id', $trailId)->exists();

        if ($isFavorited) {
            $user->favoriteTrails()->detach($trailId);
            $isNowFavorited = false;
            $message = 'Removed from favorites';
        } else {
            $user->favoriteTrails()->attach($trailId);
            $isNowFavorited = true;
            $message = 'Added to favorites';
        }

        // Return updated count
        $count = $trail->favoritedBy()->count();

        return response()->json([
            'success' => true,
            'is_favorited' => $isNowFavorited,
            'count' => $count,
            'message' => $message,
        ]);
    }

    /** List user's favorites */
    public function index(Request $request)
    {
        $user = $request->user() ?: Auth::user() ?: Auth::guard('sanctum')->user() ?: Auth::guard('web')->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $favorites = $user->favoriteTrails()->with(['location', 'primaryImage'])->paginate(20);

        return response()->json(['success' => true, 'data' => $favorites]);
    }

    /** Check if a given trail is favorited by the authenticated user (session or sanctum) */
    public function isFavorited(Request $request, Trail $trail)
    {

        $user = $request->user() ?: Auth::user() ?: Auth::guard('sanctum')->user() ?: Auth::guard('web')->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $exists = $user->favoriteTrails()->where('trail_id', $trail->id)->exists();

        return response()->json(['success' => true, 'is_favorited' => (bool) $exists]);
    }
}
