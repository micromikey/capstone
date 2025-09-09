<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mountain extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name', 'elevation', 'prominence', 'is_volcano', 'coordinate_string',
        'latitude', 'longitude', 'provinces', 'regions', 'island_group', 'alt_names',
        'style', 'source_properties'
    ];

    protected $casts = [
        'provinces' => 'array',
        'regions' => 'array',
        'alt_names' => 'array',
        'style' => 'array',
        'source_properties' => 'array',
        'is_volcano' => 'boolean'
    ];
}
