<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'court_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'total_price',
        'payment_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }


    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    /**
     * Check if the booking is in the past
     */
    public function isPast()
    {
        $now = Carbon::now();
        $bookingDateTime = Carbon::parse($this->date . ' ' . $this->end_time);
        
        return $bookingDateTime->isPast();
    }

    /**
     * Check if the booking is completed
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the booking is active (confirmed and not past)
     */
    public function isActive()
    {
        return $this->status === 'confirmed' && !$this->isPast();
    }

    /**
     * Scope to get only active bookings
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'confirmed')
                    ->where(function ($q) {
                        $q->where('date', '>', Carbon::now()->format('Y-m-d'))
                          ->orWhere(function ($subQ) {
                              $subQ->where('date', '=', Carbon::now()->format('Y-m-d'))
                                   ->where('end_time', '>', Carbon::now()->format('H:i:s'));
                          });
                    });
    }

    /**
     * Scope to get only past bookings
     */
    public function scopePast($query)
    {
        return $query->where(function ($q) {
            $q->where('date', '<', Carbon::now()->format('Y-m-d'))
              ->orWhere(function ($subQ) {
                  $subQ->where('date', '=', Carbon::now()->format('Y-m-d'))
                       ->where('end_time', '<', Carbon::now()->format('H:i:s'));
              });
        });
    }
}
