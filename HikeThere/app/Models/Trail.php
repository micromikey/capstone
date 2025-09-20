<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Location;
use App\Models\TrailImage;
use App\Models\TrailReview;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trail extends Model
{
    protected $fillable = [
        'location_id', 'name', 'slug', 'difficulty', 'length',
        'elevation_gain', 'elevation_high', 'elevation_low',
        'estimated_time', 'summary', 'description', 'features',
        'gpx_file', 'coordinates', 'is_active',
        'name', 'description', 
        'region_id', 'distance', 'elevation_gain'
    ];


    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function safetyIncidents()
    {
        return $this->hasMany(SafetyIncident::class);
    }

    public function emergencyReadiness()
    {
        return $this->hasOne(EmergencyReadiness::class);
    }

    

    protected $casts = [
        'features' => 'array',
        'coordinates' => 'array',
        'is_active' => 'boolean',
    ];


    
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
        return ucwords(str_replace('-', ' ', $this->difficulty));
    }

    public function getEstimatedTimeFormattedAttribute()
    {
        $hours = floor($this->estimated_time / 60);
        $minutes = $this->estimated_time % 60;
        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
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
}