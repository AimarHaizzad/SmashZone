<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use App\Models\User;
use App\Models\Product;
use App\Services\BookingPredictionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;

class AnalyticsController extends Controller
{
    public function index()
    {
        try {
            if (!auth()->user() || !auth()->user()->isOwner()) {
                abort(403, 'Unauthorized. Only owners can access analytics.');
            }

            $user = auth()->user();
            
            // Revenue Analytics
            try {
                $revenueData = $this->getRevenueAnalytics($user);
            } catch (\Exception $e) {
                \Log::error('Revenue analytics error', ['error' => $e->getMessage()]);
                $revenueData = $this->getEmptyRevenueData();
            }
            
            // Court Utilization
            try {
                $utilizationData = $this->getCourtUtilization($user);
            } catch (\Exception $e) {
                \Log::error('Court utilization error', ['error' => $e->getMessage()]);
                $utilizationData = $this->getEmptyUtilizationData();
            }
            
            // Customer Analytics
            try {
                $customerData = $this->getCustomerAnalytics($user);
            } catch (\Exception $e) {
                \Log::error('Customer analytics error', ['error' => $e->getMessage()]);
                $customerData = $this->getEmptyCustomerData();
            }
            
            // Performance Metrics
            try {
                $performanceData = $this->getPerformanceMetrics($user);
            } catch (\Exception $e) {
                \Log::error('Performance metrics error', ['error' => $e->getMessage()]);
                $performanceData = $this->getEmptyPerformanceData();
            }
            
            // AI Booking Predictions
            try {
                $predictionService = new BookingPredictionService();
                $predictionData = $predictionService->predictWeeklyTrends($user->id);
            } catch (\Exception $e) {
                \Log::error('Prediction service error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $predictionData = $this->getEmptyPredictionData();
            }

            return view('analytics.index', compact(
                'revenueData',
                'utilizationData', 
                'customerData',
                'performanceData',
                'predictionData'
            ));
        } catch (\Exception $e) {
            \Log::error('Analytics index error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('analytics.index', [
                'revenueData' => $this->getEmptyRevenueData(),
                'utilizationData' => $this->getEmptyUtilizationData(),
                'customerData' => $this->getEmptyCustomerData(),
                'performanceData' => $this->getEmptyPerformanceData(),
                'predictionData' => $this->getEmptyPredictionData()
            ])->withErrors(['error' => 'Failed to load analytics. Please try again later.']);
        }
    }

    private function getRevenueAnalytics($user)
    {
        try {
            $driver = DB::getDriverName();
            $monthFunction = $driver === 'pgsql' 
                ? 'EXTRACT(MONTH FROM payment_date)' 
                : 'MONTH(payment_date)';
            $yearFunction = $driver === 'pgsql' 
                ? 'EXTRACT(YEAR FROM payment_date)' 
                : 'YEAR(payment_date)';
            
            // Monthly revenue for the last 12 months
            $monthlyRevenue = Payment::whereHas('booking', function($query) use ($user) {
                $query->whereHas('court', function($q) use ($user) {
                    $q->where('owner_id', $user->id);
                });
            })
            ->where('payments.status', 'paid')
            ->selectRaw("{$monthFunction} as month, {$yearFunction} as year, SUM(amount) as total")
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
            $dateFunction = $driver === 'pgsql' 
                ? 'payment_date::date' 
                : 'DATE(payment_date)';
            
            $dailyRevenue = Payment::whereHas('booking', function($query) use ($user) {
                $query->whereHas('court', function($q) use ($user) {
                    $q->where('owner_id', $user->id);
                });
            })
            ->where('payments.status', 'paid')
            ->where('payment_date', '>=', now()->startOfMonth())
            ->selectRaw("{$dateFunction} as date, SUM(amount) as total")
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
        } catch (\Exception $e) {
            \Log::error('getRevenueAnalytics error', ['error' => $e->getMessage()]);
            return $this->getEmptyRevenueData();
        }
    }

    private function getCourtUtilization($user)
    {
        try {
            $driver = DB::getDriverName();
            $hourFunction = $driver === 'pgsql' 
                ? 'EXTRACT(HOUR FROM start_time::time)' 
                : 'HOUR(start_time)';
            
            // Peak hours analysis
            $peakHours = Booking::whereHas('court', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->selectRaw("{$hourFunction} as hour, COUNT(*) as booking_count")
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
            $dayOfWeekFunction = $driver === 'pgsql' 
                ? 'EXTRACT(DOW FROM date) + 1' 
                : 'DAYOFWEEK(date)';
            
            $weeklyUtilization = Booking::whereHas('court', function($query) use ($user) {
                $query->where('owner_id', $user->id);
            })
            ->selectRaw("{$dayOfWeekFunction} as day_of_week, COUNT(*) as booking_count")
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
        } catch (\Exception $e) {
            \Log::error('getCourtUtilization error', ['error' => $e->getMessage()]);
            return $this->getEmptyUtilizationData();
        }
    }

    private function getCustomerAnalytics($user)
    {
        try {
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
            $driver = DB::getDriverName();
            if ($driver === 'pgsql') {
                $newCustomers = Booking::whereHas('court', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })
                ->where('date', '>=', now()->subMonths(1))
                ->distinct('user_id')
                ->count('user_id');
            } else {
                $newCustomers = Booking::whereHas('court', function($query) use ($user) {
                    $query->where('owner_id', $user->id);
                })
                ->where('date', '>=', now()->subMonths(1))
                ->distinct()
                ->count('user_id');
            }

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
        } catch (\Exception $e) {
            \Log::error('getCustomerAnalytics error', ['error' => $e->getMessage()]);
            return $this->getEmptyCustomerData();
        }
    }

    private function getPerformanceMetrics($user)
    {
        try {
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
        } catch (\Exception $e) {
            \Log::error('getPerformanceMetrics error', ['error' => $e->getMessage()]);
            return $this->getEmptyPerformanceData();
        }
    }

    public function exportPDF()
    {
        try {
            if (!auth()->user() || !auth()->user()->isOwner()) {
                abort(403, 'Unauthorized.');
            }

            $user = auth()->user();
            
            try {
                $revenueData = $this->getRevenueAnalytics($user);
            } catch (\Exception $e) {
                \Log::error('Revenue analytics error in PDF export', ['error' => $e->getMessage()]);
                $revenueData = $this->getEmptyRevenueData();
            }
            
            try {
                $utilizationData = $this->getCourtUtilization($user);
            } catch (\Exception $e) {
                \Log::error('Court utilization error in PDF export', ['error' => $e->getMessage()]);
                $utilizationData = $this->getEmptyUtilizationData();
            }
            
            try {
                $customerData = $this->getCustomerAnalytics($user);
            } catch (\Exception $e) {
                \Log::error('Customer analytics error in PDF export', ['error' => $e->getMessage()]);
                $customerData = $this->getEmptyCustomerData();
            }
            
            try {
                $performanceData = $this->getPerformanceMetrics($user);
            } catch (\Exception $e) {
                \Log::error('Performance metrics error in PDF export', ['error' => $e->getMessage()]);
                $performanceData = $this->getEmptyPerformanceData();
            }

            try {
                $pdf = PDF::loadView('analytics.pdf', compact(
                    'revenueData',
                    'utilizationData',
                    'customerData', 
                    'performanceData'
                ));

                return $pdf->download('smashzone-analytics-' . now()->format('Y-m-d') . '.pdf');
            } catch (\Exception $e) {
                \Log::error('PDF generation failed', ['error' => $e->getMessage()]);
                return redirect('/analytics')->withErrors(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            \Log::error('PDF export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/analytics')->withErrors(['error' => 'Failed to export PDF. Please try again later.']);
        }
    }

    public function exportExcel()
    {
        try {
            if (!auth()->check()) {
                return redirect('/login')->withErrors(['error' => 'Please login to export analytics.']);
            }

            $user = auth()->user();
            
            if (!$user->isOwner()) {
                abort(403, 'Unauthorized. Only owners can export analytics.');
            }

            // Check if Excel package is available
            if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
                \Log::error('Excel package not found');
                return redirect('/analytics')->withErrors(['error' => 'Excel export package not available.']);
            }

            try {
                // Set memory limit for large exports
                ini_set('memory_limit', '512M');
                set_time_limit(300); // 5 minutes
                
                // Create export instance
                $export = new AnalyticsExport($user);
                
                $filename = 'smashzone-analytics-' . now()->format('Y-m-d') . '.xlsx';
                
                \Log::info('Starting Excel export', [
                    'user_id' => $user->id, 
                    'filename' => $filename,
                    'export_class' => get_class($export)
                ]);
                
                // Try to generate the export
                try {
                    return Excel::download($export, $filename);
                } catch (\Throwable $ex) {
                    \Log::error('Excel::download failed', [
                        'error' => $ex->getMessage(),
                        'file' => $ex->getFile(),
                        'line' => $ex->getLine(),
                        'trace' => $ex->getTraceAsString()
                    ]);
                    throw $ex;
                }
            } catch (\Illuminate\Database\QueryException $e) {
                \Log::error('Excel export database error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => $user->id ?? null
                ]);
                return redirect('/analytics')->withErrors(['error' => 'Database error while generating Excel. Please check logs for details.']);
            } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
                \Log::error('Excel export PhpSpreadsheet error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => $user->id ?? null
                ]);
                return redirect('/analytics')->withErrors(['error' => 'Excel generation error. Please try again later.']);
            } catch (\TypeError $e) {
                \Log::error('Excel export TypeError', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => $user->id ?? null
                ]);
                return redirect('/analytics')->withErrors(['error' => 'Data type error in Excel export. Please check your data.']);
            } catch (\Exception $e) {
                \Log::error('Excel export failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'user_id' => $user->id ?? null,
                    'class' => get_class($e)
                ]);
                return redirect('/analytics')->withErrors(['error' => 'Failed to generate Excel file. Error: ' . substr($e->getMessage(), 0, 100)]);
            }
        } catch (\Exception $e) {
            \Log::error('Excel export failed (outer catch)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'class' => get_class($e)
            ]);
            return redirect('/analytics')->withErrors(['error' => 'Failed to export Excel. Please try again later.']);
        }
    }

    private function getEmptyRevenueData()
    {
        return [
            'monthly' => collect(),
            'monthly_labels' => collect(),
            'monthly_data' => collect(),
            'by_court' => collect(),
            'daily' => collect(),
            'total_revenue' => 0,
            'avg_revenue_per_booking' => 0
        ];
    }

    private function getEmptyUtilizationData()
    {
        return [
            'peak_hours' => collect(),
            'peak_hours_labels' => collect(),
            'peak_hours_data' => collect(),
            'court_popularity' => collect(),
            'weekly_utilization' => collect(),
            'utilization_rate' => collect()
        ];
    }

    private function getEmptyCustomerData()
    {
        return [
            'top_customers' => collect(),
            'customer_retention' => collect(),
            'new_customers' => 0,
            'returning_customers' => 0,
            'avg_bookings_per_customer' => 0
        ];
    }

    private function getEmptyPerformanceData()
    {
        return [
            'conversion_rate' => 0,
            'avg_booking_value' => 0,
            'monthly_growth' => 0,
            'current_month_revenue' => 0,
            'last_month_revenue' => 0,
            'court_efficiency' => collect()
        ];
    }

    private function getEmptyPredictionData()
    {
        // Generate empty predictions for next 7 days
        $predictions = [];
        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);
            $predictions[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => $date->format('l'),
                'predicted_bookings' => 0,
                'revenue_estimate' => 0,
                'confidence' => 0,
                'is_peak_day' => false,
                'is_low_day' => false
            ];
        }

        return [
            'predictions' => $predictions,
            'confidence' => [
                'overall' => 0,
                'by_day' => array_fill(0, 7, 0)
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
} 