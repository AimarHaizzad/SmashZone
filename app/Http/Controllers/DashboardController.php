<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NewsApiService;

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
        
        return view('dashboard', compact('user', 'badmintonNews', 'newsStatus'));
    }
}
