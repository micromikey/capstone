<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'trail_id',
        'price',
        'package_inclusions',
        'duration',
        'permit_required',
        'permit_process',
        'transport_included',
        'transport_details',
        'transportation_details',
        'commute_legs',
        'commute_summary',
        'side_trips',
    // new structured fields
    'package_inclusions_json',
    'side_trips_json',
        // schedule/time fields
        'opening_time',
        'closing_time',
        'pickup_time',
        'departure_time',
    ];

    /**
     * Casts to ensure attributes return the expected types when accessed
     * via the parent `Trail` model's compatibility accessors.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'permit_required' => 'boolean',
        'transport_included' => 'boolean',
        'commute_legs' => 'array',
        'package_inclusions_json' => 'array',
        'side_trips_json' => 'array',
        // times stored as string (HH:MM) by default; change to datetime if needed
        'opening_time' => 'string',
        'closing_time' => 'string',
        'pickup_time' => 'string',
        'departure_time' => 'string',
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }
}
