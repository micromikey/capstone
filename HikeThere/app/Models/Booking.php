<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        'participants',
        'status',
        'notes',
        'price_cents',
        'payment_proof_path',
        'transaction_number',
        'payment_notes',
        'payment_status',
        'payment_method_used',
        'payment_verified_at',
        'payment_verified_by',
    ];

    protected $casts = [
        'participants' => 'array',
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

    /**
     * Check if booking uses manual payment
     */
    public function usesManualPayment(): bool
    {
        return $this->payment_method_used === 'manual';
    }

    /**
     * Check if payment is pending verification
     */
    public function isPaymentPendingVerification(): bool
    {
        return $this->usesManualPayment() && $this->payment_status === 'pending';
    }

    /**
     * Check if payment has been verified
     */
    public function isPaymentVerified(): bool
    {
        return $this->usesManualPayment() && $this->payment_status === 'verified';
    }

    /**
     * Check if payment was rejected
     */
    public function isPaymentRejected(): bool
    {
        return $this->usesManualPayment() && $this->payment_status === 'rejected';
    }

    /**
     * Get the organization that owns this booking's trail
     */
    public function organization()
    {
        return $this->trail ? $this->trail->organization() : null;
    }

    /**
     * Verify payment (mark as verified by organization)
     */
    public function verifyPayment($verifiedBy): void
    {
        $this->update([
            'payment_status' => 'verified',
            'payment_verified_at' => now(),
            'payment_verified_by' => $verifiedBy,
            'status' => 'confirmed', // Confirm the booking
        ]);

        // Reserve slots now that payment is verified
        if ($this->batch) {
            $this->batch->reserveSlots($this->party_size);
        }
    }

    /**
     * Reject payment (mark as rejected by organization)
     */
    public function rejectPayment(): void
    {
        $this->update([
            'payment_status' => 'rejected',
        ]);
    }

    /**
     * Get the full URL for the payment proof image
     */
    public function getPaymentProofUrl(): ?string
    {
        if (empty($this->payment_proof_path)) {
            return null;
        }

        try {
            $disk = config('filesystems.default', 'public');
            return Storage::disk($disk)->url($this->payment_proof_path);
        } catch (\Exception $e) {
            Log::error('Failed to get payment proof URL', [
                'booking_id' => $this->id,
                'path' => $this->payment_proof_path,
                'error' => $e->getMessage()
            ]);
            // Fallback to asset helper
            return asset('storage/' . $this->payment_proof_path);
        }
    }
}
