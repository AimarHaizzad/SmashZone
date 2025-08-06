<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Unauthorized. Only owners can access analytics.');
        }

        $user = auth()->user();
        
        // Revenue Analytics
        $revenueData = $this->getRevenueAnalytics($user);
        
        // Court Utilization
        $utilizationData = $this->getCourtUtilization($user);
        
        // Customer Analytics
        $customerData = $this->getCustomerAnalytics($user);
        
        // Performance Metrics
        $performanceData = $this->getPerformanceMetrics($user);

        return view('analytics.index', compact(
            'revenueData',
            'utilizationData', 
            'customerData',
            'performanceData'
        ));
    }

    private function getRevenueAnalytics($user)
    {
        // Monthly revenue for the last 12 months
        $monthlyRevenue = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })
        ->where('payments.status', 'paid')
        ->selectRaw('MONTH(payment_date) as month, YEAR(payment_date) as year, SUM(amount) as total')
        ->where('payment_date', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Process monthly data for charts
        $monthlyLabels = $monthlyRevenue->map(function($item) {
            return date('M', mktime(0, 0, 0, $item->month, 1));
        });
        $monthlyData = $monthlyRevenue->pluck('total');

        // Revenue by court
        $revenueByCourt = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })
        ->where('payments.status', 'paid')
        ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
        ->join('courts', 'bookings.court_id', '=', 'courts.id')
        ->selectRaw('courts.name, SUM(payments.amount) as total_revenue, COUNT(*) as booking_count')
        ->groupBy('courts.id', 'courts.name')
        ->orderBy('total_revenue', 'desc')
        ->get();

        // Daily revenue for current month
        $dailyRevenue = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })
        ->where('payments.status', 'paid')
        ->where('payment_date', '>=', now()->startOfMonth())
        ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'monthly' => $monthlyRevenue,
            'monthly_labels' => $monthlyLabels,
            'monthly_data' => $monthlyData,
            'by_court' => $revenueByCourt,
            'daily' => $dailyRevenue,
            'total_revenue' => $revenueByCourt->sum('total_revenue'),
            'avg_revenue_per_booking' => $revenueByCourt->sum('total_revenue') / max($revenueByCourt->sum('booking_count'), 1)
        ];
    }

    private function getCourtUtilization($user)
    {
        // Peak hours analysis
        $peakHours = Booking::whereHas('court', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->selectRaw('HOUR(start_time) as hour, COUNT(*) as booking_count')
        ->groupBy('hour')
        ->orderBy('hour')
        ->get();

        // Process peak hours data for charts
        $peakHoursLabels = $peakHours->map(function($item) {
            return $item->hour . ':00';
        });
        $peakHoursData = $peakHours->pluck('booking_count');

        // Court popularity
        $courtPopularity = Court::where('owner_id', $user->id)
        ->withCount(['bookings' => function($query) {
            $query->where('date', '>=', now()->subMonths(6));
        }])
        ->withSum(['bookings' => function($query) {
            $query->where('date', '>=', now()->subMonths(6));
        }], 'total_price')
        ->orderBy('bookings_count', 'desc')
        ->get();

        // Weekly utilization
        $weeklyUtilization = Booking::whereHas('court', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->selectRaw('DAYOFWEEK(date) as day_of_week, COUNT(*) as booking_count')
        ->where('date', '>=', now()->subMonths(3))
        ->groupBy('day_of_week')
        ->orderBy('day_of_week')
        ->get();

        // Utilization rate by court
        $totalHours = 24 * 30; // Assuming 30 days
        $utilizationRate = Court::where('owner_id', $user->id)
        ->withCount(['bookings' => function($query) {
            $query->where('date', '>=', now()->subMonths(1));
        }])
        ->get()
        ->map(function($court) use ($totalHours) {
            $court->utilization_rate = ($court->bookings_count * 2) / $totalHours * 100; // Assuming 2 hours per booking
            return $court;
        });

        return [
            'peak_hours' => $peakHours,
            'peak_hours_labels' => $peakHoursLabels,
            'peak_hours_data' => $peakHoursData,
            'court_popularity' => $courtPopularity,
            'weekly_utilization' => $weeklyUtilization,
            'utilization_rate' => $utilizationRate
        ];
    }

    private function getCustomerAnalytics($user)
    {
        // Top customers by spending
        $topCustomers = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })
        ->where('payments.status', 'paid')
        ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
        ->join('users', 'bookings.user_id', '=', 'users.id')
        ->selectRaw('users.name, users.email, SUM(payments.amount) as total_spent, COUNT(*) as booking_count')
        ->groupBy('users.id', 'users.name', 'users.email')
        ->orderBy('total_spent', 'desc')
        ->limit(10)
        ->get();

        // Customer retention
        $customerRetention = Booking::whereHas('court', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->selectRaw('user_id, COUNT(*) as booking_count, MIN(date) as first_booking, MAX(date) as last_booking')
        ->groupBy('user_id')
        ->having('booking_count', '>', 1)
        ->get();

        // New vs returning customers
        $newCustomers = Booking::whereHas('court', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->where('date', '>=', now()->subMonths(1))
        ->distinct('user_id')
        ->count('user_id');

        $returningCustomers = $customerRetention->count();

        // Customer booking frequency
        $bookingFrequency = Booking::whereHas('court', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })
        ->selectRaw('user_id, COUNT(*) as booking_count')
        ->groupBy('user_id')
        ->get()
        ->avg('booking_count');

        return [
            'top_customers' => $topCustomers,
            'customer_retention' => $customerRetention,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'avg_bookings_per_customer' => $bookingFrequency ?? 0
        ];
    }

    private function getPerformanceMetrics($user)
    {
        // Booking conversion rate
        $totalBookings = Booking::whereHas('court', function($query) use ($user) {
            $query->where('owner_id', $user->id);
        })->count();

        $paidBookings = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->where('payments.status', 'paid')->count();

        $conversionRate = $totalBookings > 0 ? ($paidBookings / $totalBookings) * 100 : 0;

        // Average booking value
        $avgBookingValue = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->where('payments.status', 'paid')->avg('amount') ?? 0;

        // Monthly growth
        $currentMonthRevenue = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->where('payments.status', 'paid')
        ->where('payment_date', '>=', now()->startOfMonth())
        ->sum('amount');

        $lastMonthRevenue = Payment::whereHas('booking', function($query) use ($user) {
            $query->whereHas('court', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        })->where('payments.status', 'paid')
        ->where('payment_date', '>=', now()->subMonth()->startOfMonth())
        ->where('payment_date', '<', now()->startOfMonth())
        ->sum('amount');

        $monthlyGrowth = $lastMonthRevenue > 0 ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

        // Court efficiency
        $courtEfficiency = Court::where('owner_id', $user->id)
        ->withCount(['bookings' => function($query) {
            $query->where('date', '>=', now()->subMonths(1));
        }])
        ->withSum(['bookings' => function($query) {
            $query->where('date', '>=', now()->subMonths(1));
        }], 'total_price')
        ->get()
        ->map(function($court) {
            $court->revenue_per_booking = $court->bookings_count > 0 ? $court->bookings_sum_total_price / $court->bookings_count : 0;
            return $court;
        });

        return [
            'conversion_rate' => $conversionRate,
            'avg_booking_value' => $avgBookingValue,
            'monthly_growth' => $monthlyGrowth,
            'current_month_revenue' => $currentMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'court_efficiency' => $courtEfficiency
        ];
    }

    public function exportPDF()
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Unauthorized.');
        }

        $user = auth()->user();
        
        $revenueData = $this->getRevenueAnalytics($user);
        $utilizationData = $this->getCourtUtilization($user);
        $customerData = $this->getCustomerAnalytics($user);
        $performanceData = $this->getPerformanceMetrics($user);

        $pdf = PDF::loadView('analytics.pdf', compact(
            'revenueData',
            'utilizationData',
            'customerData', 
            'performanceData'
        ));

        return $pdf->download('smashzone-analytics-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel()
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Unauthorized.');
        }

        return Excel::download(new AnalyticsExport(auth()->user()), 'smashzone-analytics-' . now()->format('Y-m-d') . '.xlsx');
    }
} 