<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'trail_id',
        'trail_package_id',
        'event_id',
        'name',
        'capacity',
        'slots_taken',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function trailPackage()
    {
        return $this->belongsTo(TrailPackage::class, 'trail_package_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the number of available slots
     */
    public function getAvailableSlots(): int
    {
        return max(0, $this->capacity - $this->slots_taken);
    }

    /**
     * Check if batch has enough available slots
     */
    public function hasAvailableSlots(int $needed = 1): bool
    {
        return $this->getAvailableSlots() >= $needed;
    }

    /**
     * Check if batch is full
     */
    public function isFull(): bool
    {
        return $this->slots_taken >= $this->capacity;
    }

    /**
     * Reserve slots (deduct from available)
     * Returns true if successful, false if not enough slots
     */
    public function reserveSlots(int $count): bool
    {
        if (!$this->hasAvailableSlots($count)) {
            return false;
        }

        $this->increment('slots_taken', $count);
        return true;
    }

    /**
     * Release slots (add back to available)
     * Used when booking is cancelled or payment fails
     */
    public function releaseSlots(int $count): void
    {
        // Ensure we don't go below zero
        $newSlotsTaken = max(0, $this->slots_taken - $count);
        $this->update(['slots_taken' => $newSlotsTaken]);
    }

    /**
     * Get percentage of slots filled
     */
    public function getOccupancyPercentage(): int
    {
        if ($this->capacity == 0) {
            return 0;
        }

        return (int) (($this->slots_taken / $this->capacity) * 100);
    }
}
