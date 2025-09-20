<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'fullname',
        'email',
        'phone',
        'emergency_contact',
        'trail',
        'trail_name',
        'hike_date',
        'participants',
        'total_amount',
        'payment_option',
        'payment_method',
        'status',
        'notes',
        'user_id', 'trail_id', 'booking_date', 
        'status', 'group_size', 'notes'

    ];

    protected $casts = [
        'hike_date' => 'date',
        'total_amount' => 'decimal:2',
        'participants' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'booking_date' => 'date'

    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    // Trail prices mapping
    const TRAIL_PRICES = [
        'mt_kulis' => 1500,
        'mt_mariglem' => 1500,
        'mt_tagapo' => 1500,
        'mt_batulao' => 1500,
        'mt_387' => 1500,
        'mt_pulag' => 4900,
        'mt_fato' => 3900,
        'mt_malindig' => 5900,
        'mt_guiting' => 6500,
        'mt_apo' => 7500,
    ];

    // Trail names mapping
    const TRAIL_NAMES = [
        'mt_kulis' => 'Mt. Kulis',
        'mt_mariglem' => 'Mt. Mariglem',
        'mt_tagapo' => 'Mt. Tagapo',
        'mt_batulao' => 'Mt. Batulao',
        'mt_387' => 'Mt. 387',
        'mt_pulag' => 'Mt. Pulag',
        'mt_fato' => 'Mt. Fato',
        'mt_malindig' => 'Mt. Malindig',
        'mt_guiting' => 'Mt. Guiting',
        'mt_apo' => 'Mt. Apo',
    ];

    // Boot method to auto-generate booking reference
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = static::generateBookingReference();
            }
            
            if (empty($booking->status)) {
                $booking->status = static::STATUS_PENDING;
            }

            // Set trail name from trail code
            if (!empty($booking->trail) && empty($booking->trail_name)) {
                $booking->trail_name = static::TRAIL_NAMES[$booking->trail] ?? $booking->trail;
            }

            // Calculate total amount
            if (empty($booking->total_amount) && !empty($booking->trail) && !empty($booking->participants)) {
                $price = static::TRAIL_PRICES[$booking->trail] ?? 0;
                $booking->total_amount = $price * $booking->participants;
            }
        });
    }

    // Generate unique booking reference
    public static function generateBookingReference()
    {
        $year = date('Y');
        $lastBooking = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastBooking ? intval(substr($lastBooking->booking_reference, -3)) + 1 : 1;
        
        return 'HK' . $year . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Accessor for formatted amount
    public function getFormattedTotalAmountAttribute()
    {
        return '₱' . number_format($this->total_amount, 0);
    }

    // Accessor for downpayment amount
    public function getDownpaymentAmountAttribute()
    {
        return ceil($this->total_amount * 0.5);
    }

    // Accessor for formatted downpayment
    public function getFormattedDownpaymentAttribute()
    {
        return '₱' . number_format($this->downpayment_amount, 0);
    }

    // Accessor for remaining balance
    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount - $this->downpayment_amount;
    }

    // Accessor for formatted remaining balance
    public function getFormattedRemainingBalanceAttribute()
    {
        return '₱' . number_format($this->remaining_balance, 0);
    }

    // Accessor for formatted hike date
    public function getFormattedHikeDateAttribute()
    {
        return $this->hike_date ? $this->hike_date->format('M j, Y') : null;
    }

    // Accessor for status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            static::STATUS_PENDING => 'yellow',
            static::STATUS_CONFIRMED => 'green',
            static::STATUS_CANCELLED => 'red',
            static::STATUS_COMPLETED => 'blue',
            default => 'gray'
        };
    }

    // Check if booking can be confirmed
    public function canBeConfirmed()
    {
        return $this->status === static::STATUS_PENDING;
    }

    // Check if booking can be cancelled
    public function canBeCancelled()
    {
        return in_array($this->status, [static::STATUS_PENDING, static::STATUS_CONFIRMED]);
    }

    // Check if booking can be completed
    public function canBeCompleted()
    {
        return $this->status === static::STATUS_CONFIRMED && $this->hike_date <= now();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', static::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', static::STATUS_CONFIRMED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', static::STATUS_CANCELLED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', static::STATUS_COMPLETED);
    }

    public function scopeByTrail($query, $trail)
    {
        return $query->where('trail', $trail);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('hike_date', $date);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('hike_date', '>=', now()->toDateString());
    }



      public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

}