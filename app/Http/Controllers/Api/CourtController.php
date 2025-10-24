<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Court;
use Illuminate\Http\Request;

class CourtController extends Controller
{
    /**
     * Get all courts
     */
    public function index()
    {
        $courts = Court::with('owner')->get();
        
        return response()->json([
            'success' => true,
            'data' => $courts
        ]);
    }

    /**
     * Get specific court
     */
    public function show(Court $court)
    {
        $court->load('owner', 'bookings');
        
        return response()->json([
            'success' => true,
            'data' => $court
        ]);
    }
}
