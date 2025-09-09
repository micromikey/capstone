<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrailSegment extends Model
{
    protected $fillable = [
        'segment_id',
        'original_way_id',
        'points_data',
        'intersection_start_id',
        'intersection_end_id',
        'distance_total',
        'bounding_box',
        'private_access',
        'bicycle_accessible',
        'highway_type',
        'name',
        'sac_scale',
        'trail_visibility',
        'surface',
        'width',
        'incline',
        'min_lat',
        'max_lat',
        'min_lng',
        'max_lng',
        'point_count'
    ];

    protected $casts = [
        'points_data' => 'array',
        'bounding_box' => 'array',
        'private_access' => 'boolean',
        'bicycle_accessible' => 'boolean',
        'distance_total' => 'decimal:4',
        'min_lat' => 'decimal:7',
        'max_lat' => 'decimal:7',
        'min_lng' => 'decimal:7',
        'max_lng' => 'decimal:7'
    ];

    /**
     * Get the starting intersection
     */
    public function startIntersection()
    {
        return $this->belongsTo(TrailIntersection::class, 'intersection_start_id');
    }

    /**
     * Get the ending intersection
     */
    public function endIntersection()
    {
        return $this->belongsTo(TrailIntersection::class, 'intersection_end_id');
    }

    /**
     * Get trails that use this segment
     */
    public function trails(): BelongsToMany
    {
        return $this->belongsToMany(Trail::class, 'trail_segment_usage')
                    ->withPivot(['segment_order', 'direction']);
    }

    /**
     * Scope to find segments within bounding box
     */
    public function scopeWithinBounds($query, float $minLat, float $maxLat, float $minLng, float $maxLng)
    {
        return $query->where('min_lat', '<=', $maxLat)
                    ->where('max_lat', '>=', $minLat)
                    ->where('min_lng', '<=', $maxLng)
                    ->where('max_lng', '>=', $minLng);
    }

    /**
     * Scope to find segments by highway type
     */
    public function scopeByHighwayType($query, string $type)
    {
        return $query->where('highway_type', $type);
    }

    /**
     * Scope to find hiking trails (exclude roads)
     */
    public function scopeHikingTrails($query)
    {
        return $query->whereIn('highway_type', ['path', 'track', 'footway', 'steps', 'bridleway']);
    }

    /**
     * Scope to exclude private access
     */
    public function scopePublicAccess($query)
    {
        return $query->where('private_access', false);
    }

    /**
     * Get difficulty based on SAC scale
     */
    public function getDifficultyAttribute(): string
    {
        $sacScale = $this->sac_scale;
        
        return match($sacScale) {
            'hiking', 'T1' => 'beginner',
            'mountain_hiking', 'T2' => 'intermediate', 
            'demanding_mountain_hiking', 'T3' => 'intermediate',
            'alpine_hiking', 'T4' => 'advanced',
            'demanding_alpine_hiking', 'T5' => 'advanced',
            'difficult_alpine_hiking', 'T6' => 'advanced',
            default => 'intermediate'
        };
    }

    /**
     * Get formatted distance
     */
    public function getFormattedDistanceAttribute(): string
    {
        if ($this->distance_total < 1) {
            return round($this->distance_total * 1000) . 'm';
        }
        return round($this->distance_total, 2) . 'km';
    }

    /**
     * Get the center point of the segment
     */
    public function getCenterPointAttribute(): array
    {
        $points = $this->points_data;
        $centerIndex = intval(count($points) / 2);
        
        return $points[$centerIndex] ?? $points[0];
    }

    /**
     * Get surface quality score (1-5)
     */
    public function getSurfaceQualityAttribute(): int
    {
        return match($this->surface) {
            'paved', 'asphalt', 'concrete' => 5,
            'gravel', 'fine_gravel' => 4,
            'ground', 'earth', 'dirt' => 3,
            'grass', 'unpaved' => 2,
            'sand', 'mud', 'rock' => 1,
            default => 3
        };
    }

    /**
     * Check if segment connects to another segment
     */
    public function connectsTo(TrailSegment $other): bool
    {
        return $this->intersection_end_id === $other->intersection_start_id ||
               $this->intersection_start_id === $other->intersection_end_id ||
               $this->intersection_end_id === $other->intersection_end_id ||
               $this->intersection_start_id === $other->intersection_start_id;
    }

    /**
     * Get elevation gain estimate (if available)
     */
    public function getEstimatedElevationGainAttribute(): ?int
    {
        // This would require elevation data - could be enhanced later
        if ($this->incline) {
            $inclinePercent = (float) str_replace('%', '', $this->incline);
            return round($this->distance_total * 1000 * ($inclinePercent / 100));
        }
        
        return null;
    }
}
