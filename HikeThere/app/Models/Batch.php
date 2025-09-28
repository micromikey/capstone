<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'trail_id',
        'trail_package_id',
        'event_id',
        'name',
        'capacity',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function trailPackage()
    {
        return $this->belongsTo(TrailPackage::class, 'trail_package_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
