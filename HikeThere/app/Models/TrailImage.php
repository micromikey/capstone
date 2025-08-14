<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Trail;

class TrailImage extends Model
{
    protected $fillable = [
        'trail_id', 'image_path', 'image_type', 'caption', 'sort_order', 'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function getUrlAttribute()
    {
        // Check if it's already a full URL (external image)
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        
        // Otherwise, it's a local file
        return Storage::url($this->image_path);
    }
}