<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'trail_name',
        'difficulty_level',
        'estimated_duration',
        'distance',
        'elevation_gain',
        'best_time_to_hike',
        'weather_conditions',
        'gear_recommendations',
        'safety_tips',
        'route_description',
        'waypoints',
        'emergency_contacts',
        'schedule',
        'stopovers',
        'sidetrips',
        'transportation',
        'created_at',
    ];

    protected $casts = [
        'gear_recommendations' => 'array',
        'safety_tips' => 'array',
        'waypoints' => 'array',
        'emergency_contacts' => 'array',
        'schedule' => 'array',
        'stopovers' => 'array',
        'sidetrips' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDifficultyColorAttribute()
    {
        return match($this->difficulty_level) {
            'Easy' => 'green',
            'Moderate' => 'blue',
            'Hard' => 'orange',
            'Expert' => 'red',
            default => 'gray'
        };
    }

    public function getDifficultyIconAttribute()
    {
        return match($this->difficulty_level) {
            'Easy' => '🟢',
            'Moderate' => '🔵',
            'Hard' => '🟠',
            'Expert' => '🔴',
            default => '⚪'
        };
    }
}
