<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BookingConfirmation;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedDate = $request->query('date', now()->toDateString());
        if ($user->isOwner()) {
            $courts = $user->courts;
            $bookings = Booking::whereIn('court_id', $courts->pluck('id'))->where('date', $selectedDate)->get();
        } elseif ($user->isStaff()) {
            $courts = Court::all(); // Optionally restrict to courts assigned to staff
            $bookings = Booking::where('date', $selectedDate)->get();
        } else {
            $courts = Court::all();
            $bookings = Booking::where('user_id', $user->id)->where('date', $selectedDate)->get();
        }
        $timeSlots = [];
        for ($h = 8; $h <= 22; $h++) {
            $timeSlots[] = sprintf('%02d:00', $h);
        }
        return view('bookings.index', compact('courts', 'timeSlots', 'bookings', 'selectedDate'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->isCustomer()) {
            abort(403);
        }
        $courts = Court::all();
        $selectedCourtId = request('court_id');
        return view('bookings.create', compact('courts', 'selectedCourtId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isCustomer()) {
            abort(403);
        }
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
            'booking_id' => $booking->id,
            'amount' => $total_price,
            'status' => 'pending',
            'payment_date' => null,
        ]);

        // Send booking confirmation email
        try {
            $booking->user->notify(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            // Log the error but don't fail the booking creation
            \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
        }

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
        $user = Auth::user();
        if ($user->isCustomer() && $booking->user_id !== $user->id) {
            abort(403);
        }
        if ($user->isOwner() && $booking->court->owner_id !== $user->id) {
            abort(403);
        }
        $courts = Court::all();
        return view('bookings.edit', compact('booking', 'courts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $user = Auth::user();
        if ($user->isCustomer() && $booking->user_id !== $user->id) {
            abort(403);
        }
        if ($user->isOwner() && $booking->court->owner_id !== $user->id) {
            abort(403);
        }
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
        $user = Auth::user();
        if ($user->isCustomer() && $booking->user_id !== $user->id) {
            abort(403);
        }
        if ($user->isOwner() && $booking->court->owner_id !== $user->id) {
            abort(403);
        }
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }

    public function availability(Request $request)
    {
        $courtId = $request->input('court_id');
        $date = $request->input('date');
        $bookings = \App\Models\Booking::where('court_id', $courtId)
            ->where('date', $date)
            ->get(['start_time', 'end_time']);
        return response()->json($bookings);
    }

    public function gridAvailability(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $bookings = \App\Models\Booking::where('date', $date)->get(['court_id', 'start_time', 'end_time', 'user_id']);
        return response()->json($bookings);
    }

    public function showDetails(Request $request, $id)
    {
        $booking = \App\Models\Booking::with(['court', 'payment'])->where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        return response()->json($booking);
    }

    public function userBookings(Request $request)
    {
        $bookings = \App\Models\Booking::with(['court', 'payment'])
            ->where('user_id', auth()->id())
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->get();
        return response()->json($bookings);
    }

    /**
     * Customer bookings list page.
     */
    public function my(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isCustomer()) {
            abort(403);
        }

        $bookings = Booking::with(['court', 'payment'])
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->get();

        return view('bookings.my', compact('bookings'));
    }
}
