<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $allBookings = collect();
        $recentOrders = collect();
        $featuredProducts = collect();
        $metrics = [];

        try {
            if ($user->isOwner()) {
                $courtIds = $user->courts()->pluck('id');

                $allBookings = Booking::with(['court', 'user', 'payment'])
                    ->whereIn('court_id', $courtIds)
                    ->orderByDesc('date')
                    ->limit(10)
                    ->get();

                $recentOrders = Order::with(['user', 'items'])
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();

                $metrics = $this->facilityMetrics($courtIds);
            } elseif ($user->isStaff()) {
                $allBookings = Booking::with(['court', 'user', 'payment'])
                    ->orderByDesc('date')
                    ->limit(10)
                    ->get();

                $recentOrders = Order::with(['user', 'items'])
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();

                $metrics = $this->facilityMetrics(null);
            } else {
                $allBookings = Booking::with(['court', 'user', 'payment'])
                    ->where('user_id', $user->id)
                    ->orderByDesc('date')
                    ->limit(10)
                    ->get();

                $featuredProducts = Product::where('quantity', '>', 0)
                    ->orderByDesc('created_at')
                    ->limit(3)
                    ->get();

                $metrics = [
                    'upcoming_bookings' => Booking::where('user_id', $user->id)
                        ->where('date', '>=', now()->toDateString())
                        ->whereIn('status', ['confirmed', 'pending', 'paid'])
                        ->count(),
                    'total_bookings' => Booking::where('user_id', $user->id)->count(),
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard load error: '.$e->getMessage());
        }

        $showTutorial = $user->isCustomer() && ! $user->tutorial_completed;

        return view('dashboard', compact(
            'user',
            'allBookings',
            'recentOrders',
            'featuredProducts',
            'metrics',
            'showTutorial'
        ));
    }

    private function facilityMetrics(?Collection $courtIds): array
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth();

        $bookingQuery = Booking::query();
        if ($courtIds !== null) {
            $bookingQuery->whereIn('court_id', $courtIds);
        }

        $todayBookings = (clone $bookingQuery)->where('date', $today)->count();
        $totalBookings = (clone $bookingQuery)->count();

        $paidRevenue = (clone $bookingQuery)
            ->whereHas('payment', fn ($q) => $q->where('status', 'paid'))
            ->with('payment')
            ->get()
            ->sum(fn ($booking) => $booking->payment?->amount ?? 0);

        $monthlyRevenue = (clone $bookingQuery)
            ->whereHas('payment', fn ($q) => $q->where('status', 'paid')->where('created_at', '>=', $monthStart))
            ->with('payment')
            ->get()
            ->sum(fn ($booking) => $booking->payment?->amount ?? 0);

        $pendingPayments = (clone $bookingQuery)
            ->whereIn('status', ['pending', 'pending_payment'])
            ->count();

        $confirmedToday = (clone $bookingQuery)
            ->where('date', $today)
            ->whereIn('status', ['confirmed', 'paid', 'completed'])
            ->count();

        $courtCount = $courtIds !== null ? $courtIds->count() : \App\Models\Court::count();
        $maxDailySlots = max($courtCount * 15, 1);
        $utilization = min(100, round(($confirmedToday / $maxDailySlots) * 100));

        return [
            'court_count' => $courtCount,
            'today_bookings' => $todayBookings,
            'total_bookings' => $totalBookings,
            'total_revenue' => $paidRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'pending_payments' => $pendingPayments,
            'utilization_today' => $utilization,
        ];
    }
}
