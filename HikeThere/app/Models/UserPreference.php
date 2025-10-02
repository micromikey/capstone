<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_notifications',
        'push_notifications',
        'trail_updates',
        'security_alerts',
        'newsletter',
        'profile_visibility',
        'show_email',
        'show_phone',
        'show_location',
        'show_birth_date',
        'show_hiking_preferences',
        'two_factor_required',
        'timezone',
        'language',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'trail_updates' => 'boolean',
        'security_alerts' => 'boolean',
        'newsletter' => 'boolean',
        'show_email' => 'boolean',
        'show_phone' => 'boolean',
        'show_location' => 'boolean',
        'show_birth_date' => 'boolean',
        'show_hiking_preferences' => 'boolean',
        'two_factor_required' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get default preferences
    public static function getDefaults()
    {
        return [
            'email_notifications' => true,
            'push_notifications' => true,
            'trail_updates' => true,
            'security_alerts' => true,
            'newsletter' => false,
            'profile_visibility' => 'public',
            'show_email' => false,
            'show_phone' => false,
            'show_location' => true,
            'show_birth_date' => false,
            'show_hiking_preferences' => true,
            'two_factor_required' => false,
            'timezone' => 'Asia/Manila',
            'language' => 'en',
        ];
    }

    // Create or update preferences
    public static function updatePreferences($userId, $preferences)
    {
        return static::updateOrCreate(
            ['user_id' => $userId],
            $preferences
        );
    }

    // Check if user has specific notification enabled
    public function hasNotification($type)
    {
        return $this->{$type} ?? false;
    }

    // Get profile visibility level
    public function getProfileVisibilityAttribute($value)
    {
        return $value ?? 'public';
    }

    // Check if specific profile field should be shown
    public function shouldShowField($field)
    {
        $fieldMap = [
            'email' => 'show_email',
            'phone' => 'show_phone',
            'location' => 'show_location',
            'birth_date' => 'show_birth_date',
            'hiking_preferences' => 'show_hiking_preferences',
        ];

        return $this->{$fieldMap[$field] ?? $field} ?? false;
    }
}
