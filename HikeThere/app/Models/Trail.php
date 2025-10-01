<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\TrailImage;
use App\Models\TrailReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\TrailPackage;

class Trail extends Model
{
    protected $fillable = [
    'osm_id', 'name', 'geometry', 'region',
    'user_id', 'location_id', 'mountain_name', 'trail_name', 'slug',
        // NOTE: the following package-related fields have been moved to the
        // `trail_packages` table and are accessed via the `package()` relation
        // through compatibility accessors below. They are intentionally not
        // included for mass-assignment here to avoid accidental writes when the
        // columns no longer exist on the `trails` table.
        'difficulty', 'difficulty_description',
        'best_season', 'terrain_notes', 'other_trail_notes',
        'packing_list', 'health_fitness', 'requirements', 'emergency_contacts',
        'campsite_info', 'guide_info', 'environmental_practices', 'customers_feedback',
        'testimonials_faqs', 'length', 'elevation_gain', 'elevation_high',
        'elevation_low', 'estimated_time', 'summary', 'description', 'features',
        'gpx_file', 'coordinates', 'latitude', 'longitude', 'is_active', 'coordinate_generation_method',
        'custom_start_point', 'custom_end_point', 'custom_waypoints', 'metrics_confidence',
        'activities',
        // Transportation and package-related fields were migrated to
        // `trail_packages` and should be accessed via $trail->package.*
        // (compatibility accessors are provided below).
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
        // package-related casts moved to TrailPackage to reflect column move
        'commute_legs' => 'array',
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

    /**
     * Users who favorited this trail
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(\App\Models\User::class, 'trail_favorites', 'trail_id', 'user_id')
                    ->withTimestamps();
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
        // `estimated_time` is stored as integer minutes. Return null when empty.
        if (!isset($this->estimated_time) || $this->estimated_time === null) {
            return null;
        }

        $mins = intval($this->estimated_time);
        if ($mins <= 0) return null;

        // Format into days/hours/minutes
        $days = intdiv($mins, 60 * 24);
        $remainder = $mins % (60 * 24);
        $hours = intdiv($remainder, 60);
        $minutes = $remainder % 60;

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
        }
        if ($hours > 0) {
            $parts[] = $hours . ' h';
        }
        if ($minutes > 0 && $days === 0) {
            // show minutes only when less than a day (avoid clutter like "1 day 0 h 30 m")
            $parts[] = $minutes . ' m';
        }

        return implode(' ', $parts);
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

    /**
     * Relation to the TrailPackage record that holds package-specific fields.
     * This allows existing views/controllers that read $trail->price, etc.
     * to continue working by proxying to the related package when the
     * original columns have been moved.
     */
    public function package()
    {
        return $this->hasOne(TrailPackage::class);
    }

    // Compatibility accessors: return the package value when the Trail
    // column is absent (null). This keeps existing code working without
    // changing every view/controller at once.
    public function getPriceAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->price ?? null;
    }

    public function getPackageInclusionsAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->package_inclusions ?? null;
    }

    public function getDurationAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->duration ?? null;
    }

    public function getPermitRequiredAttribute($value)
    {
        if (!is_null($value)) return (bool)$value;
        return (bool)($this->package?->permit_required ?? false);
    }

    public function getPermitProcessAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->permit_process ?? null;
    }

    public function getTransportIncludedAttribute($value)
    {
        // Prefer package value when available (package data is authoritative)
        if ($this->package && isset($this->package->transport_included)) {
            return (bool)$this->package->transport_included;
        }
        // Fallback to trail's direct value
        if (!is_null($value)) return (bool)$value;
        return false;
    }

    public function getTransportDetailsAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->transport_details ?? null;
    }

    public function getTransportationDetailsAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->transportation_details ?? null;
    }

    public function getCommuteLegsAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->commute_legs ?? null;
    }

    public function getCommuteSummaryAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->commute_summary ?? null;
    }

    public function getSideTripsAttribute($value)
    {
        if (!is_null($value)) return $value;
        return $this->package?->side_trips ?? null;
    }
}