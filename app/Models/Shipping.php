<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'tracking_number',
        'carrier',
        'notes',
        'estimated_delivery_date',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'estimated_delivery_date' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Generate unique tracking number
     */
    public static function generateTrackingNumber($carrier = null)
    {
        $prefix = $carrier ? strtoupper(substr($carrier, 0, 3)) : 'TRK';
        do {
            $trackingNumber = $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
        } while (self::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Accessors
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'preparing' => 'bg-yellow-100 text-yellow-800',
            'out_for_delivery' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'preparing' => 'Preparing',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getProgressPercentageAttribute()
    {
        $statuses = [
            'preparing' => 33,
            'out_for_delivery' => 66,
            'delivered' => 100,
            'cancelled' => 0,
        ];

        return $statuses[$this->status] ?? 0;
    }
}

