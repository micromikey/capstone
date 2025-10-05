<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;


class User extends Authenticatable implements MustVerifyEmail
{


    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'organization_name',
        'organization_description',
        'approval_status',
        'approved_at',
        'profile_picture',
        'phone',
        'bio',
        'location',
        'birth_date',
        'gender',
        'hiking_preferences',
    'preferences_onboarded_at',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'birth_date' => 'date',
            'hiking_preferences' => 'array',
            'preferences_onboarded_at' => 'datetime',
        ];
    }

    /**
     * Determine if the user has verified their email address.
     * Organizations don't need email verification, only approval.
     */
    public function hasVerifiedEmail(): bool
    {
        if ($this->user_type === 'organization') {
            // Organizations don't need email verification, only approval
            return true;
        }
        
        // Hikers need email verification
        return ! is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        if ($this->user_type === 'organization') {
            // Organizations don't need email verification
            return true;
        }
        
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        if ($this->user_type === 'organization') {
            // Organizations don't need email verification
            return;
        }
        
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    /**
     * Get the organization profile associated with the user.
     */
    public function organizationProfile()
    {
        return $this->hasOne(OrganizationProfile::class);
    }

    /**
     * Get the organization's payment credentials
     */
    public function paymentCredentials()
    {
        return $this->hasOne(OrganizationPaymentCredential::class);
    }

    /**
     * Get the user's preferences
     */
    public function preferences()
    {
        return $this->hasOne(UserPreference::class);
    }

    /**
     * Get the user's notifications
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the user's unread notifications
     */
    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->unread()->orderBy('created_at', 'desc');
    }

    /**
     * Get the count of unread notifications
     */
    public function unreadNotificationsCount()
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Get the user's assessment results
     */
    public function assessmentResults()
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /**
     * Get the user's latest assessment result
     */
    public function latestAssessmentResult()
    {
        return $this->hasOne(AssessmentResult::class)->latestOfMany();
    }

    /**
     * Get the user's itineraries
     */
    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    /**
     * Get the user's latest itinerary
     */
    public function latestItinerary()
    {
        return $this->hasOne(Itinerary::class)->latestOfMany();
    }

    /**
     * Check if user is an approved organization
     */
    public function isApprovedOrganization()
    {
        return $this->user_type === 'organization' && $this->approval_status === 'approved';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->user_type === 'admin' || $this->email === 'admin@hikethere.com';
    }

    /**
     * Check if user is a pending organization
     */
    public function isPendingOrganization()
    {
        return $this->user_type === 'organization' && $this->approval_status === 'pending';
    }

    /**
     * Check if user is a rejected organization
     */
    public function isRejectedOrganization()
    {
        return $this->user_type === 'organization' && $this->approval_status === 'rejected';
    }

    /**
     * Check if user is a verified hiker
     */
    public function isVerifiedHiker()
    {
        return $this->user_type === 'hiker' && $this->hasVerifiedEmail();
    }

    /**
     * Get the approval status with human-readable text
     */
    public function getApprovalStatusTextAttribute()
    {
        return match($this->approval_status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Check if profile should be visible to a viewer
     * 
     * @param User|null $viewer The user viewing the profile (null for guests)
     * @return bool
     */
    public function isProfileVisibleTo($viewer = null)
    {
        $preferences = $this->preferences;
        
        // If no preferences set, default to public
        if (!$preferences) {
            return true;
        }
        
        $visibility = $preferences->profile_visibility ?? 'public';
        
        // Public profiles are visible to everyone
        if ($visibility === 'public') {
            return true;
        }
        
        // Private profiles are only visible to the owner
        if ($visibility === 'private') {
            return $viewer && $viewer->id === $this->id;
        }
        
        // Default to public
        return true;
    }

    /**
     * Check if a specific field should be shown on profile
     * 
     * @param string $field The field name (email, phone, location, birth_date, hiking_preferences)
     * @param User|null $viewer The user viewing the profile (null for guests)
     * @return bool
     */
    public function shouldShowField($field, $viewer = null)
    {
        // Owner can always see their own fields
        if ($viewer && $viewer->id === $this->id) {
            return true;
        }
        
        // If profile is not visible, hide all fields
        if (!$this->isProfileVisibleTo($viewer)) {
            return false;
        }
        
        $preferences = $this->preferences;
        
        // If no preferences, use defaults
        if (!$preferences) {
            $defaults = [
                'email' => false,
                'phone' => false,
                'location' => true,
                'birth_date' => false,
                'hiking_preferences' => true,
            ];
            return $defaults[$field] ?? false;
        }
        
        // Check the specific field preference
        $fieldMap = [
            'email' => 'show_email',
            'phone' => 'show_phone',
            'location' => 'show_location',
            'birth_date' => 'show_birth_date',
            'hiking_preferences' => 'show_hiking_preferences',
        ];
        
        $preferenceKey = $fieldMap[$field] ?? null;
        
        if (!$preferenceKey) {
            return false;
        }
        
        return $preferences->{$preferenceKey} ?? false;
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            // If using GCS, return GCS public URL (but check if GCS is actually configured)
            if (config('filesystems.default') === 'gcs') {
                try {
                    // Verify GCS disk is configured before using it
                    if (config('filesystems.disks.gcs.bucket')) {
                        return \Storage::disk('gcs')->url($this->profile_picture);
                    }
                } catch (\Exception $e) {
                    // If GCS fails, fall back to local storage
                    \Log::warning('GCS not available, falling back to local storage: ' . $e->getMessage());
                }
            }
            // Otherwise return local storage URL
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return default avatar based on user type
        if ($this->user_type === 'organization') {
            return asset('img/default-org-avatar.png');
        }
        
        return asset('img/default-hiker-avatar.png');
    }

    /**
     * Get the user's age
     */
    public function getAgeAttribute()
    {
        if ($this->birth_date) {
            return $this->birth_date->age;
        }
        return null;
    }

    /**
     * Check if user has completed profile
     */
    public function hasCompletedProfile()
    {
        if ($this->user_type === 'hiker') {
            return !empty($this->phone) && !empty($this->bio) && !empty($this->location);
        }
        
        return !empty($this->phone) && !empty($this->bio);
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletionPercentageAttribute()
    {
        if ($this->user_type === 'hiker') {
            $fields = ['name', 'email', 'phone', 'bio', 'location', 'profile_picture'];
            $completed = collect($fields)->filter(fn($field) => !empty($this->$field))->count();
            return round(($completed / count($fields)) * 100);
        }
        
        $fields = ['name', 'email', 'phone', 'bio', 'profile_picture'];
        $completed = collect($fields)->filter(fn($field) => !empty($this->$field))->count();
        return round(($completed / count($fields)) * 100);
    }

    // =================== Community Feature Relationships ===================

    /**
     * Organizations that this hiker is following
     * (Only applicable for hiker users)
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'hiker_id', 'organization_id')
            ->withTimestamps();
    }

    /**
     * Hikers that are following this organization
     * (Only applicable for organization users)
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follows', 'organization_id', 'hiker_id')
            ->withTimestamps();
    }

    /**
     * Check if this hiker is following a specific organization
     */
    public function isFollowing($organizationId)
    {
        if ($this->user_type !== 'hiker') {
            return false;
        }
        
        return $this->following()->where('organization_id', $organizationId)->exists();
    }

    /**
     * Follow an organization
     */
    public function followOrganization($organizationId)
    {
        if ($this->user_type !== 'hiker') {
            return false;
        }
        
        if ($this->isFollowing($organizationId)) {
            return false; // Already following
        }
        
        $this->following()->attach($organizationId);
        return true;
    }

    /**
     * Unfollow an organization
     */
    public function unfollowOrganization($organizationId)
    {
        if ($this->user_type !== 'hiker') {
            return false;
        }
        
        $this->following()->detach($organizationId);
        return true;
    }

    /**
     * Get trails from followed organizations
     * (Only applicable for hiker users)
     */
    public function followedOrganizationsTrails()
    {
        if ($this->user_type !== 'hiker') {
            return collect();
        }
        
        $followedOrgIds = $this->following()->pluck('users.id');
        return \App\Models\Trail::whereIn('user_id', $followedOrgIds)
            ->where('is_active', true)
            ->with(['user', 'location', 'primaryImage', 'reviews'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the user's trail reviews
     */
    public function trailReviews()
    {
        return $this->hasMany(\App\Models\TrailReview::class);
    }

    /**
     * Trails that the user has favorited
     */
    public function favoriteTrails()
    {
        return $this->belongsToMany(\App\Models\Trail::class, 'trail_favorites', 'user_id', 'trail_id')
                    ->withTimestamps();
    }

    /**
     * Get trails created by this organization
     * (Only applicable for organization users)
     */
    public function organizationTrails()
    {
        return $this->hasMany(\App\Models\Trail::class, 'user_id');
    }

    /**
     * Get the display name for the user
     * For organizations, returns organization_name; for hikers, returns name
     */
    public function getDisplayNameAttribute()
    {
        if ($this->user_type === 'organization') {
            return $this->organization_name ?? 'Unknown Organization';
        }
        
        return $this->name ?? 'Unknown User';
    }
}