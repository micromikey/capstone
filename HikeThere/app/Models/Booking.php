<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trail_id',
        'batch_id',
        'event_id',
        'date',
        'party_size',
        'status',
        'notes',
        'price_cents',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class);
    }
}
