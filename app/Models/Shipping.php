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
            'pending' => 'bg-gray-100 text-gray-800',
            'preparing' => 'bg-yellow-100 text-yellow-800',
            'ready_for_pickup' => 'bg-blue-100 text-blue-800',
            'picked_up' => 'bg-indigo-100 text-indigo-800',
            'in_transit' => 'bg-purple-100 text-purple-800',
            'out_for_delivery' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'returned' => 'bg-pink-100 text-pink-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'preparing' => 'Preparing Order',
            'ready_for_pickup' => 'Ready for Pickup',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'failed' => 'Delivery Failed',
            'returned' => 'Returned',
            default => 'Unknown',
        };
    }

    public function getProgressPercentageAttribute()
    {
        $statuses = [
            'pending' => 0,
            'preparing' => 20,
            'ready_for_pickup' => 40,
            'picked_up' => 50,
            'in_transit' => 60,
            'out_for_delivery' => 80,
            'delivered' => 100,
            'failed' => 0,
            'returned' => 0,
        ];

        return $statuses[$this->status] ?? 0;
    }
}

