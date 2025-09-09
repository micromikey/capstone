<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peak extends Model
{
    use HasFactory;

    protected $fillable = [
        'osm_id',
        'name',
        'latitude',
        'longitude',
        'elevation',
        'raw_tags'
    ];

    protected $casts = [
        'raw_tags' => 'array'
    ];
}
