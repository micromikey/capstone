<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\TrailImage;
use App\Models\TrailReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trail extends Model
{
    protected $fillable = [
        'osm_id', 'name', 'geometry', 'region',
        'user_id', 'location_id', 'mountain_name', 'trail_name', 'slug', 'price',
        'package_inclusions', 'difficulty', 'difficulty_description', 'duration',
        'best_season', 'terrain_notes', 'other_trail_notes', 'permit_required',
        'permit_process', 'departure_point', 'transport_options', 'side_trips',
        'packing_list', 'health_fitness', 'requirements', 'emergency_contacts',
        'campsite_info', 'guide_info', 'environmental_practices', 'customers_feedback',
        'testimonials_faqs', 'length', 'elevation_gain', 'elevation_high',
        'elevation_low', 'estimated_time', 'summary', 'description', 'features',
        'gpx_file', 'coordinates', 'latitude', 'longitude', 'is_active', 'coordinate_generation_method',
        'custom_start_point', 'custom_end_point', 'custom_waypoints', 'metrics_confidence',
        'activities'
    ];


    protected $casts = [
        'geometry' => 'array',
        'features' => 'array',
        'activities' => 'array',
        'coordinates' => 'array',
        'custom_start_point' => 'array',
        'custom_end_point' => 'array',
        'custom_waypoints' => 'array',
        'is_active' => 'boolean',
        'permit_required' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function images()
    {
        return $this->hasMany(TrailImage::class)->orderBy('sort_order');
    }

    public function reviews()
    {
        return $this->hasMany(TrailReview::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(TrailImage::class)->where('is_primary', true);
    }

    public function mapImage()
    {
        return $this->hasOne(TrailImage::class)->where('image_type', 'map');
    }

    // Accessors
    public function getDifficultyLabelAttribute()
    {
        // Map internal difficulty values to user-facing labels
        $map = [
            'beginner' => 'Easy',
            'intermediate' => 'Moderate',
            'advanced' => 'Challenging',
            // legacy/alternate values
            'hard' => 'Challenging',
            'difficult' => 'Challenging',
            'very_hard' => 'Challenging',
        ];

        $key = strtolower($this->difficulty ?? '');
        return $map[$key] ?? ucwords($this->difficulty);
    }

    public function getEstimatedTimeFormattedAttribute()
    {
        if (!$this->estimated_time) return null;
        $hours = floor($this->estimated_time / 60);
        $minutes = $this->estimated_time % 60;
        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeByOrganization($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}