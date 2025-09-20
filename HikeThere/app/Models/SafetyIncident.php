<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'trail_id', 'reported_by', 'incident_type', 'severity',
        'description', 'incident_date', 'resolution_status'
    ];

    protected $casts = [
        'incident_date' => 'date'
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
