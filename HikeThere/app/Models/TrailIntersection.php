<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrailIntersection extends Model
{
    protected $fillable = [
        'latitude',
        'longitude',
        'connected_ways',
        'connected_segments',
        'connection_count',
        'intersection_type'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'connected_ways' => 'array',
        'connected_segments' => 'array'
    ];

    /**
     * Get segments that start at this intersection
     */
    public function startingSegments(): HasMany
    {
        return $this->hasMany(TrailSegment::class, 'intersection_start_id');
    }

    /**
     * Get segments that end at this intersection
     */
    public function endingSegments(): HasMany
    {
        return $this->hasMany(TrailSegment::class, 'intersection_end_id');
    }

    /**
     * Get all segments connected to this intersection
     */
    public function getAllConnectedSegments()
    {
        return TrailSegment::where('intersection_start_id', $this->id)
                          ->orWhere('intersection_end_id', $this->id)
                          ->get();
    }

    /**
     * Scope to find intersections near a point
     */
    public function scopeNear($query, float $lat, float $lng, float $radiusKm = 0.1)
    {
        // Simple bounding box search (for more precision, use spatial functions)
        $latDelta = $radiusKm / 111; // Approximately 111km per degree
        $lngDelta = $radiusKm / (111 * cos(deg2rad($lat)));
        
        return $query->whereBetween('latitude', [$lat - $latDelta, $lat + $latDelta])
                    ->whereBetween('longitude', [$lng - $lngDelta, $lng + $lngDelta]);
    }

    /**
     * Get intersection type based on connection count
     */
    public function getTypeAttribute(): string
    {
        return match($this->connection_count) {
            1 => 'endpoint',
            2 => 'waypoint',
            3, 4 => 'junction',
            default => 'complex_junction'
        };
    }

    /**
     * Check if this is a major trail junction
     */
    public function isMajorJunction(): bool
    {
        return $this->connection_count >= 3;
    }

    /**
     * Get formatted coordinates
     */
    public function getFormattedCoordinatesAttribute(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Calculate distance to another point
     */
    public function distanceTo(float $lat, float $lng): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $deltaLat = deg2rad($lat - $this->latitude);
        $deltaLng = deg2rad($lng - $this->longitude);
        
        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) *
             sin($deltaLng / 2) * sin($deltaLng / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
