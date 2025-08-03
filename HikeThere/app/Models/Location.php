<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Trail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    protected $fillable = [
        'name', 'slug', 'province', 'region', 'country',
        'latitude', 'longitude', 'description', 'image'
    ];

    public function trails()
    {
        return $this->hasMany(Trail::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}