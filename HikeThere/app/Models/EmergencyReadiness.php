<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyReadiness extends Model
{
    use HasFactory;

    protected $table = 'emergency_readiness';

    protected $fillable = [
        'trail_id',
        'organization_id',
        'equipment_status',
        'staff_availability',
        'communication_status',
        'first_aid_score',
        'equipment_score',
        'staff_training_score',
        'emergency_access_score',
        'overall_score',
        'readiness_level',
        'equipment_notes',
        'staff_notes',
        'communication_notes',
        'recommendations',
        'comments',
        'assessed_by',
        'submitted_by',
        'assessment_date',
    ];

    protected $casts = [
        'equipment_status' => 'integer',
        'staff_availability' => 'integer',
        'communication_status' => 'integer',
        'first_aid_score' => 'integer',
        'equipment_score' => 'integer',
        'staff_training_score' => 'integer',
        'emergency_access_score' => 'integer',
        'overall_score' => 'integer',
        'assessment_date' => 'datetime',
    ];

    /**
     * Get the trail this assessment belongs to
     */
    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    /**
     * Get the organization this assessment belongs to
     */
    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    /**
     * Get the user who assessed this (organization assessment)
     */
    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    /**
     * Get the user who submitted this (hiker feedback)
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Calculate overall readiness score
     */
    public function getOverallScoreAttribute()
    {
        return round(($this->equipment_status + $this->staff_availability + $this->communication_status) / 3, 2);
    }

    /**
     * Get readiness level based on score
     */
    public function getReadinessLevelAttribute()
    {
        $score = $this->overall_score;
        
        if ($score >= 85) return 'Excellent';
        if ($score >= 70) return 'Good';
        if ($score >= 50) return 'Fair';
        return 'Needs Improvement';
    }

    /**
     * Get badge color based on readiness level
     */
    public function getReadinessBadgeColorAttribute()
    {
        switch ($this->readiness_level) {
            case 'Excellent':
                return 'bg-green-100 text-green-800';
            case 'Good':
                return 'bg-blue-100 text-blue-800';
            case 'Fair':
                return 'bg-yellow-100 text-yellow-800';
            case 'Needs Improvement':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }
}
