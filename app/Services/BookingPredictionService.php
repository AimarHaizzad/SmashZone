<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingPredictionService
{
    /**
     * Predict booking trends for the next 7 days
     */
    public function predictWeeklyTrends($ownerId)
    {
        // Get historical data for analysis
        $historicalData = $this->getHistoricalData($ownerId);
        
        // Check if we have enough data (at least 7 days of booking data)
        if ($historicalData['daily_bookings']->count() < 7) {
            return $this->getEmptyPredictionData();
        }
        
        // Analyze patterns
        $patterns = $this->analyzePatterns($historicalData);
        
        // Generate predictions for next 7 days
        $predictions = $this->generatePredictions($patterns);
        
        // Calculate confidence scores
        $confidence = $this->calculateConfidence($historicalData, $patterns);
        
        // Generate recommendations
        $recommendations = $this->generateRecommendations($predictions, $patterns);
        
        return [
            'predictions' => $predictions,
            'confidence' => $confidence,
            'recommendations' => $recommendations,
            'patterns' => $patterns,
            'historical_data' => $historicalData
        ];
    }
    
    /**
     * Get empty prediction data structure
     */
    private function getEmptyPredictionData()
    {
        $predictions = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i + 1);
            $predictions[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('l'),
                'day_of_week' => $date->dayOfWeek,
                'predicted_bookings' => 0,
                'revenue_estimate' => 0,
                'confidence' => 0,
                'is_peak_day' => false,
                'is_low_day' => false,
                'peak_hours' => [],
                'recommendations' => []
            ];
        }

        return [
            'predictions' => $predictions,
            'confidence' => [
                'overall' => 0,
                'data_quality' => 0,
                'pattern_consistency' => 0,
                'data_points' => 0
            ],
            'recommendations' => [
                [
                    'type' => 'info',
                    'title' => 'No Data Available',
                    'description' => 'Insufficient historical data to generate predictions.',
                    'actions' => ['Continue booking courts to build prediction data']
                ]
            ],
            'patterns' => [],
            'historical_data' => []
        ];
    }

    /**
     * Get historical booking data for analysis
     */
    private function getHistoricalData($ownerId)
    {
        // Get data from last 3 months
        $startDate = Carbon::now()->subMonths(3);
        $driver = DB::getDriverName();
        
        // Database-specific functions
        $dateFunction = $driver === 'pgsql' 
            ? 'bookings.date::date' 
            : 'DATE(bookings.date)';
        $dayOfWeekFunction = $driver === 'pgsql' 
            ? 'EXTRACT(DOW FROM bookings.date) + 1' 
            : 'DAYOFWEEK(bookings.date)';
        $hourFunction = $driver === 'pgsql' 
            ? 'EXTRACT(HOUR FROM bookings.start_time::time)' 
            : 'HOUR(bookings.start_time)';
        $paymentDateFunction = $driver === 'pgsql' 
            ? 'payments.payment_date::date' 
            : 'DATE(payments.payment_date)';
        
        // Daily booking counts
        $dailyBookings = Booking::whereHas('court', function($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
        ->where('date', '>=', $startDate)
        ->selectRaw("{$dateFunction} as booking_date, COUNT(*) as booking_count, {$dayOfWeekFunction} as day_of_week")
        ->groupBy('booking_date', 'day_of_week')
        ->orderBy('booking_date')
        ->get();

        // Revenue data
        $dailyRevenue = Payment::whereHas('booking', function($query) use ($ownerId) {
            $query->whereHas('court', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });
        })
        ->where('payment_date', '>=', $startDate)
        ->where('status', 'paid')
        ->selectRaw("{$paymentDateFunction} as payment_date, SUM(amount) as total_revenue")
        ->groupBy('payment_date')
        ->orderBy('payment_date')
        ->get();

        // Peak hours data
        $hourlyBookings = Booking::whereHas('court', function($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
        ->where('date', '>=', $startDate)
        ->selectRaw("{$hourFunction} as hour, COUNT(*) as booking_count, {$dayOfWeekFunction} as day_of_week")
        ->groupBy('hour', 'day_of_week')
        ->orderBy('hour')
        ->get();

        return [
            'daily_bookings' => $dailyBookings,
            'daily_revenue' => $dailyRevenue,
            'hourly_bookings' => $hourlyBookings,
            'start_date' => $startDate,
            'end_date' => Carbon::now()
        ];
    }

    /**
     * Analyze patterns in historical data
     */
    private function analyzePatterns($data)
    {
        $patterns = [];

        // Day of week patterns
        $dayOfWeekData = $data['daily_bookings']->groupBy('day_of_week');
        $patterns['day_of_week'] = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $dayData = $dayOfWeekData->get($i, collect());
            $patterns['day_of_week'][$i] = [
                'avg_bookings' => $dayData->avg('booking_count') ?? 0,
                'max_bookings' => $dayData->max('booking_count') ?? 0,
                'min_bookings' => $dayData->min('booking_count') ?? 0,
                'trend' => $this->calculateTrend($dayData->pluck('booking_count')->toArray()),
                'day_name' => $this->getDayName($i)
            ];
        }

        // Hourly patterns by day of week
        $hourlyData = $data['hourly_bookings']->groupBy('day_of_week');
        $patterns['hourly_by_day'] = [];
        
        for ($i = 1; $i <= 7; $i++) {
            $dayHourlyData = $hourlyData->get($i, collect())->groupBy('hour');
            $patterns['hourly_by_day'][$i] = [];
            
            for ($hour = 0; $hour < 24; $hour++) {
                $hourData = $dayHourlyData->get($hour, collect());
                $patterns['hourly_by_day'][$i][$hour] = [
                    'avg_bookings' => $hourData->avg('booking_count') ?? 0,
                    'peak_hour' => $hourData->max('booking_count') > 0
                ];
            }
        }

        // Seasonal patterns (if we have enough data)
        $patterns['seasonal'] = $this->analyzeSeasonalPatterns($data['daily_bookings']);

        // Growth trends
        $patterns['growth_trend'] = $this->calculateGrowthTrend($data['daily_bookings']);

        return $patterns;
    }

    /**
     * Generate predictions for next 7 days
     */
    private function generatePredictions($patterns)
    {
        $predictions = [];
        $startDate = Carbon::now()->addDay();
        
        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;
            
            // Base prediction from historical patterns
            $basePrediction = $patterns['day_of_week'][$dayOfWeek]['avg_bookings'] ?? 0;
            
            // Apply growth trend
            $growthFactor = 1 + ($patterns['growth_trend'] * ($i + 1) / 100);
            
            // Apply seasonal adjustments
            $seasonalFactor = $this->getSeasonalFactor($date, $patterns['seasonal']);
            
            // Calculate final prediction
            $predictedBookings = round($basePrediction * $growthFactor * $seasonalFactor);
            
            // Calculate confidence based on historical variance
            $confidence = $this->calculateDayConfidence($patterns['day_of_week'][$dayOfWeek] ?? []);
            
            // Determine if it's a peak or low day
            $avgBookings = $patterns['day_of_week'][$dayOfWeek]['avg_bookings'] ?? 0;
            $isPeakDay = $predictedBookings > $avgBookings * 1.2;
            $isLowDay = $predictedBookings < $avgBookings * 0.8;
            
            // Get peak hours for this day
            $peakHours = $this->getPeakHoursForDay($dayOfWeek, $patterns['hourly_by_day'][$dayOfWeek] ?? []);
            
            $predictions[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('l'),
                'day_of_week' => $dayOfWeek,
                'predicted_bookings' => $predictedBookings,
                'confidence' => $confidence,
                'is_peak_day' => $isPeakDay,
                'is_low_day' => $isLowDay,
                'peak_hours' => $peakHours,
                'revenue_estimate' => $predictedBookings * $this->getAverageBookingValue(),
                'recommendations' => $this->getDayRecommendations($predictedBookings, $isPeakDay, $isLowDay)
            ];
        }
        
        return $predictions;
    }

    /**
     * Calculate confidence score for predictions
     */
    private function calculateConfidence($historicalData, $patterns)
    {
        $totalDays = $historicalData['daily_bookings']->count();
        $dataQuality = min($totalDays / 30, 1); // Higher confidence with more data
        
        $patternConsistency = $this->calculatePatternConsistency($patterns);
        
        $overallConfidence = ($dataQuality * 0.6) + ($patternConsistency * 0.4);
        
        return [
            'overall' => round($overallConfidence * 100, 1),
            'data_quality' => round($dataQuality * 100, 1),
            'pattern_consistency' => round($patternConsistency * 100, 1),
            'data_points' => $totalDays
        ];
    }

    /**
     * Generate actionable recommendations
     */
    private function generateRecommendations($predictions, $patterns)
    {
        $recommendations = [];
        
        // Find peak and low days
        $peakDays = collect($predictions)->where('is_peak_day', true);
        $lowDays = collect($predictions)->where('is_low_day', true);
        
        if ($peakDays->count() > 0) {
            $recommendations[] = [
                'type' => 'peak_days',
                'title' => 'Peak Days Strategy',
                'description' => 'You have ' . $peakDays->count() . ' peak days predicted this week',
                'actions' => [
                    'Increase staff during peak hours',
                    'Consider premium pricing for peak slots',
                    'Promote off-peak hours to balance demand',
                    'Ensure all courts are operational'
                ],
                'days' => $peakDays->pluck('day_name')->toArray()
            ];
        }
        
        if ($lowDays->count() > 0) {
            $recommendations[] = [
                'type' => 'low_days',
                'title' => 'Low Days Strategy',
                'description' => 'You have ' . $lowDays->count() . ' low-demand days predicted this week',
                'actions' => [
                    'Offer special promotions or discounts',
                    'Run marketing campaigns for these days',
                    'Schedule maintenance during low-demand periods',
                    'Consider flexible pricing strategies'
                ],
                'days' => $lowDays->pluck('day_name')->toArray()
            ];
        }
        
        // Overall recommendations
        $avgBookings = collect($predictions)->avg('predicted_bookings');
        $totalRevenue = collect($predictions)->sum('revenue_estimate');
        
        $recommendations[] = [
            'type' => 'overall',
            'title' => 'Weekly Overview',
            'description' => "Expected " . round($avgBookings, 1) . " average bookings per day",
            'actions' => [
                'Total estimated revenue: RM ' . number_format($totalRevenue, 2),
                'Focus on customer retention during peak days',
                'Use low days for business development',
                'Monitor actual vs predicted performance'
            ]
        ];
        
        return $recommendations;
    }

    /**
     * Helper methods
     */
    private function calculateTrend($values)
    {
        if (count($values) < 2) return 0;
        
        $n = count($values);
        $x = range(1, $n);
        $y = $values;
        
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(function($x, $y) { return $x * $y; }, $x, $y));
        $sumXX = array_sum(array_map(function($x) { return $x * $x; }, $x));
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
        return $slope;
    }

    private function getDayName($dayOfWeek)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$dayOfWeek - 1] ?? 'Unknown';
    }

    private function analyzeSeasonalPatterns($dailyBookings)
    {
        // Simple seasonal analysis - could be enhanced with more sophisticated algorithms
        $monthlyData = $dailyBookings->groupBy(function($item) {
            return Carbon::parse($item->booking_date)->format('m');
        });
        
        $seasonalFactors = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyData->get(sprintf('%02d', $i), collect());
            $avgBookings = $monthData->avg('booking_count') ?? 1;
            $overallAvg = $dailyBookings->avg('booking_count') ?? 1;
            $seasonalFactors[$i] = $avgBookings / $overallAvg;
        }
        
        return $seasonalFactors;
    }

    private function calculateGrowthTrend($dailyBookings)
    {
        $values = $dailyBookings->pluck('booking_count')->toArray();
        return $this->calculateTrend($values);
    }

    private function getSeasonalFactor($date, $seasonalPatterns)
    {
        $month = $date->month;
        return $seasonalPatterns[$month] ?? 1.0;
    }

    private function calculateDayConfidence($dayPattern)
    {
        if (empty($dayPattern)) return 0.5;
        
        $variance = ($dayPattern['max_bookings'] ?? 0) - ($dayPattern['min_bookings'] ?? 0);
        $avg = $dayPattern['avg_bookings'] ?? 0;
        
        if ($avg == 0) return 0.5;
        
        $coefficient = $variance / $avg;
        return max(0.1, 1 - ($coefficient / 2));
    }

    private function getPeakHoursForDay($dayOfWeek, $hourlyPattern)
    {
        $peakHours = [];
        if (empty($hourlyPattern)) return $peakHours;
        
        foreach ($hourlyPattern as $hour => $data) {
            if (isset($data['peak_hour']) && $data['peak_hour']) {
                $peakHours[] = $hour;
            }
        }
        return $peakHours;
    }

    private function getAverageBookingValue()
    {
        // This could be made dynamic based on historical data
        return 50; // Default average booking value
    }

    private function calculatePatternConsistency($patterns)
    {
        // Calculate how consistent the patterns are across different days
        $dayAverages = collect($patterns['day_of_week'])->pluck('avg_bookings');
        $values = $dayAverages->toArray();
        
        if (count($values) < 2) return 0.5;
        
        $mean = array_sum($values) / count($values);
        if ($mean == 0) return 0.5;
        
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        
        $coefficient = sqrt($variance) / $mean;
        return max(0.1, 1 - ($coefficient / 2));
    }

    private function getDayRecommendations($predictedBookings, $isPeakDay, $isLowDay)
    {
        $recommendations = [];
        
        if ($isPeakDay) {
            $recommendations[] = "High demand expected - prepare for busy day";
            $recommendations[] = "Consider increasing staff during peak hours";
        } elseif ($isLowDay) {
            $recommendations[] = "Low demand expected - good day for promotions";
            $recommendations[] = "Consider maintenance or training activities";
        } else {
            $recommendations[] = "Normal demand expected - maintain regular operations";
        }
        
        return $recommendations;
    }
}
