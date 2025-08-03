<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrailReview extends Model
{
    protected $fillable = [
        'trail_id', 'user_id', 'rating', 'review', 'hike_date', 'conditions'
    ];

    protected $casts = [
        'hike_date' => 'date',
        'conditions' => 'array',
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}