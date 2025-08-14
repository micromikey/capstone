<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrailReview;

use App\Services\ContentFilterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReviewModerationController extends Controller
{
    /**
     * Show the review moderation dashboard
     */
    public function index()
    {
        $pendingReviews = TrailReview::with(['user', 'trail'])
            ->where('is_approved', false)
            ->orderBy('moderation_score', 'asc')
            ->paginate(20);

        $lowScoreReviews = TrailReview::with(['user', 'trail'])
            ->where('is_approved', true)
            ->where('moderation_score', '<', 70)
            ->orderBy('moderation_score', 'asc')
            ->paginate(20);

        return view('admin.review-moderation.index', compact('pendingReviews', 'lowScoreReviews'));
    }

    /**
     * Approve a review
     */
    public function approve(Request $request, $reviewId): JsonResponse
    {
        $review = TrailReview::findOrFail($reviewId);
        
        $review->update([
            'is_approved' => true,
            'moderation_score' => max($review->moderation_score, 70)
        ]);

        // Images are now part of the review record, no separate approval needed

        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully'
        ]);
    }

    /**
     * Reject a review
     */
    public function reject(Request $request, $reviewId): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $review = TrailReview::findOrFail($reviewId);
        
        $review->update([
            'is_approved' => false,
            'moderation_score' => 0,
            'moderation_feedback' => array_merge(
                $review->moderation_feedback ?? [],
                ['Admin rejection reason: ' . $request->reason]
            )
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review rejected successfully'
        ]);
    }

    /**
     * Re-moderate a review using the content filter service
     */
    public function remoderate(Request $request, $reviewId): JsonResponse
    {
        $review = TrailReview::findOrFail($reviewId);
        
        $contentFilterService = app('App\Services\ContentFilterService');
        $moderationResult = $contentFilterService->getModerationFeedback($review->review);
        
        $review->update([
            'is_approved' => $moderationResult['approved'],
            'moderation_score' => $moderationResult['score'],
            'moderation_feedback' => $moderationResult['feedback']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review re-moderated successfully',
            'moderation' => $moderationResult
        ]);
    }

    /**
     * Get review details for moderation
     */
    public function show($reviewId): JsonResponse
    {
        $review = TrailReview::with(['user', 'trail'])
            ->findOrFail($reviewId);

        return response()->json([
            'success' => true,
            'review' => $review
        ]);
    }

    /**
     * Bulk approve reviews
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'integer|exists:trail_reviews,id'
        ]);

        TrailReview::whereIn('id', $request->review_ids)->update([
            'is_approved' => true,
            'moderation_score' => 100
        ]);

        return response()->json([
            'success' => true,
            'message' => count($request->review_ids) . ' reviews approved successfully'
        ]);
    }

    /**
     * Get moderation statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_reviews' => TrailReview::count(),
            'approved_reviews' => TrailReview::where('is_approved', true)->count(),
            'pending_reviews' => TrailReview::where('is_approved', false)->count(),
            'low_score_reviews' => TrailReview::where('is_approved', true)
                ->where('moderation_score', '<', 70)->count(),
            'reviews_with_images' => TrailReview::whereNotNull('review_images')->count(),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }
}
