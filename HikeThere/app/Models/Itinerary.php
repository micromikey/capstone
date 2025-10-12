<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'trail_name',
        'user_location',
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
        'route_coordinates',
        'daily_schedule',
        'transport_details',
        'departure_info',
        'arrival_info',
        'route_data',
        'route_summary',
        'created_at',
        'share_token',
    ];

    protected $casts = [
        'gear_recommendations' => 'array',
        'safety_tips' => 'array',
        'waypoints' => 'array',
        'emergency_contacts' => 'array',
        'schedule' => 'array',
        'stopovers' => 'array',
        'sidetrips' => 'array',
        'route_coordinates' => 'array',
        'daily_schedule' => 'array',
        'transport_details' => 'array',
        'departure_info' => 'array',
        'arrival_info' => 'array',
        'route_data' => 'array',
        'route_summary' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Boot method to auto-generate share token
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($itinerary) {
            if (empty($itinerary->share_token)) {
                $itinerary->share_token = static::generateUniqueShareToken();
            }
        });
    }

    /**
     * Generate a unique share token
     */
    protected static function generateUniqueShareToken()
    {
        do {
            $token = Str::random(32); // Generate a 32-character random string
        } while (static::where('share_token', $token)->exists());

        return $token;
    }

    /**
     * Regenerate the share token (useful for security/privacy)
     */
    public function regenerateShareToken()
    {
        $this->share_token = static::generateUniqueShareToken();
        $this->save();
        
        return $this->share_token;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function trail()
    {
        return $this->belongsTo(Trail::class, 'trail_id');
    }

    public function days()
    {
        return $this->hasMany(ItineraryDay::class, 'itinerary_id')->orderBy('day_index');
    }

    public function activities()
    {
        return $this->hasManyThrough(ItineraryActivity::class, ItineraryDay::class, 'itinerary_id', 'itinerary_day_id', 'id', 'id');
    }

    public function getDifficultyColorAttribute()
    {
        return match ($this->difficulty_level) {
            'Easy' => 'green',
            'Moderate' => 'blue',
            'Hard' => 'orange',
            'Expert' => 'red',
            default => 'gray'
        };
    }

    public function getDifficultyIconAttribute()
    {
        return match ($this->difficulty_level) {
            'Easy' => '🟢',
            'Moderate' => '🔵',
            'Hard' => '🟠',
            'Expert' => '🔴',
            default => '⚪'
        };
    }

    /**
     * Get formatted departure information
     */
    public function getFormattedDepartureAttribute()
    {
        if (! $this->departure_info) {
            return null;
        }

        $date = \Carbon\Carbon::parse($this->departure_info['date'] ?? $this->schedule['date'] ?? '');
        $time = $this->departure_info['time'] ?? $this->schedule['start_time'] ?? '';

        return [
            'date_formatted' => $date->format('M d, Y'),
            'time_formatted' => $time,
            'full_datetime' => $date->format('M d, Y').' – '.$time,
            'location' => $this->departure_info['location'] ?? $this->user_location,
            'coordinates' => $this->departure_info['coordinates'] ?? null,
        ];
    }

    /**
     * Get formatted arrival information
     */
    public function getFormattedArrivalAttribute()
    {
        if (! $this->arrival_info) {
            return null;
        }

        return [
            'trail_name' => $this->arrival_info['trail_name'] ?? $this->trail_name,
            'location' => $this->arrival_info['location'] ?? 'Trail destination',
            'coordinates' => $this->arrival_info['coordinates'] ?? null,
            'difficulty' => $this->arrival_info['difficulty'] ?? $this->difficulty_level,
        ];
    }

    /**
     * Get daily schedule with weather and conditions
     */
    public function getDailyScheduleWithWeatherAttribute()
    {
        if (! $this->daily_schedule) {
            return [];
        }

        return collect($this->daily_schedule)->map(function ($day, $index) {
            return [
                'day_number' => $index + 1,
                'day_label' => $day['day_label'] ?? 'Day '.($index + 1),
                'date' => $day['date'] ?? '',
                'activities' => collect($day['activities'] ?? [])->map(function ($activity) {
                    return [
                        'time' => $activity['time'] ?? '',
                        'location' => $activity['location'] ?? '',
                        'condition' => $activity['condition'] ?? '',
                        'temperature' => $activity['temperature'] ?? '',
                        'note' => $activity['note'] ?? '',
                        'coordinates' => $activity['coordinates'] ?? null,
                        'transport_mode' => $activity['transport_mode'] ?? null,
                        'duration' => $activity['duration'] ?? null,
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    /**
     * Get route summary for display
     */
    public function getRouteSummaryAttribute()
    {
        $departure = $this->formatted_departure;
        $arrival = $this->formatted_arrival;

        if (! $departure || ! $arrival) {
            return null;
        }

        // Get the actual route data from the new fields
        $routeData = $this->daily_schedule[0]['route_data'] ?? null;

        return [
            'departure' => $departure['full_datetime'].' from '.$departure['location'],
            'destination' => $arrival['trail_name'].' ('.$arrival['difficulty'].') at '.$arrival['location'],
            'total_distance' => $routeData['total_distance_km'] ?? $routeData['total_distance'] ?? $this->distance ?? 'Distance TBD',
            'estimated_duration' => $routeData['total_duration_hours'] ?? $routeData['total_duration'] ?? $this->estimated_duration ?? 'Duration TBD',
        ];
    }
}
