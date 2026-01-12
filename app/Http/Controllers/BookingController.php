<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BookingConfirmation;
use App\Notifications\BookingCancellation;
use App\Services\RefundService;
use App\Services\WebNotificationService;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $webNotificationService;

    public function __construct(WebNotificationService $webNotificationService)
    {
        $this->webNotificationService = $webNotificationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedDate = $request->query('date', now()->toDateString());
        if ($user->isOwner()) {
            $courts = $user->courts()->with('pricingRules')->get();
            $bookings = Booking::whereIn('court_id', $courts->pluck('id'))
                ->where('date', $selectedDate)
                ->where('status', '!=', 'cancelled')
                ->get();
        } elseif ($user->isStaff()) {
            $courts = Court::with('pricingRules')->get(); // Optionally restrict to courts assigned to staff
            $bookings = Booking::where('date', $selectedDate)
                ->where('status', '!=', 'cancelled')
                ->get();
        } else {
            $courts = Court::with('pricingRules')->get();
            $bookings = Booking::where('date', $selectedDate)
                ->where('status', '!=', 'cancelled')
                ->get();
        }
        $timeSlots = [];
        for ($h = 8; $h <= 23; $h++) {
            $timeSlots[] = sprintf('%02d:00', $h);
        }
        
        // Check if user should see booking page tutorial (first time on this page)
        $showTutorial = $user->isCustomer() && !session('booking_tutorial_shown', false);
        if ($showTutorial) {
            session(['booking_tutorial_shown' => true]);
        }
        
        return view('bookings.index', compact('courts', 'timeSlots', 'bookings', 'selectedDate', 'showTutorial'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('bookings.index', request()->only('court_id'));
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

        // Check for overlapping bookings (excluding cancelled bookings)
        $overlap = Booking::where('court_id', $validated['court_id'])
            ->where('date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($validated) {
                // Check if new booking overlaps with existing booking
                $query->where(function($q) use ($validated) {
                    // New booking starts before existing ends AND new booking ends after existing starts
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                });
            })->exists();
        if ($overlap) {
            // Check if this is an AJAX request
            if ($validated['date'] && request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This court is already booked for the selected time.'
                ], 422);
            }
            return back()->withErrors(['overlap' => 'This court is already booked for the selected time.'])->withInput();
        }

        $court = Court::with('pricingRules')->findOrFail($validated['court_id']);
        $total_price = $court->calculatePriceForRange(
            $validated['date'],
            $validated['start_time'],
            $validated['end_time']
        );

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'court_id' => $validated['court_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'pending',
            'total_price' => $total_price,
        ]);

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'amount' => $total_price,
            'status' => 'pending',
            'payment_date' => null,
        ]);

        $booking->update(['payment_id' => $payment->id]);
        $payment->update(['booking_id' => $booking->id]);

        // Send booking confirmation email
        try {
            $booking->user->notify(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            // Log the error but don't fail the booking creation
            \Log::error('Failed to send booking confirmation email: ' . $e->getMessage());
        }

        // Send web notifications
        try {
            $this->webNotificationService->notifyNewBooking($booking);
        } catch (\Exception $e) {
            // Log the error but don't fail the booking creation
            \Log::error('Failed to send web notification for new booking: ' . $e->getMessage());
        }

        // Check if this is an AJAX request
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking_id' => $booking->id
            ]);
        }

        return redirect()->route('bookings.index', absolute: false)->with('success', 'Booking created successfully.');
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
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login', absolute: false)->with('error', 'Please login to update bookings.');
            }

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
            try {
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
            } catch (\Exception $e) {
                \Log::error('Failed to check booking overlap', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
                return back()->withErrors(['error' => 'Failed to check availability. Please try again.'])->withInput();
            }

            try {
                $court = Court::with('pricingRules')->findOrFail($validated['court_id']);
                $total_price = $court->calculatePriceForRange(
                    $validated['date'],
                    $validated['start_time'],
                    $validated['end_time']
                );
            } catch (\Exception $e) {
                \Log::error('Failed to calculate booking price', [
                    'court_id' => $validated['court_id'],
                    'error' => $e->getMessage()
                ]);
                return back()->withErrors(['error' => 'Failed to calculate price. Please try again.'])->withInput();
            }

            $booking->update([
                'court_id' => $validated['court_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => $validated['status'],
                'total_price' => $total_price,
            ]);

            if ($booking->payment) {
                try {
                    $newTotal = $booking->payment->bookings()->sum('total_price');
                    $booking->payment->update([
                        'amount' => $newTotal,
                        'booking_id' => $booking->payment->bookings()->orderBy('date')->orderBy('start_time')->value('id'),
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to update payment amount', [
                        'payment_id' => $booking->payment->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue even if payment update fails
                }
            }

            return redirect()->route('bookings.index', absolute: false)->with('success', 'Booking updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Booking update failed', [
                'booking_id' => $booking->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to update booking. Please try again.'])->withInput();
        }
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

        if ($user->isCustomer()) {
            $bookingStart = Carbon::parse("{$booking->date} {$booking->start_time}");
            $bookingEnd = Carbon::parse("{$booking->date} {$booking->end_time}");
            $cancelDeadline = $bookingStart->copy()->subHour();

            // Check if booking time has already passed
            if (now()->greaterThan($bookingEnd)) {
                return back()->withErrors([
                    'cancel' => 'Cannot cancel - booking time has already passed.'
                ]);
            }

            // Check if within cancellation deadline (1 hour before start)
            if (now()->greaterThanOrEqualTo($cancelDeadline)) {
                return back()->withErrors([
                    'cancel' => 'Cancellations must be made at least 1 hour before the start time.'
                ]);
            }

            // Check if booking is already cancelled or completed
            if ($booking->status === 'cancelled' || $booking->status === 'completed') {
                return back()->withErrors([
                    'cancel' => 'Cannot cancel - booking is already ' . $booking->status . '.'
                ]);
            }
        }

        // Store booking info before deletion for notifications
        $bookingInfo = [
            'court_name' => $booking->court->name,
            'date' => $booking->date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'user' => $booking->user,
        ];

        // Process refund if payment was made and is paid
        $refundService = new RefundService();
        $refund = null;
        
        // Always try to process refund if payment exists and is paid
        if ($booking->payment && $booking->payment->status === 'paid') {
            try {
                if ($refundService->isEligibleForRefund($booking)) {
                    $refund = $refundService->processRefund($booking, 'Booking cancelled by ' . ($user->isCustomer() ? 'customer' : 'staff/owner'));
                } else {
                    \Log::info('Booking not eligible for refund', [
                        'booking_id' => $booking->id,
                        'payment_id' => $booking->payment->id,
                        'payment_status' => $booking->payment->status
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to process refund during booking cancellation', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
                // Continue with cancellation even if refund fails
            }
        }

        // Send cancellation notification
        try {
            $booking->user->notify(new BookingCancellation($booking, 'Booking cancelled by ' . ($user->isCustomer() ? 'customer' : 'staff/owner')));
        } catch (\Exception $e) {
            \Log::error('Failed to send booking cancellation email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }

        $payment = $booking->payment;

        // Mark booking as cancelled instead of deleting (so refund can reference it)
        $booking->update(['status' => 'cancelled']);

        if ($payment) {
            // Recalculate payment amount based on remaining (non-cancelled) bookings
            $remainingBookings = $payment->bookings()->where('status', '!=', 'cancelled')->get();
            $remainingTotal = $remainingBookings->sum('total_price');
            
            if ($remainingTotal <= 0) {
                // All bookings cancelled - can delete payment if no refunds exist
                $hasRefunds = $payment->refunds()->exists();
                if (!$hasRefunds) {
                    $payment->delete();
                } else {
                    // Keep payment record for refund tracking
                    // Don't change status - keep as 'paid' since refund is tracked separately
                    $payment->update([
                        'amount' => 0,
                        // Status remains 'paid' - refunds are tracked in refunds table
                    ]);
                }
            } else {
                $payment->update([
                    'amount' => $remainingTotal,
                    'booking_id' => $remainingBookings->orderBy('date')->orderBy('start_time')->value('id'),
                ]);
            }
        }

        $message = 'Booking cancelled successfully.';
        if ($refund) {
            $message .= ' A refund of ' . $refund->formatted_amount . ' has been processed.';
        }

        return redirect()->route('bookings.index')->with('success', $message);
    }

    public function availability(Request $request)
    {
        $courtId = $request->input('court_id');
        $date = $request->input('date');
        $bookings = \App\Models\Booking::where('court_id', $courtId)
            ->where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->get(['start_time', 'end_time']);
        return response()->json($bookings);
    }

    public function gridAvailability(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $bookings = \App\Models\Booking::where('date', $date)
            ->where('status', '!=', 'cancelled')
            ->get(['court_id', 'start_time', 'end_time', 'user_id']);
        return response()->json($bookings);
    }

    public function showDetails(Request $request, $id)
    {
        $booking = \App\Models\Booking::with(['court', 'payment'])->where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        return response()->json($booking);
    }

    public function userBookings(Request $request)
    {
        $bookings = \App\Models\Booking::with(['court', 'payment.bookings'])
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

        $bookings = Booking::with(['court', 'payment.bookings.court'])
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->orderBy('start_time')
            ->get();

        $uniquePayments = $bookings->pluck('payment')->filter()->unique('id');
        $pendingPaymentsCount = $uniquePayments->where('status', 'pending')->count();
        $paidPaymentsCount = $uniquePayments->where('status', 'paid')->count();
        $totalSpent = $uniquePayments->where('status', 'paid')->sum('amount');

        return view('bookings.my', compact('bookings', 'pendingPaymentsCount', 'paidPaymentsCount', 'totalSpent'));
    }

    /**
     * Mark a booking as completed (customer has played)
     */
    public function markCompleted(Booking $booking)
    {
        // Check if user is owner or staff
        if (!Auth::user() || (!Auth::user()->isOwner() && !Auth::user()->isStaff())) {
            abort(403, 'Unauthorized to mark bookings as completed');
        }

        // Check if booking belongs to owner's courts (for owners)
        if (Auth::user()->isOwner()) {
            $userCourts = Auth::user()->courts->pluck('id');
            if (!$userCourts->contains($booking->court_id)) {
                abort(403, 'Unauthorized to mark this booking as completed');
            }
        }

        // Update booking status to completed
        $booking->update(['status' => 'completed']);

        // Send web notifications
        try {
            $this->webNotificationService->notifyBookingCompleted($booking);
        } catch (\Exception $e) {
            \Log::error('Failed to send web notification for completed booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->back()->with('success', 'Booking marked as completed successfully!');
    }

    /**
     * Cancel a booking (for late customers)
     */
    public function cancel(Booking $booking)
    {
        // Check if user is owner or staff
        if (!Auth::user() || (!Auth::user()->isOwner() && !Auth::user()->isStaff())) {
            abort(403, 'Unauthorized to cancel bookings');
        }

        // Check if booking belongs to owner's courts (for owners)
        if (Auth::user()->isOwner()) {
            $userCourts = Auth::user()->courts->pluck('id');
            if (!$userCourts->contains($booking->court_id)) {
                abort(403, 'Unauthorized to cancel this booking');
            }
        }

        // Check if booking can be cancelled (not already completed or cancelled)
        if ($booking->status === 'completed' || $booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot cancel this booking - already completed or cancelled');
        }

        // Check if customer is late (more than 30 minutes)
        $bookingDateTime = \Carbon\Carbon::parse($booking->date . ' ' . $booking->start_time);
        $now = \Carbon\Carbon::now();
        $isLate = $now->diffInMinutes($bookingDateTime, false) > 30;

        if (!$isLate) {
            return redirect()->back()->with('error', 'Cannot cancel booking - customer is not late enough (must be 30+ minutes late)');
        }

        // Process refund if payment was made
        $refundService = new RefundService();
        $refund = null;
        
        try {
            if ($refundService->isEligibleForRefund($booking)) {
                $refund = $refundService->processRefund($booking, 'Booking cancelled by staff/owner - customer was late');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to process refund during booking cancellation', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            // Continue with cancellation even if refund fails
        }

        // Update booking status to cancelled
        $booking->update(['status' => 'cancelled']);

        // Send web notifications
        try {
            $this->webNotificationService->notifyBookingCancelled($booking);
        } catch (\Exception $e) {
            \Log::error('Failed to send web notification for cancelled booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }

        // Send cancellation notification
        try {
            $booking->user->notify(new BookingCancellation($booking, 'Booking cancelled by staff/owner - customer was late'));
        } catch (\Exception $e) {
            \Log::error('Failed to send booking cancellation email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }

        // Handle different cancellation rules based on payment method
        $message = 'Booking cancelled successfully.';
        
        if ($booking->payment) {
            $paymentMethod = $booking->payment->payment_method ?? 'online';
            
            if ($paymentMethod === 'pay_at_counter') {
                // For pay at counter: court becomes available immediately
                // No refund needed since payment wasn't processed
                $message .= ' Court is now available for new bookings.';
            } else {
                // For online payments: court remains unavailable until booking time ends
                // This prevents double booking and maintains payment integrity
                $message .= ' Court will remain unavailable until the original booking time ends to maintain payment integrity.';
            }
        } else {
            // No payment record - court becomes available immediately
            $message .= ' Court is now available for new bookings.';
        }
        
        // Add refund information to message
        if ($refund) {
            $message .= ' A refund of RM ' . number_format($refund->amount, 2) . ' has been processed.';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Store multiple slot bookings
     */
    public function storeMulti(Request $request)
    {
        if (!Auth::user()->isCustomer()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Handle JSON string for slots
        $slots = $request->input('slots');
        \Log::info('Raw slots input:', ['slots' => $slots, 'type' => gettype($slots)]);
        
        if (is_string($slots)) {
            $slots = json_decode($slots, true);
            \Log::info('Decoded slots:', ['slots' => $slots, 'type' => gettype($slots)]);
        }
        
        \Log::info('Final slots before validation:', ['slots' => $slots]);

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'total_price' => 'nullable|numeric|min:0',
        ]);

        // Validate slots manually
        if (!is_array($slots) || empty($slots)) {
            return response()->json(['success' => false, 'message' => 'Slots must be an array'], 400);
        }

        foreach ($slots as $index => $slot) {
            // Handle both court_id and courtId for backward compatibility
            $courtId = $slot['court_id'] ?? $slot['courtId'] ?? null;
            
            if (!$courtId || !isset($slot['time'])) {
                return response()->json(['success' => false, 'message' => "Slot {$index} is missing required fields (court_id and time)"], 400);
            }
            if (!\App\Models\Court::where('id', $courtId)->exists()) {
                return response()->json(['success' => false, 'message' => "Court {$courtId} does not exist"], 400);
            }
            if (!preg_match('/^\d{2}:\d{2}$/', $slot['time'])) {
                return response()->json(['success' => false, 'message' => "Invalid time format for slot {$index}"], 400);
            }
            
            // Normalize the slot data
            $slots[$index]['court_id'] = $courtId;
        }

        $validated['slots'] = $slots;

        $bookings = [];
        $errors = [];

        // Group slots by court and merge consecutive times
        $slotsByCourt = [];
        foreach ($validated['slots'] as $slot) {
            $slotsByCourt[$slot['court_id']][] = $slot['time'];
        }

        $groupedSlots = [];
        foreach ($slotsByCourt as $courtId => $times) {
            $uniqueTimes = array_values(array_unique($times));
            sort($uniqueTimes);

            $groupStart = null;
            $previousTime = null;
            $slotCount = 0;

            foreach ($uniqueTimes as $time) {
                if ($groupStart === null) {
                    $groupStart = $time;
                    $previousTime = $time;
                    $slotCount = 1;
                    continue;
                }

                $expectedNext = date('H:i', strtotime($previousTime . ' +1 hour'));
                if ($time === $expectedNext) {
                    $slotCount++;
                    $previousTime = $time;
                } else {
                    $groupedSlots[] = [
                        'court_id' => $courtId,
                        'start_time' => $groupStart . ':00',
                        'end_time' => date('H:i:s', strtotime($previousTime . ' +1 hour')),
                        'hours' => $slotCount,
                    ];

                    $groupStart = $time;
                    $previousTime = $time;
                    $slotCount = 1;
                }
            }

            if ($groupStart !== null) {
                $groupedSlots[] = [
                    'court_id' => $courtId,
                    'start_time' => $groupStart . ':00',
                    'end_time' => date('H:i:s', strtotime($previousTime . ' +1 hour')),
                    'hours' => $slotCount,
                ];
            }
        }

        $courtMap = Court::with('pricingRules')
            ->whereIn('id', collect($groupedSlots)->pluck('court_id')->unique())
            ->get()
            ->keyBy('id');

        $totalAmount = 0;

        // Process each grouped slot block
        foreach ($groupedSlots as $slotGroup) {
            $startTime = $slotGroup['start_time'];
            $endTime = $slotGroup['end_time'];
            $court = $courtMap->get($slotGroup['court_id']);
            if (!$court) {
                $errors[] = "Court {$slotGroup['court_id']} is no longer available.";
                continue;
            }

            // Check for overlapping bookings with more precise logic
            $overlap = Booking::where('court_id', $slotGroup['court_id'])
                ->where('date', $validated['date'])
                ->where('status', '!=', 'cancelled') // Don't consider cancelled bookings
                ->where(function($query) use ($startTime, $endTime) {
                    // Check if new booking overlaps with existing booking
                    $query->where(function($q) use ($startTime, $endTime) {
                        // New booking starts before existing ends AND new booking ends after existing starts
                        $q->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                    });
                })->first();

            if ($overlap) {
                $courtName = $court->name ?? "Court {$slotGroup['court_id']}";
                $errors[] = "{$courtName} from " . date('g:i A', strtotime($startTime)) . " to " . date('g:i A', strtotime($endTime)) . " is already booked (existing: " . date('g:i A', strtotime($overlap->start_time)) . " - " . date('g:i A', strtotime($overlap->end_time)) . ")";
                continue;
            }

            $startForPrice = substr($startTime, 0, 5);
            $endForPrice = substr($endTime, 0, 5);
            $calculatedPrice = $court->calculatePriceForRange($validated['date'], $startForPrice, $endForPrice);

            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'court_id' => $slotGroup['court_id'],
                'date' => $validated['date'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'pending',
                'total_price' => $calculatedPrice,
            ]);

            $bookings[] = $booking;
            $totalAmount += $calculatedPrice;

            // Send booking confirmation email
            try {
                $booking->user->notify(new BookingConfirmation($booking));
            } catch (\Exception $e) {
                \Log::error('Failed to send booking confirmation email', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Send web notifications
            try {
                $this->webNotificationService->notifyNewBooking($booking);
            } catch (\Exception $e) {
                \Log::error('Failed to send web notification for new booking', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if (count($bookings) > 0 && $totalAmount > 0) {
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'amount' => $totalAmount,
                'status' => 'pending',
                'payment_date' => null,
            ]);

            foreach ($bookings as $booking) {
                $booking->update(['payment_id' => $payment->id]);
            }

            $payment->update(['booking_id' => $bookings[0]->id]);
        }

        if (!empty($errors)) {
            $message = count($bookings) > 0 
                ? count($bookings) . ' booking(s) created successfully, but ' . count($errors) . ' slot(s) could not be booked'
                : 'No bookings could be created - all selected slots are unavailable';
            
            \Log::info('Multi-slot booking partial success', [
                'bookings_created' => count($bookings),
                'errors' => $errors,
                'user_id' => Auth::id()
            ]);
                
            if (count($bookings) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'errors' => $errors,
                    'bookings_created' => 0,
                    'total_slots' => count($validated['slots'])
                ]);
            }
        }

        if (count($bookings) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No bookings could be created - all selected slots are unavailable',
                'errors' => $errors,
            ]);
        }

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'amount' => $totalAmount,
            'status' => 'pending',
            'payment_date' => null,
        ]);

        foreach ($bookings as $booking) {
            $booking->update(['payment_id' => $payment->id]);
        }

        $primaryBookingId = $payment->bookings()->orderBy('date')->orderBy('start_time')->value('id');
        $payment->update(['booking_id' => $primaryBookingId]);

        \Log::info('Multi-slot booking completed successfully', [
            'bookings_created' => count($bookings),
            'user_id' => Auth::id(),
            'payment_id' => $payment->id,
            'errors' => $errors,
        ]);

        return response()->json([
            'success' => count($bookings) > 0,
            'message' => empty($errors)
                ? 'All ' . count($bookings) . ' booking(s) created successfully'
                : count($bookings) . ' booking(s) created successfully, but ' . count($errors) . ' slot(s) could not be booked',
            'bookings_created' => count($bookings),
            'errors' => $errors,
            'payment_id' => $payment->id,
        ]);
    }
}
