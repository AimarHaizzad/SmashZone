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
        
        // Get badminton news only
        $badmintonNews = $this->newsService->getBadmintonNews(6);
        
        // Check if NewsAPI is configured
        $newsStatus = $this->newsService->getStatus();
        
        // Load bookings data based on user role
        $allBookings = collect();
        $totalRevenue = 0;
        
        if ($user->isOwner()) {
            // For owners, get bookings for their courts
            $userCourts = $user->courts->pluck('id');
            $allBookings = Booking::with(['court', 'user', 'payment'])
                ->whereIn('court_id', $userCourts)
                ->orderBy('date', 'desc')
                ->take(10)
                ->get();
            
            // Calculate total revenue for owner's courts
            $totalRevenue = Booking::with('payment')
                ->whereIn('court_id', $userCourts)
                ->whereHas('payment', function($query) {
                    $query->where('status', 'paid');
                })
                ->get()
                ->sum('payment.amount');
                
        } elseif ($user->isStaff()) {
            // For staff, get all bookings
            $allBookings = Booking::with(['court', 'user', 'payment'])
                ->orderBy('date', 'desc')
                ->take(10)
                ->get();
            
            // Calculate total revenue for all courts
            $totalRevenue = Booking::with('payment')
                ->whereHas('payment', function($query) {
                    $query->where('status', 'paid');
                })
                ->get()
                ->sum('payment.amount');
                
        } else {
            // For customers, get their own bookings
            $allBookings = Booking::with(['court', 'user', 'payment'])
                ->where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->take(10)
                ->get();
        }
        
        return view('dashboard', compact('user', 'badmintonNews', 'newsStatus', 'allBookings', 'totalRevenue'));
    }
}
