<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'content', 'status',
        'likes_count', 'comments_count'
    ];

    protected $casts = [
        'likes_count' => 'integer',
        'comments_count' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flags()
    {
        return $this->hasMany(ContentFlag::class, 'post_id');
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class, 'post_id');
    }
}