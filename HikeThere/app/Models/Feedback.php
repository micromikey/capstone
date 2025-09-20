<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'trail_id', 'rating', 'comment', 'experience_date'
    ];

    protected $casts = [
        'experience_date' => 'date',
        'rating' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }
}
