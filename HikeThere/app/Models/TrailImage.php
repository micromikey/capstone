<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Trail;
use App\Helpers\StorageHelper;

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
        return StorageHelper::url($this->image_path);
    }
}