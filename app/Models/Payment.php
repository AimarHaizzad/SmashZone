<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'booking_id',
        'amount',
        'status',
        'payment_date',
        'stripe_session_id',
        'stripe_payment_intent_id',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'payment_id');
    }


    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedAmountAttribute()
    {
        return 'RM ' . number_format($this->amount, 2);
    }

    public function getPaymentMethodAttribute()
    {
        if ($this->stripe_payment_intent_id) {
            return 'Stripe (Card/FPX)';
        }
        return 'Manual';
    }
}
