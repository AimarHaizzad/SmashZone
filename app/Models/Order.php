<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'payment_id',
        'total_amount',
        'status',
        'delivery_method',
        'delivery_address',
        'delivery_city',
        'delivery_postcode',
        'delivery_state',
        'delivery_phone',
        'notes',
        'received_at',
        'return_requested_at',
        'return_reason',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'received_at' => 'datetime',
        'return_requested_at' => 'datetime',
    ];

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    /**
     * Accessors
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'processing' => 'bg-purple-100 text-purple-800',
            'shipped' => 'bg-indigo-100 text-indigo-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'return_requested' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getFormattedTotalAttribute()
    {
        return 'RM ' . number_format($this->total_amount, 2);
    }

    public function getDeliveryMethodLabelAttribute()
    {
        return match($this->delivery_method) {
            'pickup' => 'Self Pickup',
            'delivery' => 'Home Delivery',
            default => 'Unknown',
        };
    }

    public function getFullDeliveryAddressAttribute()
    {
        if ($this->delivery_method === 'pickup') {
            return 'Self Pickup';
        }

        $parts = array_filter([
            $this->delivery_address,
            $this->delivery_postcode,
            $this->delivery_city,
            $this->delivery_state,
        ]);

        return implode(', ', $parts);
    }
}

