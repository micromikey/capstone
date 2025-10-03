<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','title','slug','description','start_at','end_at','trail_id','capacity','is_public',
        'duration','always_available','batch_count','location_name','price','is_free'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_public' => 'boolean',
        'always_available' => 'boolean',
        'batch_count' => 'integer',
        'is_free' => 'boolean',
        'price' => 'decimal:2',
    ];

    public static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . uniqid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    /**
     * Batches created for this event (manual or generated)
     */
    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Get all bookings for this event's trail
     */
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Trail::class, 'id', 'trail_id', 'trail_id', 'id');
    }
}
