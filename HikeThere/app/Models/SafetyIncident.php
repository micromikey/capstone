<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyIncident extends Model
{
    use HasFactory;

    protected $table = 'safety_incidents';

    protected $fillable = [
        'trail_id',
        'organization_id',
        'reported_by',
        'incident_type',
        'severity',
        'status',
        'location',
        'description',
        'incident_date',
        'incident_time',
        'occurred_at',
        'resolved_at',
        'resolution_notes',
        'affected_parties',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'resolved_at' => 'datetime',
        'affected_parties' => 'array',
    ];

    // Legacy constants (capitalized - for backward compatibility)
    const SEVERITY_CRITICAL = 'Critical';
    const SEVERITY_HIGH = 'High';
    const SEVERITY_MEDIUM = 'Medium';
    const SEVERITY_LOW = 'Low';

    const STATUS_OPEN = 'Open';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_RESOLVED = 'Resolved';
    const STATUS_CLOSED = 'Closed';
    
    // New lowercase constants (preferred for hiker reports)
    const SEVERITY_CRITICAL_LC = 'critical';
    const SEVERITY_HIGH_LC = 'high';
    const SEVERITY_MEDIUM_LC = 'medium';
    const SEVERITY_LOW_LC = 'low';
    
    const STATUS_REPORTED = 'reported';
    const STATUS_OPEN_LC = 'open';
    const STATUS_IN_PROGRESS_LC = 'in progress';
    const STATUS_RESOLVED_LC = 'resolved';
    const STATUS_CLOSED_LC = 'closed';

    /**
     * Get the trail this incident belongs to
     */
    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    /**
     * Get the organization this incident belongs to
     */
    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    /**
     * Get the user who reported this incident
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Get severity badge color
     */
    public function getSeverityBadgeColorAttribute()
    {
        switch ($this->severity) {
            case self::SEVERITY_CRITICAL:
                return 'bg-red-600 text-white';
            case self::SEVERITY_HIGH:
                return 'bg-orange-500 text-white';
            case self::SEVERITY_MEDIUM:
                return 'bg-yellow-500 text-white';
            case self::SEVERITY_LOW:
                return 'bg-green-500 text-white';
            default:
                return 'bg-gray-500 text-white';
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_OPEN:
                return 'bg-red-100 text-red-800';
            case self::STATUS_IN_PROGRESS:
                return 'bg-blue-100 text-blue-800';
            case self::STATUS_RESOLVED:
                return 'bg-green-100 text-green-800';
            case self::STATUS_CLOSED:
                return 'bg-gray-100 text-gray-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Check if incident is resolved
     */
    public function isResolved()
    {
        return in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }

    /**
     * Get days since occurred
     */
    public function getDaysSinceOccurredAttribute()
    {
        if (!$this->occurred_at) {
            return null;
        }
        return $this->occurred_at->diffInDays(now());
    }

    /**
     * Scope for open incidents
     */
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    /**
     * Scope for critical incidents
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', self::SEVERITY_CRITICAL);
    }
}
