<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrailReview extends Model
{
    protected $fillable = [
        'trail_id', 'user_id', 'rating', 'review', 'hike_date', 'conditions',
        'is_approved', 'moderation_score', 'moderation_feedback',
        'review_images', 'image_captions'
    ];

    protected $casts = [
        'hike_date' => 'date',
        'conditions' => 'array',
        'is_approved' => 'boolean',
        'moderation_score' => 'integer',
        'moderation_feedback' => 'array'
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the review images as an array
     */
    public function getReviewImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set the review images as JSON string
     */
    public function setReviewImagesAttribute($value)
    {
        $this->attributes['review_images'] = $value ? json_encode($value) : null;
    }

    /**
     * Get image URLs for display
     */
    public function getImageUrlsAttribute()
    {
        $images = $this->review_images;
        if (!$images) return [];
        
        return array_map(function($image) {
            return asset('storage/' . $image['path']);
        }, $images);
    }

    /**
     * Get thumbnail URLs for display
     */
    public function getThumbnailUrlsAttribute()
    {
        $images = $this->review_images;
        if (!$images) return [];
        
        return array_map(function($image) {
            $path = $image['path'];
            $pathInfo = pathinfo($path);
            $thumbnailPath = $pathInfo['dirname'] . '/thumbnails/' . $pathInfo['basename'];
            
            // Check if thumbnail exists, otherwise return original
            if (file_exists(storage_path('app/public/' . $thumbnailPath))) {
                return asset('storage/' . $thumbnailPath);
            }
            
            return asset('storage/' . $path);
        }, $images);
    }

    /**
     * Scope for approved reviews only
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for reviews that need moderation
     */
    public function scopeNeedsModeration($query)
    {
        return $query->where('is_approved', false);
    }

    /**
     * Scope for reviews with low moderation scores
     */
    public function scopeLowScore($query, $threshold = 50)
    {
        return $query->where('moderation_score', '<', $threshold);
    }
}