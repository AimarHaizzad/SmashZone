<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    public const DEFAULT_HOURLY_RATE = 20;

    protected $fillable = [
        'name',
        'description',
        'image',
        'owner_id',
        'status',
        'location',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function pricingRules()
    {
        return $this->hasMany(CourtPricingRule::class)->orderBy('start_time');
    }

    public function hourlyRateFor(CarbonInterface $dateTime): float
    {
        if (!$this->relationLoaded('pricingRules')) {
            $this->load('pricingRules');
        }

        $rule = $this->pricingRules->first(fn ($rule) => $rule->appliesTo($dateTime));

        return $rule?->price_per_hour ?? static::DEFAULT_HOURLY_RATE;
    }

    public function hourlyRateForSlot(string $date, string $time): float
    {
        $dateTime = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}");
        return $this->hourlyRateFor($dateTime);
    }

    public function calculatePriceForRange(string $date, string $startTime, string $endTime): float
    {
        $start = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$startTime}");
        $end = Carbon::createFromFormat('Y-m-d H:i', "{$date} {$endTime}");
        $total = 0;

        while ($start < $end) {
            $total += $this->hourlyRateFor($start);
            $start->addHour();
        }

        return $total;
    }
}
