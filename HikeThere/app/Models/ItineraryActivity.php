<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryActivity extends Model
{
    use HasFactory;

    protected $table = 'itinerary_activities';

    protected $fillable = [
        'itinerary_day_id',
        'order',
        'minutes_offset',
        'title',
        'description',
        'location',
        'type',
        'transport',
        'weather',
        'notes',
        'meta',
    ];

    protected $casts = [
        'transport' => 'array',
        'weather' => 'array',
        'notes' => 'array',
        'meta' => 'array',
    ];

    public function day()
    {
        return $this->belongsTo(ItineraryDay::class, 'itinerary_day_id');
    }
}
