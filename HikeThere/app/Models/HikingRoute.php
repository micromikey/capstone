<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HikingRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'osm_id', 'name', 'distance_km', 'ascent', 'descent', 'raw_tags'
    ];

    protected $casts = [
        'raw_tags' => 'array'
    ];
}
