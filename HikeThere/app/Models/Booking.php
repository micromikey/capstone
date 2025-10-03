<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'trail_id',
        'batch_id',
        'event_id',
        'date',
        'party_size',
        'status',
        'notes',
        'price_cents',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class);
    }

    /**
     * Get the payment associated with this booking
     */
    public function payment()
    {
        return $this->hasOne(BookingPayment::class);
    }

    /**
     * Check if booking has been paid
     */
    public function isPaid(): bool
    {
        return $this->payment && $this->payment->isPaid();
    }

    /**
     * Check if booking payment is pending
     */
    public function hasPaymentPending(): bool
    {
        return $this->payment && $this->payment->isPending();
    }

    /**
     * Get the total amount for this booking (price_cents converted to pesos)
     */
    public function getAmountInPesos(): int
    {
        // If price_cents is already set, use it
        if ($this->price_cents) {
            return (int) ($this->price_cents / 100);
        }
        
        // Otherwise calculate from trail price × party size
        if ($this->trail && $this->trail->price) {
            return (int) ($this->trail->price * $this->party_size);
        }
        
        return 0;
    }

    /**
     * Get the total amount in cents (for storage)
     */
    public function calculatePriceCents(): int
    {
        if ($this->trail && $this->trail->price) {
            // Trail price is in pesos, multiply by party size and convert to cents
            return (int) ($this->trail->price * $this->party_size * 100);
        }
        
        return 0;
    }

    /**
     * Cancel booking and release reserved slots
     */
    public function cancel(): void
    {
        // Only release slots if booking was confirmed (payment was successful)
        if ($this->status === 'confirmed' && $this->batch) {
            $this->batch->releaseSlots($this->party_size);
        }
        
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        // Can't cancel if already cancelled
        if ($this->status === 'cancelled') {
            return false;
        }
        
        // Can't cancel if the event/batch has already started
        if ($this->batch && $this->batch->starts_at <= now()) {
            return false;
        }
        
        return true;
    }
}
