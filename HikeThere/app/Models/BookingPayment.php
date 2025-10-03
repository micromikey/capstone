<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingPayment extends Model
{
    protected $table = 'booking_payments';

    protected $fillable = [
        'booking_id',
        'user_id',
        'fullname',
        'email',
        'phone',
        'mountain',
        'amount',
        'hike_date',
        'participants',
        'paymongo_link_id',
        'paymongo_payment_id',
        'payment_status',
        'paid_at'
    ];

    protected $casts = [
        'hike_date' => 'date',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the booking associated with this payment
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who made this payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payment is completed
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(string $paymentId = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'paymongo_payment_id' => $paymentId ?? $this->paymongo_payment_id,
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'payment_status' => 'failed',
        ]);
    }
}

