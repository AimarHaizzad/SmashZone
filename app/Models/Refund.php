<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'payment_id',
        'booking_id',
        'user_id',
        'amount',
        'status',
        'stripe_refund_id',
        'reason',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedAmountAttribute()
    {
        return 'RM ' . number_format($this->amount, 2);
    }

    public function getRefundMethodAttribute()
    {
        if ($this->stripe_refund_id) {
            return 'Stripe (Original Payment Method)';
        }
        return 'Manual';
    }
}
