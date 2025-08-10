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
        'additional_docs'
    ];

    protected $casts = [
        'additional_docs' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}