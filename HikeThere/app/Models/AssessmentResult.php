<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'overall_score',
        'gear_score',
        'fitness_score',
        'health_score',
        'weather_score',
        'emergency_score',
        'environment_score',
        'readiness_level',
        'recommendations',
        'completed_at',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'emergency_contact_phone_alt',
    ];

    protected $casts = [
        'recommendations' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReadinessLevelColorAttribute()
    {
        return match(true) {
            str_contains($this->readiness_level, 'Excellent') => 'green',
            str_contains($this->readiness_level, 'Good') => 'blue',
            str_contains($this->readiness_level, 'Fair') => 'yellow',
            str_contains($this->readiness_level, 'Needs Improvement') => 'red',
            default => 'gray'
        };
    }

    public function getReadinessLevelIconAttribute()
    {
        return match(true) {
            str_contains($this->readiness_level, 'Excellent') => '🏔️',
            str_contains($this->readiness_level, 'Good') => '🥾',
            str_contains($this->readiness_level, 'Fair') => '⚠️',
            str_contains($this->readiness_level, 'Needs Improvement') => '🚨',
            default => '❓'
        };
    }
}
