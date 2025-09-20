<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyReadiness extends Model
{
    use HasFactory;

    protected $fillable = [
        'trail_id', 'equipment_status', 'staff_availability',
        'communication_status', 'last_inspection_date'
    ];

    protected $casts = [
        'last_inspection_date' => 'date'
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }
}
