<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;

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
     * Check if user is an approved organization
     */
    public function isApprovedOrganization()
    {
        return $this->user_type === 'organization' && $this->approval_status === 'approved';
    }

    /**
     * Check if user is a pending organization
     */
    public function isPendingOrganization()
    {
        return $this->user_type === 'organization' && $this->approval_status === 'pending';
    }

    /**
     * Check if user is a verified hiker
     */
    public function isVerifiedHiker()
    {
        return $this->user_type === 'hiker' && $this->hasVerifiedEmail();
    }
}