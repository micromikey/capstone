<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Trail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    /**
     * Display the community dashboard for hikers to discover organizations
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'hiker') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Community features are only available for hikers.');
        }

        // Get approved organizations
        $organizations = User::where('user_type', 'organization')
            ->where('approval_status', 'approved')
            ->with(['organizationProfile'])
            ->withCount('followers')
            ->get();

        // Get organizations the hiker is following
        $followingIds = $user->following()->pluck('users.id')->toArray();

        // Get upcoming events from followed organizations
        $events = collect();
        if (!empty($followingIds)) {
            // Select events that are relevant:
            // - always_available
            // - start in the future
            // - or have an explicit end date in the future (ongoing events)
            $events = \App\Models\Event::whereIn('user_id', $followingIds)
                ->where(function($q) {
                    $q->where('always_available', true)
                      ->orWhere('start_at', '>=', now())
                      ->orWhere(function($q2) {
                          $q2->whereNotNull('end_at')->where('end_at', '>', now());
                      });
                })
                ->with(['user'])
                ->orderBy('start_at', 'asc')
                ->limit(6)
                ->get();
        }

        // Get recent trails from followed organizations
        $followedTrails = $user->followedOrganizationsTrails()
            ->with(['user', 'location', 'primaryImage'])
            ->limit(6)
            ->get();

        // Get total trails count from all approved organizations
        $totalTrails = Trail::whereHas('user', function($query) {
            $query->where('user_type', 'organization')
                  ->where('approval_status', 'approved');
        })->where('is_active', true)->count();

    return view('hiker.community.index', compact('organizations', 'followingIds', 'followedTrails', 'totalTrails', 'events'));
    }

    /**
     * Follow an organization (AJAX)
     */
    public function follow(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|exists:users,id'
        ]);

        $user = Auth::user();
        $organizationId = $request->organization_id;

        // Check if user is a hiker
        if ($user->user_type !== 'hiker') {
            return response()->json([
                'success' => false,
                'message' => 'Only hikers can follow organizations.'
            ], 403);
        }

        // Check if organization exists and is approved
        $organization = User::where('id', $organizationId)
            ->where('user_type', 'organization')
            ->where('approval_status', 'approved')
            ->first();

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found or not approved.'
            ], 404);
        }

        // Check if already following
        if ($user->isFollowing($organizationId)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already following this organization.'
            ], 400);
        }

        try {
            $user->followOrganization($organizationId);
            
            // Get updated follower count
            $followerCount = $organization->followers()->count();

            return response()->json([
                'success' => true,
                'message' => 'Successfully followed ' . $organization->display_name . '!',
                'follower_count' => $followerCount,
                'is_following' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while following the organization.'
            ], 500);
        }
    }

    /**
     * Unfollow an organization (AJAX)
     */
    public function unfollow(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|exists:users,id'
        ]);

        $user = Auth::user();
        $organizationId = $request->organization_id;

        // Check if user is a hiker
        if ($user->user_type !== 'hiker') {
            return response()->json([
                'success' => false,
                'message' => 'Only hikers can unfollow organizations.'
            ], 403);
        }

        $organization = User::find($organizationId);

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found.'
            ], 404);
        }

        // Check if not following
        if (!$user->isFollowing($organizationId)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not following this organization.'
            ], 400);
        }

        try {
            $user->unfollowOrganization($organizationId);
            
            // Get updated follower count
            $followerCount = $organization->followers()->count();

            return response()->json([
                'success' => true,
                'message' => 'Successfully unfollowed ' . $organization->display_name . '.',
                'follower_count' => $followerCount,
                'is_following' => false
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while unfollowing the organization.'
            ], 500);
        }
    }

    /**
     * Get trails from followed organizations (AJAX)
     */
    public function getFollowedTrails(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'hiker') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        try {
            $trails = $user->followedOrganizationsTrails()
                ->with(['user', 'location', 'primaryImage'])
                ->paginate(12);

            return response()->json([
                'success' => true,
                'trails' => $trails->items(),
                'pagination' => [
                    'current_page' => $trails->currentPage(),
                    'last_page' => $trails->lastPage(),
                    'total' => $trails->total(),
                    'per_page' => $trails->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching trails.'
            ], 500);
        }
    }

    /**
     * Get organization details (AJAX)
     */
    public function getOrganization(Request $request): JsonResponse
    {
        $organizationId = $request->route('organization');
        
        $organization = User::where('id', $organizationId)
            ->where('user_type', 'organization')
            ->where('approval_status', 'approved')
            ->with(['organizationProfile'])
            ->withCount(['followers', 'organizationTrails'])
            ->first();

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found.'
            ], 404);
        }

        // Check if current user is following this organization
        $isFollowing = false;
        if (Auth::check() && Auth::user()->user_type === 'hiker') {
            $isFollowing = Auth::user()->isFollowing($organizationId);
        }

        return response()->json([
            'success' => true,
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->display_name,
                'bio' => $organization->bio,
                'location' => $organization->location,
                'profile_picture_url' => $organization->profile_photo_url,
                'followers_count' => $organization->followers_count,
                'trails_count' => $organization->organization_trails_count,
                'organization_profile' => $organization->organizationProfile,
                'is_following' => $isFollowing
            ]
        ]);
    }

    /**
     * Get organization trails (AJAX)
     */
    public function getOrganizationTrails(Request $request): JsonResponse
    {
        $organizationId = $request->route('organization');
        
        $organization = User::where('id', $organizationId)
            ->where('user_type', 'organization')
            ->where('approval_status', 'approved')
            ->first();

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found.'
            ], 404);
        }

        try {
            $trails = Trail::where('user_id', $organizationId)
                ->where('is_active', true)
                ->with(['location', 'primaryImage', 'reviews'])
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            return response()->json([
                'success' => true,
                'trails' => $trails->items(),
                'pagination' => [
                    'current_page' => $trails->currentPage(),
                    'last_page' => $trails->lastPage(),
                    'total' => $trails->total(),
                    'per_page' => $trails->perPage()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching trails.'
            ], 500);
        }
    }

    /**
     * Show organization profile page for hikers
     */
    public function showOrganization($organizationId)
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'hiker') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Organization profiles are only available for hikers.');
        }

        $organization = User::where('id', $organizationId)
            ->where('user_type', 'organization')
            ->where('approval_status', 'approved')
            ->with(['organizationProfile'])
            ->withCount(['followers'])
            ->first();

        if (!$organization) {
            abort(404, 'Organization not found.');
        }

        // Check if current user is following this organization
        $isFollowing = $user->isFollowing($organizationId);

        // Get organization's trails with pagination
        $trails = Trail::where('user_id', $organizationId)
            ->where('is_active', true)
            ->with(['location', 'primaryImage'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Calculate average rating properly
        foreach ($trails as $trail) {
            $trail->average_rating = $trail->reviews_avg_rating ? round($trail->reviews_avg_rating, 1) : 0;
            $trail->total_reviews = $trail->reviews_count;
        }

        return view('hiker.community.organization-profile', compact('organization', 'isFollowing', 'trails'));
    }
}