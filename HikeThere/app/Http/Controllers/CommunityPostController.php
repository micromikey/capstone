<?php

namespace App\Http\Controllers;

use App\Models\CommunityPost;
use App\Models\CommunityPostLike;
use App\Models\CommunityPostComment;
use App\Models\Trail;
use App\Models\Event;
use App\Models\TrailReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommunityPostController extends Controller
{
    /**
     * Display a listing of posts
     */
    public function index(Request $request)
    {
        try {
            $query = CommunityPost::with(['user', 'trail', 'event', 'likes', 'comments'])
                ->active()
                ->latest();

            // Filter by type if specified
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            // If user is authenticated and wants to see followed organizations' posts
            if (auth()->check() && $request->get('following')) {
                $query->fromFollowedOrganizations(auth()->id());
            }

            $posts = $query->paginate(20);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'posts' => $posts
                ]);
            }

            return view('community.posts.index', compact('posts'));
        } catch (\Exception $e) {
            Log::error('Error in CommunityPost index: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load posts: ' . $e->getMessage(),
                    'posts' => ['data' => [], 'total' => 0]
                ], 500);
            }
            
            return back()->with('error', 'Failed to load posts');
        }
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trail_id' => 'nullable|exists:trails,id',
            'event_id' => 'nullable|exists:events,id',
            'content' => 'required|string|max:5000',
            'rating' => 'nullable|integer|min:1|max:5',
            'hike_date' => 'nullable|date',
            'conditions' => 'nullable|array',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_captions' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $type = $user->role === 'organization' ? 'organization' : 'hiker';

            // Check if user already has a post for this trail (hikers only)
            if ($type === 'hiker' && isset($validated['trail_id'])) {
                $existingPost = CommunityPost::where('user_id', $user->id)
                    ->where('trail_id', $validated['trail_id'])
                    ->where('type', 'hiker')
                    ->first();

                if ($existingPost) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already posted about this trail. You can only post once per trail.'
                    ], 422);
                }
            }

            // Handle image uploads
            $uploadedImages = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('community-posts', 'public');
                    $uploadedImages[] = [
                        'path' => $path,
                        'caption' => $validated['image_captions'][$index] ?? null
                    ];
                }
            }

            // Create the post
            $postData = [
                'user_id' => $user->id,
                'trail_id' => $validated['trail_id'] ?? null,
                'event_id' => $validated['event_id'] ?? null,
                'type' => $type,
                'content' => $validated['content'],
                'rating' => $validated['rating'] ?? null,
                'hike_date' => $validated['hike_date'] ?? null,
                'conditions' => $validated['conditions'] ?? null,
                'images' => !empty($uploadedImages) ? $uploadedImages : null,
                'image_captions' => $validated['image_captions'] ?? null
            ];

            $post = CommunityPost::create($postData);

            // If this is a hiker post about a trail with a rating, also create/update a trail review
            if ($type === 'hiker' && isset($validated['trail_id']) && isset($validated['rating'])) {
                $reviewData = [
                    'trail_id' => $validated['trail_id'],
                    'user_id' => $user->id,
                    'rating' => $validated['rating'],
                    'review' => $validated['content'],
                    'hike_date' => $validated['hike_date'] ?? null,
                    'conditions' => $validated['conditions'] ?? null,
                    'review_images' => !empty($uploadedImages) ? json_encode($uploadedImages) : null,
                    'image_captions' => !empty($validated['image_captions']) ? json_encode($validated['image_captions']) : null,
                    'is_approved' => true // Auto-approve since it's coming from community post
                ];

                $review = TrailReview::updateOrCreate(
                    [
                        'trail_id' => $validated['trail_id'],
                        'user_id' => $user->id
                    ],
                    $reviewData
                );

                // Link the review to the post
                $post->update(['trail_review_id' => $review->id]);
            }

            DB::commit();

            $post->load(['user', 'trail', 'event']);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully!',
                'post' => $post
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded images on error
            if (isset($uploadedImages)) {
                foreach ($uploadedImages as $image) {
                    Storage::disk('public')->delete($image['path']);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Like/Unlike a post
     */
    public function toggleLike(Request $request, CommunityPost $post)
    {
        $user = auth()->user();

        $like = CommunityPostLike::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            // Unlike
            $like->delete();
            $post->decrement('likes_count');
            $isLiked = false;
        } else {
            // Like
            CommunityPostLike::create([
                'post_id' => $post->id,
                'user_id' => $user->id
            ]);
            $post->increment('likes_count');
            $isLiked = true;
        }

        return response()->json([
            'success' => true,
            'is_liked' => $isLiked,
            'likes_count' => $post->fresh()->likes_count
        ]);
    }

    /**
     * Add a comment to a post
     */
    public function addComment(Request $request, CommunityPost $post)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:community_post_comments,id'
        ]);

        $comment = CommunityPostComment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment']
        ]);

        $post->increment('comments_count');
        $comment->load('user', 'replies.user');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => $comment
        ]);
    }

    /**
     * Get comments for a post
     */
    public function getComments(CommunityPost $post)
    {
        $comments = $post->comments()
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    /**
     * Delete a comment
     */
    public function deleteComment(CommunityPostComment $comment)
    {
        // Check if user owns the comment or the post
        $user = auth()->user();
        $post = $comment->post;

        if ($comment->user_id !== $user->id && $post->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this comment'
            ], 403);
        }

        $post->decrement('comments_count', 1 + $comment->replies()->count());
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    /**
     * Delete a post
     */
    public function destroy(CommunityPost $post)
    {
        if ($post->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this post'
            ], 403);
        }

        // Delete associated images
        if ($post->images) {
            foreach ($post->images as $image) {
                $path = is_array($image) ? $image['path'] : $image;
                Storage::disk('public')->delete($path);
            }
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ]);
    }

    /**
     * Get user's trails for post creation (hikers)
     */
    public function getUserTrails()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'trails' => []
                ], 401);
            }
            
            // Get IDs of organizations the user follows
            $followedOrgIds = DB::table('follows')
                ->where('follower_id', $user->id)
                ->pluck('following_id')
                ->toArray();
            
            if (empty($followedOrgIds)) {
                return response()->json([
                    'success' => true,
                    'trails' => [],
                    'message' => 'Follow some organizations to see their trails'
                ]);
            }
            
            // Get trails from followed organizations
            $trails = Trail::where('is_active', true)
                ->whereIn('user_id', $followedOrgIds)
                ->with('user:id,display_name')
                ->select('id', 'trail_name', 'user_id', 'slug')
                ->orderBy('trail_name')
                ->get();

            return response()->json([
                'success' => true,
                'trails' => $trails
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getUserTrails: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load trails: ' . $e->getMessage(),
                'trails' => []
            ], 500);
        }
    }

    /**
     * Get organization's trails and events for post creation (organizations)
     */
    public function getOrganizationContent()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'trails' => [],
                    'events' => []
                ], 401);
            }

            $trails = Trail::where('user_id', $user->id)
                ->where('is_active', true)
                ->select('id', 'trail_name', 'slug')
                ->get();

            $events = Event::where('user_id', $user->id)
                ->where('is_active', true)
                ->select('id', 'title', 'slug')
                ->get();

            return response()->json([
                'success' => true,
                'trails' => $trails,
                'events' => $events
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getOrganizationContent: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load content: ' . $e->getMessage(),
                'trails' => [],
                'events' => []
            ], 500);
        }
    }
}
