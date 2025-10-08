<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trail_id',
        'event_id',
        'trail_review_id',
        'type',
        'content',
        'rating',
        'hike_date',
        'conditions',
        'images',
        'image_captions',
        'likes_count',
        'comments_count',
        'is_active'
    ];

    protected $casts = [
        'hike_date' => 'date',
        'conditions' => 'array',
        'images' => 'array',
        'image_captions' => 'array',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'is_active' => 'boolean'
    ];

    protected $appends = ['image_urls', 'is_liked_by_auth_user'];

    /**
     * Get the user that owns the post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trail associated with the post
     */
    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    /**
     * Get the event associated with the post
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the trail review associated with the post
     */
    public function trailReview()
    {
        return $this->belongsTo(TrailReview::class);
    }

    /**
     * Get the likes for the post
     */
    public function likes()
    {
        return $this->hasMany(CommunityPostLike::class, 'post_id');
    }

    /**
     * Get the comments for the post
     */
    public function comments()
    {
        return $this->hasMany(CommunityPostComment::class, 'post_id')
                    ->whereNull('parent_id')
                    ->with(['user', 'replies.user'])
                    ->latest();
    }

    /**
     * Get all comments including replies
     */
    public function allComments()
    {
        return $this->hasMany(CommunityPostComment::class, 'post_id');
    }

    /**
     * Check if the post is liked by a user
     */
    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Get if the post is liked by the authenticated user
     */
    public function getIsLikedByAuthUserAttribute()
    {
        if (!auth()->check()) {
            return false;
        }
        return $this->isLikedBy(auth()->id());
    }

    /**
     * Get image URLs for display
     */
    public function getImageUrlsAttribute()
    {
        $images = $this->images;
        if (!$images || !is_array($images)) {
            return [];
        }
        
        return array_map(function($image) {
            if (is_array($image) && isset($image['path'])) {
                return asset('storage/' . $image['path']);
            }
            return asset('storage/' . $image);
        }, $images);
    }

    /**
     * Scope to get posts by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get active posts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get posts from followed organizations
     */
    public function scopeFromFollowedOrganizations($query, $userId)
    {
        return $query->whereHas('user', function($q) use ($userId) {
            $q->whereHas('followers', function($followerQuery) use ($userId) {
                $followerQuery->where('follower_id', $userId);
            });
        });
    }
}
