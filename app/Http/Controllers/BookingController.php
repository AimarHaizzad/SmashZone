<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $selectedDate = $request->query('date', now()->toDateString());
        $courts = Court::all();
        // Generate time slots (8:00 to 22:00, hourly)
        $timeSlots = [];
        for ($h = 8; $h <= 22; $h++) {
            $timeSlots[] = sprintf('%02d:00', $h);
        }
        $bookings = Booking::where('date', $selectedDate)->get();
        return view('bookings.index', compact('courts', 'timeSlots', 'bookings', 'selectedDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $courts = Court::all();
        $selectedCourtId = request('court_id');
        return view('bookings.create', compact('courts', 'selectedCourtId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for overlapping bookings
        $overlap = Booking::where('court_id', $validated['court_id'])
            ->where('date', $validated['date'])
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })->exists();
        if ($overlap) {
            return back()->withErrors(['overlap' => 'This court is already booked for the selected time.'])->withInput();
        }

        // Example price calculation: RM20 per hour
        $start = strtotime($validated['start_time']);
        $end = strtotime($validated['end_time']);
        $hours = ($end - $start) / 3600;
        $total_price = 20 * $hours;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'court_id' => $validated['court_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'pending',
            'total_price' => $total_price,
        ]);

        // Create payment record
        $booking->payment()->create([
            'user_id' => Auth::id(),
            'amount' => $total_price,
            'status' => 'pending',
            'payment_date' => null,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $courts = Court::all();
        return view('bookings.edit', compact('booking', 'courts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'court_id' => 'required|exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        // Check for overlapping bookings (exclude current booking)
        $overlap = Booking::where('court_id', $validated['court_id'])
            ->where('date', $validated['date'])
            ->where('id', '!=', $booking->id)
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })->exists();
        if ($overlap) {
            return back()->withErrors(['overlap' => 'This court is already booked for the selected time.'])->withInput();
        }

        // Example price calculation: RM20 per hour
        $start = strtotime($validated['start_time']);
        $end = strtotime($validated['end_time']);
        $hours = ($end - $start) / 3600;
        $total_price = 20 * $hours;

        $booking->update([
            'court_id' => $validated['court_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => $validated['status'],
            'total_price' => $total_price,
        ]);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
