<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viewpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'osm_id', 'name', 'latitude', 'longitude', 'raw_tags'
    ];

    protected $casts = [
        'raw_tags' => 'array'
    ];
}
