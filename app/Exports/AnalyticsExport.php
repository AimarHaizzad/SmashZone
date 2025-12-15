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
            'Revenue Summary' => new RevenueSummarySheet($this->user),
            'Court Utilization' => new CourtUtilizationSheet($this->user),
            'Customer Analytics' => new CustomerAnalyticsSheet($this->user),
            'Performance Metrics' => new PerformanceMetricsSheet($this->user),
        ];
    }
}

class RevenueSummarySheet implements FromCollection, WithHeadings, WithMapping
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        try {
            return Payment::whereHas('booking', function($query) {
                $query->whereHas('court', function($q) {
                    $q->where('owner_id', $this->user->id);
                });
            })
            ->where('payments.status', 'paid')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('courts', 'bookings.court_id', '=', 'courts.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->select(
                'courts.name as court_name',
                'users.name as customer_name',
                'users.email as customer_email',
                'bookings.date',
                'bookings.start_time',
                'bookings.end_time',
                'payments.amount',
                'payments.payment_date',
                'payments.status'
            )
            ->orderBy('payments.payment_date', 'desc')
            ->get();
        } catch (\Exception $e) {
            \Log::error('RevenueSummarySheet collection error', ['error' => $e->getMessage()]);
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
        return [
            $row->court_name,
            $row->customer_name,
            $row->customer_email,
            $row->date,
            $row->start_time,
            $row->end_time,
            number_format($row->amount, 2),
            $row->payment_date,
            ucfirst($row->status)
        ];
    }
}

class CourtUtilizationSheet implements FromCollection, WithHeadings, WithMapping
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        try {
            return Court::where('owner_id', $this->user->id)
            ->withCount(['bookings' => function($query) {
                $query->where('date', '>=', now()->subMonths(6));
            }])
            ->withSum(['bookings' => function($query) {
                $query->where('date', '>=', now()->subMonths(6));
            }], 'total_price')
            ->get();
        } catch (\Exception $e) {
            \Log::error('CourtUtilizationSheet collection error', ['error' => $e->getMessage()]);
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
        $utilizationRate = $row->bookings_count > 0 ? ($row->bookings_count * 2) / (24 * 30) * 100 : 0;
        $avgRevenue = $row->bookings_count > 0 ? $row->bookings_sum_total_price / $row->bookings_count : 0;

        return [
            $row->name,
            $row->location,
            number_format($row->price_per_hour, 2),
            $row->bookings_count,
            number_format($row->bookings_sum_total_price, 2),
            number_format($avgRevenue, 2),
            number_format($utilizationRate, 1)
        ];
    }
}

class CustomerAnalyticsSheet implements FromCollection, WithHeadings, WithMapping
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        try {
            return Payment::whereHas('booking', function($query) {
                $query->whereHas('court', function($q) {
                    $q->where('owner_id', $this->user->id);
                });
            })
            ->where('payments.status', 'paid')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->selectRaw('
                users.name,
                users.email,
                COUNT(*) as booking_count,
                SUM(payments.amount) as total_spent,
                AVG(payments.amount) as avg_spent_per_booking,
                MIN(bookings.date) as first_booking,
                MAX(bookings.date) as last_booking
            ')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->get();
        } catch (\Exception $e) {
            \Log::error('CustomerAnalyticsSheet collection error', ['error' => $e->getMessage()]);
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
        $customerType = $row->booking_count > 5 ? 'VIP' : ($row->booking_count > 2 ? 'Regular' : 'New');
        
        return [
            $row->name,
            $row->email,
            $row->booking_count,
            number_format($row->total_spent, 2),
            number_format($row->avg_spent_per_booking, 2),
            $row->first_booking,
            $row->last_booking,
            $customerType
        ];
    }
}

class PerformanceMetricsSheet implements FromCollection, WithHeadings, WithMapping
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

            $paidBookings = Payment::whereHas('booking', function($query) {
                $query->whereHas('court', function($q) {
                    $q->where('owner_id', $this->user->id);
                });
            })->where('payments.status', 'paid')->count();

            $conversionRate = $totalBookings > 0 ? ($paidBookings / $totalBookings) * 100 : 0;

            $avgBookingValue = Payment::whereHas('booking', function($query) {
                $query->whereHas('court', function($q) {
                    $q->where('owner_id', $this->user->id);
                });
            })->where('payments.status', 'paid')->avg('amount') ?? 0;

            $currentMonthRevenue = Payment::whereHas('booking', function($query) {
                $query->whereHas('court', function($q) {
                    $q->where('owner_id', $this->user->id);
                });
            })->where('payments.status', 'paid')
            ->where('payment_date', '>=', now()->startOfMonth())
            ->sum('amount');

            $lastMonthRevenue = Payment::whereHas('booking', function($query) {
                $query->whereHas('court', function($q) {
                    $q->where('owner_id', $this->user->id);
                });
            })->where('payments.status', 'paid')
            ->where('payment_date', '>=', now()->subMonth()->startOfMonth())
            ->where('payment_date', '<', now()->startOfMonth())
            ->sum('amount');

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

            return $metrics;
        } catch (\Exception $e) {
            \Log::error('PerformanceMetricsSheet collection error', ['error' => $e->getMessage()]);
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
        $value = $row['unit'] === 'RM' ? number_format($row['value'], 2) : 
                ($row['unit'] === '%' ? number_format($row['value'], 1) : $row['value']);

        return [
            $row['metric'],
            $value,
            $row['unit']
        ];
    }
} 