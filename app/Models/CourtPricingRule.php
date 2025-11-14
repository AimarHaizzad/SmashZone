<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtPricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'court_id',
        'label',
        'start_time',
        'end_time',
        'day_of_week',
        'price_per_hour',
    ];

    protected $casts = [
        'price_per_hour' => 'float',
        'day_of_week' => 'integer',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function appliesTo(CarbonInterface $dateTime): bool
    {
        if (!is_null($this->day_of_week) && (int) $this->day_of_week !== $dateTime->dayOfWeek) {
            return false;
        }

        $time = $dateTime->format('H:i:s');
        $start = $this->start_time;
        $end = $this->end_time;

        if ($start === $end) {
            return true;
        }

        if ($start < $end) {
            return $time >= $start && $time < $end;
        }

        return $time >= $start || $time < $end;
    }
}


