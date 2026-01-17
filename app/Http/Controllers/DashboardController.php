<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Load bookings data based on user role
        $allBookings = collect();
        $totalRevenue = 0;
        $recentOrders = collect();
        
        try {
            if ($user->isOwner()) {
                // For owners, get bookings for their courts
                $userCourts = $user->courts()->pluck('id')->toArray();
                
                if (!empty($userCourts)) {
                    $allBookings = Booking::with(['court', 'user', 'payment'])
                        ->whereIn('court_id', $userCourts)
                        ->orderBy('date', 'desc')
                        ->take(10)
                        ->get();
                    
                    // Calculate total revenue for owner's courts
                    $paidBookings = Booking::with('payment')
                        ->whereIn('court_id', $userCourts)
                        ->whereHas('payment', function($query) {
                            $query->where('status', 'paid');
                        })
                        ->get();
                    
                    $totalRevenue = $paidBookings->sum(function($booking) {
                        return $booking->payment ? $booking->payment->amount : 0;
                    });
                }
                
                // Get recent orders for owner dashboard
                try {
                    $recentOrders = Order::with(['user', 'items'])
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                } catch (\Exception $e) {
                    \Log::warning('Dashboard orders load error: ' . $e->getMessage());
                    $recentOrders = collect();
                }
                
            } elseif ($user->isStaff()) {
                // For staff, get all bookings
                $allBookings = Booking::with(['court', 'user', 'payment'])
                    ->orderBy('date', 'desc')
                    ->take(10)
                    ->get();
                
                // Calculate total revenue for all courts
                $paidBookings = Booking::with('payment')
                    ->whereHas('payment', function($query) {
                        $query->where('status', 'paid');
                    })
                    ->get();
                
                $totalRevenue = $paidBookings->sum(function($booking) {
                    return $booking->payment ? $booking->payment->amount : 0;
                });
                
                // Get recent orders for staff dashboard
                try {
                    $recentOrders = Order::with(['user', 'items'])
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                } catch (\Exception $e) {
                    \Log::warning('Dashboard orders load error: ' . $e->getMessage());
                    $recentOrders = collect();
                }
                
            } else {
                // For customers, get their own bookings
                $allBookings = Booking::with(['court', 'user', 'payment'])
                    ->where('user_id', $user->id)
                    ->orderBy('date', 'desc')
                    ->take(10)
                    ->get();
            }
        } catch (\Exception $e) {
            // If there's an error loading bookings, log it and continue with empty data
            \Log::error('Dashboard booking load error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $allBookings = collect();
            $totalRevenue = 0;
            $recentOrders = collect();
        }
        
        // Safely check tutorial_completed field
        try {
            $tutorialCompleted = (bool) ($user->tutorial_completed ?? false);
            $showTutorial = $user->isCustomer() && !$tutorialCompleted;
        } catch (\Exception $e) {
            // If tutorial_completed field doesn't exist, show tutorial for customers
            $showTutorial = $user->isCustomer();
        }
        
        return view('dashboard', compact('user', 'allBookings', 'totalRevenue', 'showTutorial', 'recentOrders'));
    }
}
