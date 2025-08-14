<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationProfile extends Model
{
    protected $fillable = [
        'user_id',
        'organization_name',
        'organization_description',
        'email',
        'phone',
        'name',
        'address',
        'password',
        'business_permit_path',
        'government_id_path',
        'additional_docs',
        'profile_picture',
        'website',
        'social_media_facebook',
        'social_media_instagram',
        'social_media_twitter',
        'mission_statement',
        'services_offered',
        'operating_hours',
        'contact_person',
        'contact_position',
        'specializations',
        'founded_year',
        'team_size'
    ];

    protected $casts = [
        'additional_docs' => 'array',
        'specializations' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        
        return asset('img/default-org-avatar.png');
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletionPercentageAttribute()
    {
        $fields = ['organization_name', 'organization_description', 'email', 'phone', 'profile_picture', 'website', 'mission_statement'];
        $completed = collect($fields)->filter(fn($field) => !empty($this->$field))->count();
        return round(($completed / count($fields)) * 100);
    }
}