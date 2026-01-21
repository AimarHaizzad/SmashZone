@php
    $dateCarbon = \Carbon\Carbon::parse($date);
    $isToday = $dateCarbon->isToday();
    $isTomorrow = $dateCarbon->isTomorrow();
    $isPast = $dateCarbon->isPast() && !$dateCarbon->isToday();
    
    // Determine date label
    if ($isToday) {
        $dateLabel = 'Today';
        $dateSubLabel = $dateCarbon->format('M d, Y');
    } elseif ($isTomorrow) {
        $dateLabel = 'Tomorrow';
        $dateSubLabel = $dateCarbon->format('M d, Y');
    } elseif ($isPast) {
        $dateLabel = $dateCarbon->format('M d, Y');
        $dateSubLabel = $dateCarbon->format('l');
    } else {
        $dateLabel = $dateCarbon->format('M d, Y');
        $dateSubLabel = $dateCarbon->format('l');
    }
    
    // Use the passed renderedPaymentButtons array or initialize if not set
    $renderedPaymentButtons = $renderedPaymentButtons ?? [];
@endphp

<div class="mb-6 last:mb-0">
    <!-- Date Header -->
    <div class="flex items-center justify-between mb-4 pb-3 border-b-2 {{ $sectionType === 'pending' ? 'border-yellow-300' : ($sectionType === 'upcoming' ? 'border-blue-300' : 'border-gray-300') }}">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 {{ $sectionType === 'pending' ? 'bg-yellow-100' : ($sectionType === 'upcoming' ? 'bg-blue-100' : 'bg-gray-100') }} rounded-lg flex items-center justify-center">
                @if($isToday)
                    <svg class="w-5 h-5 {{ $sectionType === 'pending' ? 'text-yellow-600' : ($sectionType === 'upcoming' ? 'text-blue-600' : 'text-gray-600') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="w-5 h-5 {{ $sectionType === 'pending' ? 'text-yellow-600' : ($sectionType === 'upcoming' ? 'text-blue-600' : 'text-gray-600') }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                @endif
            </div>
            <div>
                <h3 class="text-lg font-bold {{ $sectionType === 'pending' ? 'text-yellow-800' : ($sectionType === 'upcoming' ? 'text-blue-800' : 'text-gray-800') }}">
                    {{ $dateLabel }}
                </h3>
                <p class="text-sm {{ $sectionType === 'pending' ? 'text-yellow-600' : ($sectionType === 'upcoming' ? 'text-blue-600' : 'text-gray-600') }}">
                    {{ $dateSubLabel }} • {{ $bookings->count() }} {{ $bookings->count() === 1 ? 'booking' : 'bookings' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="space-y-3">
        @foreach($bookings->sortBy('start_time') as $booking)
            @php
                $startDateTime = \Carbon\Carbon::parse("{$booking->date} {$booking->start_time}");
                $endDateTime = \Carbon\Carbon::parse("{$booking->date} {$booking->end_time}");
                $cancelDeadline = $startDateTime->copy()->subHour();
                $isPastBooking = now()->gt($endDateTime);
                $canCancel = !$isPastBooking && now()->lt($cancelDeadline) && $booking->status !== 'cancelled' && $booking->status !== 'completed';
                $payment = $booking->payment;
                $paymentStatus = $payment ? strtolower($payment->status ?? 'pending') : 'pending';
                $paymentExpired = $paymentStatus === 'pending' && now()->greaterThanOrEqualTo($startDateTime);
                
                if ($paymentExpired) {
                    $paymentStatus = 'failed';
                }
                $paymentId = $payment ? ($payment->id ?? null) : null;
                $showPayButton = $payment && $paymentStatus === 'pending' && $paymentId && !in_array($paymentId, $renderedPaymentButtons);
            @endphp
            
            <div class="bg-gradient-to-r {{ $sectionType === 'pending' ? 'from-yellow-50 to-orange-50 border-yellow-200' : ($sectionType === 'upcoming' ? 'from-blue-50 to-green-50 border-blue-200' : 'from-gray-50 to-gray-100 border-gray-200') }} border rounded-xl p-4 hover:shadow-md transition-all">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Left: Booking Info -->
                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Court -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Court</p>
                                <p class="text-sm font-bold text-gray-900">{{ $booking->court->name }}</p>
                            </div>
                        </div>
                        
                        <!-- Time -->
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Time</p>
                            <p class="text-sm font-bold text-gray-900">
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A') }}
                            </p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->diffInHours(\Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)) }} hour(s)</p>
                        </div>
                        
                        <!-- Amount -->
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Amount</p>
                            <p class="text-lg font-bold {{ $sectionType === 'pending' ? 'text-yellow-700' : ($sectionType === 'upcoming' ? 'text-blue-600' : 'text-gray-700') }}">
                                RM {{ number_format($booking->total_price, 2) }}
                            </p>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <p class="text-xs text-gray-500 font-medium mb-1">Status</p>
                            @php
                                $statusConfig = match($paymentStatus) {
                                    'paid' => ['label' => 'Paid', 'classes' => 'bg-green-100 text-green-800', 'icon' => 'check'],
                                    'failed' => ['label' => 'Failed', 'classes' => 'bg-red-100 text-red-800', 'icon' => 'x'],
                                    'cancelled' => ['label' => 'Cancelled', 'classes' => 'bg-gray-100 text-gray-600', 'icon' => 'x'],
                                    default => ['label' => 'Pending', 'classes' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock'],
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold {{ $statusConfig['classes'] }}">
                                @if($statusConfig['icon'] === 'check')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                @elseif($statusConfig['icon'] === 'clock')
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 18L18 6M6 6l12 12" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Right: Actions -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <button class="details-btn px-3 py-2 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition-colors text-sm flex items-center gap-1" data-booking-id="{{ $booking->id }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Details
                        </button>
                        
                        @if($showPayButton)
                            @php 
                                $renderedPaymentButtons[] = $paymentId;
                                // Get booking count safely - default to 1 if we can't determine
                                $bookingCount = 1;
                                if ($payment && $payment->id) {
                                    try {
                                        // Check if bookings relationship is loaded
                                        if ($payment->relationLoaded('bookings') && $payment->bookings) {
                                            $bookingCount = $payment->bookings->count();
                                        } elseif (method_exists($payment, 'bookings')) {
                                            // Fallback to query if not loaded
                                            $bookingCount = $payment->bookings()->count() ?: 1;
                                        }
                                    } catch (\Exception $e) {
                                        // If anything fails, default to 1
                                        $bookingCount = 1;
                                    }
                                }
                            @endphp
                            <a href="{{ route('payments.pay', $payment, absolute: false) }}" class="px-3 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors text-sm flex items-center gap-1 shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Pay {{ $bookingCount > 1 ? '(' . $bookingCount . ')' : '' }}
                            </a>
                        @elseif($payment && $paymentStatus === 'pending')
                            <span class="px-3 py-2 bg-gray-50 text-gray-400 rounded-lg font-medium text-sm cursor-not-allowed flex items-center gap-1" title="Included in another pending payment">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Pay
                            </span>
                        @endif
                        
                        @if($canCancel)
                            <form action="{{ route('bookings.destroy', $booking, absolute: false) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 bg-red-50 text-red-700 rounded-lg font-medium hover:bg-red-100 transition-colors text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel
                                </button>
                            </form>
                        @else
                            @php
                                $cancelMessage = $isPastBooking 
                                    ? 'Cannot cancel - booking time has already passed' 
                                    : 'Cancellations must be made at least 1 hour before the start time';
                            @endphp
                            <span class="px-3 py-2 bg-gray-100 text-gray-400 rounded-lg font-medium text-sm cursor-not-allowed flex items-center gap-1" title="{{ $cancelMessage }}">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
