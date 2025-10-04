<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Trail;
use App\Models\TrailReview;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrailReviewController extends Controller
{
    /**
     * Store a new trail review (AJAX)
     */
    public function store(Request $request)
    {
        \Log::info('Review submission request received', [
            'has_files' => $request->hasFile('review_images'),
            'files_count' => $request->file('review_images') ? count($request->file('review_images')) : 0,
            'all_data' => $request->all(),
            'files' => $request->allFiles(),
            'user_id' => auth()->id(),
            'user_type' => auth()->user() ? auth()->user()->user_type : 'none'
        ]);

        $request->validate([
            'trail_id' => 'required|exists:trails,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
            'hike_date' => 'required|date|before_or_equal:today',
            'conditions' => 'nullable|array',
            'conditions.*' => 'string|in:sunny,cloudy,rainy,windy,foggy,hot,cold,humid,dry'
        ]);

        // Check if user has already reviewed this trail
        $existingReview = TrailReview::where('user_id', auth()->id())
            ->where('trail_id', $request->trail_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this trail.'
            ], 400);
        }

        // Content filtering
        $contentFilterService = app('App\Services\ContentFilterService');
        $moderationResult = $contentFilterService->getModerationFeedback($request->review);

        // Create the review
        $review = TrailReview::create([
            'trail_id' => $request->trail_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review' => $request->review,
            'hike_date' => $request->hike_date,
            'conditions' => $request->conditions ?? [],
            'is_approved' => $moderationResult['approved'],
            'moderation_score' => $moderationResult['score'],
            'moderation_feedback' => $moderationResult['feedback']
        ]);

        // Handle image uploads - simplified approach
        $uploadedImages = [];
        $imageErrors = [];
        
        if ($request->hasFile('review_images')) {
            \Log::info('Processing review images: ' . count($request->file('review_images')));
            \Log::info('File details:', [
                'files' => $request->file('review_images'),
                'first_file' => $request->file('review_images')[0] ?? 'none',
                'first_file_valid' => $request->file('review_images')[0] ? $request->file('review_images')[0]->isValid() : 'no file'
            ]);
            
            // Ensure the storage directory exists
            $storagePath = storage_path('app/public/review-images');
            if (!file_exists($storagePath)) {
                if (!mkdir($storagePath, 0755, true)) {
                    \Log::error('Failed to create review-images directory');
                    $imageErrors[] = 'Failed to create storage directory';
                }
            }
            
            foreach ($request->file('review_images') as $index => $image) {
                if ($image && $image->isValid()) {
                    try {
                        // Validate file size (2MB max to match PHP limit)
                        if ($image->getSize() > 2 * 1024 * 1024) {
                            \Log::warning("Image {$index} exceeds 2MB limit");
                            $imageErrors[] = "Image " . ($index + 1) . " exceeds 2MB limit. Please use smaller images.";
                            continue;
                        }
                        
                        // Validate MIME type
                        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                        if (!in_array($image->getMimeType(), $allowedMimes)) {
                            \Log::warning("Image {$index} has invalid MIME type: " . $image->getMimeType());
                            $imageErrors[] = "Image " . ($index + 1) . " has invalid format. Only JPG, PNG, GIF allowed.";
                            continue;
                        }
                        
                        $imagePath = $image->storeAs(
                            'review-images',
                            $image->hashName(),
                            ['disk' => 'public', 'quality' => 100]
                        );
                        \Log::info("Image stored at: {$imagePath}");
                        
                        $uploadedImages[] = [
                            'path' => $imagePath,
                            'name' => $image->getClientOriginalName(),
                            'size' => $image->getSize(),
                            'mime_type' => $image->getMimeType(),
                            'caption' => 'Review photo for trail'
                        ];
                        
                    } catch (\Exception $e) {
                        \Log::error('Failed to upload review image: ' . $e->getMessage());
                        $imageErrors[] = "Image " . ($index + 1) . " failed to upload: " . $e->getMessage();
                        // Continue with other images even if one fails
                    }
                } else {
                    \Log::warning("Image {$index} is not valid");
                    $imageErrors[] = "Image " . ($index + 1) . " is not valid";
                }
            }
            
            // Update the review with image data
            if (!empty($uploadedImages)) {
                $review->update([
                    'review_images' => $uploadedImages,
                    'image_captions' => json_encode(array_column($uploadedImages, 'caption'))
                ]);
                \Log::info('Review updated with ' . count($uploadedImages) . ' images');
            }
        }

        $message = $moderationResult['approved'] 
            ? 'Review submitted successfully!' 
            : 'Review submitted but pending moderation due to content guidelines.';

        // Add image upload information to the response
        $response = [
            'success' => true,
            'message' => $message,
            'review' => $review->load(['user', 'trail']),
            'moderation' => $moderationResult
        ];

        if (!empty($uploadedImages)) {
            $response['images_uploaded'] = count($uploadedImages);
        }

        if (!empty($imageErrors)) {
            $response['image_errors'] = $imageErrors;
            $response['message'] .= ' Some images failed to upload.';
        }

        return response()->json($response);
    }

    /**
     * Update an existing trail review (AJAX)
     */
    public function update(Request $request, $reviewId): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'hiker') {
            return response()->json([
                'success' => false,
                'message' => 'Only hikers can update trail reviews.'
            ], 403);
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
            'hike_date' => 'required|date|before_or_equal:today',
            'conditions' => 'nullable|array',
            'conditions.*' => 'string|in:sunny,cloudy,rainy,windy,foggy,hot,cold,humid,dry'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $review = TrailReview::where('id', $reviewId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Update the review
            $review->update([
                'rating' => $request->rating,
                'review' => $request->review,
                'hike_date' => $request->hike_date,
                'conditions' => $request->conditions ?? []
            ]);

            // Calculate updated trail statistics
            $updatedStats = $this->getTrailStats($review->trail_id);

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully!',
                'review' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'review' => $review->review,
                    'hike_date' => $review->hike_date->format('M d, Y'),
                    'conditions' => $review->conditions,
                    'updated_at' => $review->updated_at->diffForHumans()
                ],
                'trail_stats' => $updatedStats
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or you are not authorized to update it.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your review.'
            ], 500);
        }
    }

    /**
     * Delete a trail review (AJAX)
     */
    public function destroy(Request $request, $reviewId): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'hiker') {
            return response()->json([
                'success' => false,
                'message' => 'Only hikers can delete trail reviews.'
            ], 403);
        }

        try {
            $review = TrailReview::where('id', $reviewId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $trailId = $review->trail_id;
            $review->delete();

            // Calculate updated trail statistics
            $updatedStats = $this->getTrailStats($trailId);

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully.',
                'trail_stats' => $updatedStats
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or you are not authorized to delete it.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting your review.'
            ], 500);
        }
    }

    /**
     * Get reviews for a specific trail (AJAX)
     */
    public function getTrailReviews(Request $request, $trailId)
    {
        $reviews = TrailReview::with(['user'])
            ->where('trail_id', $trailId)
            ->where('is_approved', true) // Only show approved reviews
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $formattedReviews = $reviews->getCollection()->map(function ($review) {
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'review' => $review->review,
                'hike_date' => $review->hike_date->format('M d, Y'),
                'conditions' => $review->conditions,
                'created_at' => $review->created_at->diffForHumans(),
                'images' => $review->image_urls ?? [],
                'thumbnails' => $review->thumbnail_urls ?? [],
                'user' => [
                    'name' => $review->user->name,
                    'profile_photo_url' => $review->user->profile_photo_url
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'reviews' => $formattedReviews,
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total()
            ]
        ]);
    }

    /**
     * Get trail statistics (average rating, total reviews, etc.)
     */
    private function getTrailStats($trailId): array
    {
        $trail = Trail::with('reviews')->findOrFail($trailId);
        
        $totalReviews = $trail->reviews->count();
        $averageRating = $totalReviews > 0 ? $trail->reviews->avg('rating') : 0;
        
        // Rating distribution
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $trail->reviews->where('rating', $i)->count();
            $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 1),
            'rating_distribution' => $ratingDistribution
        ];
    }
}