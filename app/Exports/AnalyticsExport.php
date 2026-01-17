<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AnalyticsExport implements WithMultipleSheets
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function sheets(): array
    {
        return [
            new RevenueSummarySheet($this->user),
            new CourtUtilizationSheet($this->user),
            new CustomerAnalyticsSheet($this->user),
            new PerformanceMetricsSheet($this->user),
        ];
    }
}

class RevenueSummarySheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        try {
            // Get owner's court IDs
            $courtIds = Court::where('owner_id', $this->user->id)->pluck('id');
            
            if ($courtIds->isEmpty()) {
                return collect([]);
            }
            
            // Simplified query - get bookings for owner's courts, then get their payments
            $payments = Payment::with(['booking.court', 'booking.user'])
                ->whereHas('booking', function($query) use ($courtIds) {
                    $query->whereIn('court_id', $courtIds);
                })
                ->whereIn('status', ['paid', 'completed']) // Support both status values
                ->orderBy('payment_date', 'desc')
                ->get()
                ->map(function($payment) {
                    // Transform to match expected structure
                    $booking = $payment->booking;
                    if (!$booking) {
                        return null;
                    }
                    
                    $court = $booking->court;
                    $user = $booking->user;
                    
                    return (object) [
                        'court_name' => $court ? $court->name : 'Unknown Court',
                        'customer_name' => $user ? $user->name : 'Unknown Customer',
                        'customer_email' => $user ? $user->email : 'No email',
                        'date' => $booking->date,
                        'start_time' => $booking->start_time,
                        'end_time' => $booking->end_time,
                        'amount' => $payment->amount,
                        'payment_date' => $payment->payment_date,
                        'status' => $payment->status,
                    ];
                })
                ->filter(); // Remove null values
            
            return $payments;
        } catch (\Exception $e) {
            Log::error('RevenueSummarySheet collection error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Court Name',
            'Customer Name',
            'Customer Email',
            'Booking Date',
            'Start Time',
            'End Time',
            'Amount (RM)',
            'Payment Date',
            'Status'
        ];
    }

    public function map($row): array
    {
        try {
            if (!is_object($row) && !is_array($row)) {
                return ['', '', '', '', '', '', '0.00', '', ''];
            }
            
            return [
                $row->court_name ?? 'Unknown Court',
                $row->customer_name ?? 'Unknown Customer',
                $row->customer_email ?? 'No email',
                $row->date ?? '',
                $row->start_time ?? '',
                $row->end_time ?? '',
                number_format($row->amount ?? 0, 2),
                $row->payment_date ?? '',
                ucfirst($row->status ?? 'unknown')
            ];
        } catch (\Exception $e) {
            Log::error('RevenueSummarySheet map error', ['error' => $e->getMessage()]);
            return ['Error', 'Failed to map', '', '', '', '', '0.00', '', ''];
        }
    }

    public function title(): string
    {
        return 'Revenue Summary';
    }
}

class CourtUtilizationSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        try {
            $courts = Court::where('owner_id', $this->user->id)
            ->withCount(['bookings' => function($query) {
                $query->where('date', '>=', now()->subMonths(6));
            }])
            ->withSum(['bookings' => function($query) {
                $query->where('date', '>=', now()->subMonths(6));
            }], 'total_price')
            ->get();
            
            // Return empty collection if no data (Excel can handle empty collections)
            return $courts;
        } catch (\Exception $e) {
            Log::error('CourtUtilizationSheet collection error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Court Name',
            'Location',
            'Price per Hour',
            'Total Bookings (6 months)',
            'Total Revenue (6 months)',
            'Average Revenue per Booking',
            'Utilization Rate (%)'
        ];
    }

    public function map($row): array
    {
        try {
            if (!is_object($row) && !is_array($row)) {
                return ['', '', '0.00', 0, '0.00', '0.00', '0.0'];
            }
            
            $bookingsCount = $row->bookings_count ?? 0;
            $totalPrice = $row->bookings_sum_total_price ?? 0;
            $utilizationRate = $bookingsCount > 0 ? ($bookingsCount * 2) / (24 * 30) * 100 : 0;
            $avgRevenue = $bookingsCount > 0 ? $totalPrice / $bookingsCount : 0;

            return [
                $row->name ?? 'Unknown Court',
                $row->location ?? 'Unknown Location',
                number_format($row->price_per_hour ?? 0, 2),
                $bookingsCount,
                number_format($totalPrice, 2),
                number_format($avgRevenue, 2),
                number_format($utilizationRate, 1)
            ];
        } catch (\Exception $e) {
            Log::error('CourtUtilizationSheet map error', ['error' => $e->getMessage()]);
            return ['Error', 'Failed to map', '0.00', 0, '0.00', '0.00', '0.0'];
        }
    }

    public function title(): string
    {
        return 'Court Utilization';
    }
}

class CustomerAnalyticsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        try {
            // Get owner's court IDs
            $courtIds = Court::where('owner_id', $this->user->id)->pluck('id');
            
            if ($courtIds->isEmpty()) {
                return collect([]);
            }
            
            // Simplified query using Eloquent relationships
            $customers = Payment::with(['booking.court', 'booking.user'])
                ->whereHas('booking', function($query) use ($courtIds) {
                    $query->whereIn('court_id', $courtIds);
                })
                ->whereIn('status', ['paid', 'completed']) // Support both status values
                ->get()
                ->groupBy('booking.user_id')
                ->map(function($payments, $userId) {
                    $firstPayment = $payments->first();
                    $user = $firstPayment->booking->user ?? null;
                    
                    if (!$user) {
                        return null;
                    }
                    
                    $bookings = $payments->pluck('booking')->filter();
                    $bookingCount = $bookings->count();
                    $totalSpent = $payments->sum('amount');
                    $avgSpent = $bookingCount > 0 ? $totalSpent / $bookingCount : 0;
                    
                    return (object) [
                        'name' => $user->name,
                        'email' => $user->email,
                        'booking_count' => $bookingCount,
                        'total_spent' => $totalSpent,
                        'avg_spent_per_booking' => $avgSpent,
                        'first_booking' => $bookings->min('date'),
                        'last_booking' => $bookings->max('date'),
                    ];
                })
                ->filter() // Remove null values
                ->sortByDesc('total_spent')
                ->values();
            
            return $customers;
        } catch (\Exception $e) {
            Log::error('CustomerAnalyticsSheet collection error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Customer Name',
            'Email',
            'Total Bookings',
            'Total Spent (RM)',
            'Average Spent per Booking (RM)',
            'First Booking Date',
            'Last Booking Date',
            'Customer Type'
        ];
    }

    public function map($row): array
    {
        try {
            if (!is_object($row) && !is_array($row)) {
                return ['', '', 0, '0.00', '0.00', '', '', 'New'];
            }
            
            $bookingCount = $row->booking_count ?? 0;
            $customerType = $bookingCount > 5 ? 'VIP' : ($bookingCount > 2 ? 'Regular' : 'New');
            
            return [
                $row->name ?? 'Unknown Customer',
                $row->email ?? 'No email',
                $bookingCount,
                number_format($row->total_spent ?? 0, 2),
                number_format($row->avg_spent_per_booking ?? 0, 2),
                $row->first_booking ?? '',
                $row->last_booking ?? '',
                $customerType
            ];
        } catch (\Exception $e) {
            Log::error('CustomerAnalyticsSheet map error', ['error' => $e->getMessage()]);
            return ['Error', 'Failed to map', 0, '0.00', '0.00', '', '', 'New'];
        }
    }

    public function title(): string
    {
        return 'Customer Analytics';
    }
}

class PerformanceMetricsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        try {
            $metrics = collect();
            
            // Overall metrics
            $totalBookings = Booking::whereHas('court', function($query) {
                $query->where('owner_id', $this->user->id);
            })->count();

            // Get owner's court IDs
            $courtIds = Court::where('owner_id', $this->user->id)->pluck('id');
            
            if ($courtIds->isEmpty()) {
                $paidBookings = 0;
                $conversionRate = 0;
                $avgBookingValue = 0;
                $currentMonthRevenue = 0;
                $lastMonthRevenue = 0;
            } else {
                $paidBookings = Payment::whereHas('booking', function($query) use ($courtIds) {
                    $query->whereIn('court_id', $courtIds);
                })->whereIn('status', ['paid', 'completed'])->count();

                $conversionRate = $totalBookings > 0 ? ($paidBookings / $totalBookings) * 100 : 0;

                $avgBookingValue = Payment::whereHas('booking', function($query) use ($courtIds) {
                    $query->whereIn('court_id', $courtIds);
                })->whereIn('status', ['paid', 'completed'])->avg('amount') ?? 0;

                $currentMonthRevenue = Payment::whereHas('booking', function($query) use ($courtIds) {
                    $query->whereIn('court_id', $courtIds);
                })->whereIn('status', ['paid', 'completed'])
                ->where('payment_date', '>=', now()->startOfMonth())
                ->sum('amount');

                $lastMonthRevenue = Payment::whereHas('booking', function($query) use ($courtIds) {
                    $query->whereIn('court_id', $courtIds);
                })->whereIn('status', ['paid', 'completed'])
                ->where('payment_date', '>=', now()->subMonth()->startOfMonth())
                ->where('payment_date', '<', now()->startOfMonth())
                ->sum('amount');
            }

            $monthlyGrowth = $lastMonthRevenue > 0 ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

            $metrics->push([
                'metric' => 'Total Bookings',
                'value' => $totalBookings,
                'unit' => 'bookings'
            ]);

            $metrics->push([
                'metric' => 'Paid Bookings',
                'value' => $paidBookings,
                'unit' => 'bookings'
            ]);

            $metrics->push([
                'metric' => 'Conversion Rate',
                'value' => $conversionRate,
                'unit' => '%'
            ]);

            $metrics->push([
                'metric' => 'Average Booking Value',
                'value' => $avgBookingValue,
                'unit' => 'RM'
            ]);

            $metrics->push([
                'metric' => 'Current Month Revenue',
                'value' => $currentMonthRevenue,
                'unit' => 'RM'
            ]);

            $metrics->push([
                'metric' => 'Last Month Revenue',
                'value' => $lastMonthRevenue,
                'unit' => 'RM'
            ]);

            $metrics->push([
                'metric' => 'Monthly Growth',
                'value' => $monthlyGrowth,
                'unit' => '%'
            ]);

            // Return metrics collection (can be empty)
            return $metrics;
        } catch (\Exception $e) {
            Log::error('PerformanceMetricsSheet collection error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'Metric',
            'Value',
            'Unit'
        ];
    }

    public function map($row): array
    {
        try {
            if (!is_array($row)) {
                return ['Error', '0', 'N/A'];
            }
            
            $unit = $row['unit'] ?? '';
            $value = $row['value'] ?? 0;
            
            $formattedValue = $unit === 'RM' ? number_format($value, 2) : 
                            ($unit === '%' ? number_format($value, 1) : $value);

            return [
                $row['metric'] ?? 'Unknown Metric',
                $formattedValue,
                $unit
            ];
        } catch (\Exception $e) {
            Log::error('PerformanceMetricsSheet map error', ['error' => $e->getMessage()]);
            return ['Error', '0', 'N/A'];
        }
    }

    public function title(): string
    {
        return 'Performance Metrics';
    }
} 