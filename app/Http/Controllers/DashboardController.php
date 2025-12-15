<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NewsApiService;
use App\Models\Booking;
use App\Models\Court;
use App\Models\User;

class DashboardController extends Controller
{
    protected $newsService;

    public function __construct(NewsApiService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index()
    {
        $user = auth()->user();
        
        // Get badminton news only (with error handling)
        try {
            $badmintonNews = $this->newsService->getBadmintonNews(6);
            $newsStatus = $this->newsService->getStatus();
        } catch (\Exception $e) {
            // If news service fails, use empty data
            $badmintonNews = [];
            $newsStatus = ['configured' => false, 'error' => $e->getMessage()];
        }
        
        // Load bookings data based on user role
        $allBookings = collect();
        $totalRevenue = 0;
        
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
            \Log::error('Dashboard booking load error: ' . $e->getMessage());
            $allBookings = collect();
            $totalRevenue = 0;
        }
        
        return view('dashboard', compact('user', 'badmintonNews', 'newsStatus', 'allBookings', 'totalRevenue'));
    }
}
