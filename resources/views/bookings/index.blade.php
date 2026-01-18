@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-6 md:mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-green-900/90 rounded-2xl md:rounded-3xl"></div>
    <img src="/images/badminton-hero.jpg" alt="Badminton Hero" class="w-full h-40 md:h-64 object-cover rounded-2xl md:rounded-3xl shadow-2xl">
    <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-2 md:px-4">
        <div class="bg-white/10 backdrop-blur-sm rounded-xl md:rounded-2xl p-4 md:p-8 border border-white/20">
            <h1 class="text-2xl md:text-5xl font-extrabold text-white drop-shadow-lg mb-2 md:mb-4">Book Your Court</h1>
            <p class="text-sm md:text-xl text-blue-100 font-medium drop-shadow mb-3 md:mb-6">Real-time availability • Instant booking • Professional courts</p>
            <div class="flex flex-wrap items-center justify-center gap-3 md:gap-6 text-white/90" data-tutorial="legend">
                <div class="flex items-center gap-1 md:gap-2">
                    <div class="w-2 h-2 md:w-3 md:h-3 bg-green-400 rounded-full"></div>
                    <span class="text-xs md:text-sm font-medium">Available</span>
                </div>
                <div class="flex items-center gap-1 md:gap-2">
                    <div class="w-2 h-2 md:w-3 md:h-3 bg-red-400 rounded-full"></div>
                    <span class="text-xs md:text-sm font-medium">Booked</span>
                </div>
                <div class="flex items-center gap-1 md:gap-2">
                    <div class="w-2 h-2 md:w-3 md:h-3 bg-blue-400 rounded-full"></div>
                    <span class="text-xs md:text-sm font-medium">My Booking</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto py-4 md:py-8 px-2 md:px-4">
    <!-- Enhanced Date Navigation -->
    <div class="bg-white rounded-xl md:rounded-2xl shadow-lg p-3 md:p-6 mb-4 md:mb-8 border border-gray-100" data-tutorial="date-navigation">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-3 w-full md:w-auto">
                <button onclick="changeDate(-1)" class="p-2 md:p-3 rounded-lg md:rounded-xl hover:bg-blue-50 transition-colors border border-gray-200 flex-shrink-0"
                        title="Previous day"
                        aria-label="Go to previous day"
                        data-tutorial="prev-day-btn">
                    <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Previous day</span>
                </button>
                <form method="GET" action="" class="flex items-center gap-2 md:gap-3 flex-1 min-w-0">
                    <div class="relative flex-1 min-w-0">
                        <input id="date-input" type="date" name="date" value="{{ $selectedDate }}" 
                               class="border-2 border-blue-200 rounded-lg md:rounded-xl px-2 md:px-4 py-2 md:py-3 shadow-sm focus:ring-2 focus:ring-blue-300 text-sm md:text-lg font-semibold bg-white w-full" 
                               onchange="this.form.submit()"
                               title="Select booking date"
                               aria-label="Select booking date"
                               data-tutorial="date-input">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 md:pr-3 pointer-events-none">
                            <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-blue-700 font-bold text-sm md:text-xl whitespace-nowrap hidden sm:inline">{{ \Carbon\Carbon::parse($selectedDate)->format('j F Y') }}</span>
                    <span class="text-blue-700 font-bold text-xs sm:hidden">{{ \Carbon\Carbon::parse($selectedDate)->format('M j') }}</span>
                    <button type="button" onclick="setToday()" 
                            class="px-3 md:px-4 py-2 bg-blue-50 text-blue-700 rounded-lg md:rounded-xl border border-blue-200 hover:bg-blue-100 transition font-medium text-sm md:text-base whitespace-nowrap flex-shrink-0"
                            title="Go to today"
                            aria-label="Go to today's date"
                            data-tutorial="today-btn">
                        Today
                    </button>
                </form>
                <button onclick="changeDate(1)" class="p-2 md:p-3 rounded-lg md:rounded-xl hover:bg-blue-50 transition-colors border border-gray-200 flex-shrink-0"
                        title="Next day"
                        aria-label="Go to next day"
                        data-tutorial="next-day-btn">
                    <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sr-only">Next day</span>
                </button>
            </div>

        </div>
    </div>



    <!-- Responsive Table View (works on both mobile and desktop) -->
    <div class="bg-white rounded-xl md:rounded-3xl shadow-xl border border-gray-100 overflow-hidden" data-tutorial="booking-table">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="sticky top-0 z-20">
                    <tr class="bg-gradient-to-r from-blue-50 to-green-50">
                        <th class="px-2 sm:px-4 md:px-6 py-2 sm:py-3 md:py-4 border-b border-gray-200 text-center text-blue-700 text-xs sm:text-sm md:text-lg font-bold w-20 sm:w-28 md:w-32 bg-white" data-tutorial="time-column-header"></th>
                        @foreach($courts as $court)
                            <th class="px-2 sm:px-4 md:px-6 py-2 sm:py-3 md:py-4 border-b border-gray-200 text-center text-blue-700 text-xs sm:text-sm md:text-lg font-bold whitespace-nowrap shadow-sm min-w-[100px] sm:min-w-[120px]" data-court-id="{{ $court->id }}" data-tutorial="court-column-header">
                                <div class="flex flex-col items-center">
                                    <span class="font-bold text-xs sm:text-sm md:text-base">{{ $court->name }}</span>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $slotIdx => $slot)
                        @php
                            // Check if any court in this time slot is booked by current user or other users
                            $hasMyBooking = false;
                            $hasOtherBooking = false;
                            $slotTime = $slot . ':00';
                            $slotCarbon = \Carbon\Carbon::createFromFormat('H:i:s', $slotTime);
                            
                            foreach($courts as $court) {
                                $booking = $bookings->first(function($b) use ($court, $slotCarbon) {
                                    $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->start_time);
                                    $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->end_time);
                                    // Check if slot falls within booking range (start_time <= slot < end_time)
                                    return $b->court_id == $court->id && 
                                           $slotCarbon->gte($startTime) && 
                                           $slotCarbon->lt($endTime);
                                });
                                if ($booking) {
                                    if ($booking->user_id == auth()->id()) {
                                        $hasMyBooking = true;
                                    } else {
                                        $hasOtherBooking = true;
                                    }
                                }
                            }
                            $rowClass = $hasMyBooking ? 'bg-blue-50 hover:bg-blue-100' : ($hasOtherBooking ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50');
                        @endphp
                    <tr class="{{ $rowClass }} transition-colors">
                        <td class="px-2 sm:px-3 md:px-4 py-2 sm:py-3 md:py-4 border-b border-gray-100 text-right font-bold {{ $hasMyBooking ? 'text-blue-700 bg-blue-100' : ($hasOtherBooking ? 'text-red-700 bg-red-100' : 'text-blue-700 bg-blue-50') }} sticky left-0 z-10 text-xs sm:text-sm md:text-lg">
                            {{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}
                        </td>
                        @foreach($courts as $court)
                            @php
                                // Check if this slot falls within any booking's time range
                                $slotTime = $slot . ':00';
                                $slotCarbon = \Carbon\Carbon::createFromFormat('H:i:s', $slotTime);
                                
                                $booking = $bookings->first(function($b) use ($court, $slotCarbon) {
                                    $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->start_time);
                                    $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->end_time);
                                    // Check if slot falls within booking range (start_time <= slot < end_time)
                                    return $b->court_id == $court->id && 
                                           $slotCarbon->gte($startTime) && 
                                           $slotCarbon->lt($endTime);
                                });
                                
                                $isMine = $booking && $booking->user_id == auth()->id();
                                $isBooked = $booking && !$isMine;
                                
                                // Check if this is the start or end of the booking
                                $isStart = false;
                                $isEnd = false;
                                if ($booking) {
                                    $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time);
                                    $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time);
                                    $isStart = $slotCarbon->format('H:i:s') == $startTime->format('H:i:s');
                                    $isEnd = $slotCarbon->format('H:i:s') == $endTime->format('H:i:s');
                                }
                                
                                // Check if user has a booking that covers this time slot
                                $hasMyBookingInCourt = $booking && $booking->user_id == auth()->id();
                                
                                $borderClass = '';
                                if ($isStart) {
                                    $borderClass .= $isMine ? ' border-l-2 sm:border-l-4 border-blue-500' : ' border-l-2 sm:border-l-4 border-red-500';
                                }
                                if ($isEnd) {
                                    $borderClass .= $isMine ? ' border-r-2 sm:border-r-4 border-blue-500' : ' border-r-2 sm:border-r-4 border-red-500';
                                }
                                $bgClass = '';
                                if ($booking) {
                                    $bgClass = $isMine ? ' bg-blue-100 text-blue-700 border-blue-300' : ' bg-red-100 text-red-700 border-red-300';
                                }
                            @endphp
                            <td class="px-1 sm:px-2 md:px-3 py-1.5 sm:py-2.5 md:py-4 border-b border-gray-100 text-center{{ $borderClass }}{{ $bgClass }}" data-court="{{ $court->id }}" data-time="{{ $slot }}">
                                @if($hasMyBookingInCourt && $booking)
                                    @php
                                        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time);
                                        $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time);
                                        $duration = $startTime->diffInHours($endTime);
                                    @endphp
                                    <button class="my-booking-btn w-full py-1.5 sm:py-2 md:py-3 px-2 sm:px-3 md:px-4 font-semibold rounded-lg sm:rounded-xl border-2 border-blue-300 bg-blue-100 text-blue-700 hover:bg-blue-200 transition-all transform hover:scale-105 shadow-sm text-xs sm:text-sm"
                                            data-booking-id="{{ $booking->id }}">
                                        <div class="flex flex-col items-center justify-center gap-0.5 sm:gap-1">
                                            <div class="flex items-center gap-1 sm:gap-2">
                                                <svg class="h-3 w-3 sm:h-4 sm:w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span class="hidden sm:inline">
                                                    @if($isStart)
                                                        My Booking
                                                    @else
                                                        {{ $duration }}h Booking
                                                    @endif
                                                </span>
                                                <span class="sm:hidden">
                                                    @if($isStart)
                                                        Mine
                                                    @else
                                                        {{ $duration }}h
                                                    @endif
                                                </span>
                                            </div>
                                            @if($isStart)
                                                <div class="text-[10px] sm:text-xs text-blue-600 hidden sm:block">
                                                    {{ $startTime->format('g:i A') }} - {{ $endTime->format('g:i A') }}
                                                </div>
                                            @endif
                                        </div>
                                    </button>
                                @elseif($booking)
                                    @php
                                        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time);
                                        $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time);
                                        $duration = $startTime->diffInHours($endTime);
                                    @endphp
                                    <div class="other-booking-btn w-full py-1.5 sm:py-2 md:py-3 px-2 sm:px-3 md:px-4 font-semibold rounded-lg sm:rounded-xl border-2 border-red-300 bg-red-100 text-red-700 cursor-not-allowed shadow-sm text-xs sm:text-sm">
                                        <div class="flex flex-col items-center justify-center gap-0.5 sm:gap-1">
                                            <div class="flex items-center gap-1 sm:gap-2">
                                                <svg class="h-3 w-3 sm:h-4 sm:w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                <span class="hidden sm:inline">
                                                    @if($isStart)
                                                        Booked
                                                    @else
                                                        {{ $duration }}h Booked
                                                    @endif
                                                </span>
                                                <span class="sm:hidden">
                                                    @if($isStart)
                                                        Booked
                                                    @else
                                                        {{ $duration }}h
                                                    @endif
                                                </span>
                                            </div>
                                            @if($isStart)
                                                <div class="text-[10px] sm:text-xs text-red-600 hidden sm:block">
                                                    {{ $startTime->format('g:i A') }} - {{ $endTime->format('g:i A') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $slotRate = $court->hourlyRateForSlot($selectedDate, $slot);
                                    @endphp
                                    <button class="select-slot-btn w-full py-1.5 sm:py-2 md:py-3 px-2 sm:px-3 md:px-4 font-semibold rounded-lg sm:rounded-xl border-2 border-green-200 bg-green-50 text-green-800 hover:bg-green-100 hover:border-green-300 transition-all transform hover:scale-105 shadow-sm text-xs sm:text-sm" 
                                            data-court="{{ $court->id }}" data-time="{{ $slot }}" data-variant="desktop"
                                            data-price="{{ $slotRate }}" data-price-label="RM {{ number_format($slotRate, 2) }}"
                                            title="Select {{ $court->name }} at {{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}"
                                            aria-label="Select {{ $court->name }} at {{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}">
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <svg class="h-3 w-3 sm:h-4 sm:w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span class="hidden sm:inline">Select</span>
                                            <span class="text-[10px] sm:text-xs text-blue-600 font-semibold">{{ 'RM ' . number_format($slotRate, 2) }}/hr</span>
                                        </div>
                                    </button>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Multi-Slot Selection Panel -->
    <div id="multi-slot-panel" class="fixed bottom-0 left-0 right-0 transform translate-y-full transition-transform duration-300 z-[99999] pb-safe" style="display: none;" data-tutorial="multi-slot-panel">
        <div class="max-w-6xl mx-auto px-2 md:px-4 py-2 md:py-3">
            <div class="max-w-4xl mx-auto">
                <!-- Clean rounded container like date navigation -->
                <div class="bg-white rounded-lg md:rounded-xl shadow-lg border border-gray-200 p-3 md:p-4">
                    <!-- Mobile Layout (stacked) -->
                    <div class="flex flex-col md:hidden gap-3">
                        <!-- Selected Slots -->
                        <div class="flex items-start gap-2">
                            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div id="selected-slots" class="flex gap-1.5 flex-1 min-w-0 overflow-x-auto pb-1" style="scrollbar-width: thin; -ms-overflow-style: -ms-autohiding-scrollbar;">
                                <!-- Selected slots will be displayed here -->
                            </div>
                        </div>
                        
                        <!-- Summary and Actions -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-xs text-gray-700 bg-gray-50 px-2 py-1.5 rounded-lg">
                                <span id="total-slots-mobile" class="text-blue-600 font-bold">0</span> slots • 
                                <span id="total-price-mobile" class="text-green-600 font-bold">RM 0</span>
                            </div>
                            <div class="flex gap-2">
                                <button id="confirm-multi-booking-mobile" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold disabled:opacity-50 disabled:cursor-not-allowed text-xs shadow-sm" disabled>
                                    Confirm
                                </button>
                                <button id="clear-selection-mobile" class="px-2 py-1.5 text-red-600 hover:text-red-800 font-medium border border-red-200 rounded-lg hover:bg-red-50 text-xs">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Desktop Layout (horizontal) -->
                    <div class="hidden md:flex items-center justify-between gap-4">
                        <!-- Left: Title and Selected Slots -->
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800 whitespace-nowrap">Selected Time Slots:</span>
                            </div>
                            
                            <!-- Selected Slots Display -->
                            <div id="selected-slots-desktop" class="flex gap-2 flex-1 min-w-0 overflow-x-auto pb-1" style="scrollbar-width: thin; -ms-overflow-style: -ms-autohiding-scrollbar;">
                                <!-- Selected slots will be displayed here -->
                            </div>
                        </div>
                        
                        <!-- Right: Summary and Actions -->
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded-lg">
                                <span id="total-slots" class="text-blue-600 font-bold">0</span> slots • 
                                <span id="total-price" class="text-green-600 font-bold">RM 0</span>
                            </div>
                            
                            <button id="confirm-multi-booking" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold disabled:opacity-50 disabled:cursor-not-allowed text-sm shadow-sm" disabled>
                                Confirm Booking
                            </button>
                            
                            <button id="clear-selection" class="px-3 py-2 text-red-600 hover:text-red-800 font-medium border border-red-200 rounded-lg hover:bg-red-50 text-sm">
                                Clear
                            </button>
                            
                            <button id="cancel-selection" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all duration-300 p-2 md:p-4">
        <div class="bg-white rounded-xl md:rounded-3xl shadow-2xl p-4 md:p-8 max-w-md w-full mx-2 md:mx-4 relative border border-gray-100 animate-fade-in max-h-[90vh] overflow-y-auto">
            <button id="close-modal" class="absolute top-2 md:top-4 right-2 md:right-4 text-gray-400 hover:text-gray-700 text-xl md:text-2xl font-bold" title="Close modal" aria-label="Close booking modal">&times;</button>
            <div id="modal-content">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <!-- Enhanced Booking Details Modal -->
    <div id="booking-details-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden p-2 md:p-4">
        <div class="bg-white rounded-xl md:rounded-3xl shadow-2xl p-4 md:p-8 max-w-md w-full mx-2 md:mx-4 relative border border-gray-100 max-h-[90vh] overflow-y-auto">
            <button id="close-details-modal" class="absolute top-2 md:top-4 right-2 md:right-4 text-gray-400 hover:text-gray-700 text-xl md:text-2xl font-bold" title="Close modal" aria-label="Close details modal">&times;</button>
            <div id="details-modal-content">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <!-- Enhanced My Bookings Modal -->
    <div id="my-bookings-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all duration-300 p-2 md:p-4">
        <div class="bg-white rounded-xl md:rounded-3xl shadow-2xl p-4 md:p-8 max-w-4xl w-full mx-2 md:mx-4 relative border border-gray-100 animate-fade-in max-h-[90vh] overflow-y-auto">
            <button id="close-my-bookings-modal" class="absolute top-2 md:top-4 right-2 md:right-4 text-gray-400 hover:text-gray-700 text-xl md:text-2xl font-bold" title="Close modal" aria-label="Close my bookings modal">&times;</button>
            <h2 class="text-xl md:text-3xl font-bold mb-4 md:mb-6 text-blue-700 flex items-center gap-2 md:gap-3">
                <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                </svg>
                My Bookings
            </h2>
            <div id="my-bookings-list">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>
</div>

<script>
    // Version: 3.3 - Fixed panel hiding when cleared and added more debugging
    console.log('Booking system v3.3 loaded - Fixed panel hiding when cleared and added more debugging');
    
    // Multi-slot selection state
    let selectedSlots = new Map(); // Map of slotId -> {courtId, time, courtName}
    let isMultiSelectMode = false;
    const myBookingsUrl = "{{ route('bookings.my') }}";

    function formatCurrency(amount = 0) {
        const value = Number(amount || 0);
        return `RM ${value.toFixed(2)}`;
    }

    function calculateSelectedTotal() {
        let total = 0;
        selectedSlots.forEach(slot => {
            total += Number(slot.price || 0);
        });
        return total;
    }

    function addHourToTime(timeString) {
        const [hours, minutes] = timeString.split(':').map(Number);
        const nextHour = (hours + 1) % 24;
        return `${nextHour.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
    }

    function summarizeSlotsByCourt(slots) {
        const grouped = {};
        slots.forEach(slot => {
            if (!grouped[slot.court_id]) {
                grouped[slot.court_id] = {
                    courtName: slot.courtName,
                    entries: []
                };
            }
            grouped[slot.court_id].entries.push(slot);
        });

        const summaries = [];

        Object.values(grouped).forEach(group => {
            const entries = group.entries.slice().sort((a, b) => a.time.localeCompare(b.time));
            let currentStart = entries[0].time;
            let currentEnd = addHourToTime(entries[0].time);
            let currentTotal = Number(entries[0].price || 0);
            let previousTime = entries[0].time;

            for (let i = 1; i < entries.length; i++) {
                const slot = entries[i];
                const expectedNext = addHourToTime(previousTime);
                if (slot.time === expectedNext) {
                    currentEnd = addHourToTime(slot.time);
                    currentTotal += Number(slot.price || 0);
                } else {
                    summaries.push({
                        courtName: group.courtName,
                        start: currentStart,
                        end: currentEnd,
                        total: currentTotal
                    });
                    currentStart = slot.time;
                    currentEnd = addHourToTime(slot.time);
                    currentTotal = Number(slot.price || 0);
                }
                previousTime = slot.time;
            }

            summaries.push({
                courtName: group.courtName,
                start: currentStart,
                end: currentEnd,
                total: currentTotal
            });
        });

        return summaries;
    }

    function setToday() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date-input').value = today;
        document.getElementById('date-input').form.submit();
    }
    
    function changeDate(offset) {
        const input = document.getElementById('date-input');
        const date = new Date(input.value);
        date.setDate(date.getDate() + offset);
        input.value = date.toISOString().split('T')[0];
        input.form.submit();
    }

    // Multi-slot selection functions
    function isPastSlot(dateString, timeString) {
        const slotDateTime = new Date(`${dateString}T${timeString}:00`);
        const now = new Date();
        return slotDateTime <= now;
    }

    function toggleSlotSelection(courtId, time, courtName) {
        const selectedDate = document.getElementById('date-input').value;
        if (isPastSlot(selectedDate, time)) {
            alert('This time slot has already passed. Please choose a future time.');
            return;
        }

        const slotId = `${courtId}-${time}`;
        console.log('toggleSlotSelection called:', { courtId, time, courtName, slotId });
        const button = document.querySelector(`button[data-court="${courtId}"][data-time="${time}"]`);
        if (!button) {
            console.warn('No button found for slot', slotId);
            return;
        }
        const slotPrice = parseFloat(button.dataset.price || '0');
        const priceLabel = button.dataset.priceLabel || formatCurrency(slotPrice);
        
        if (selectedSlots.has(slotId)) {
            console.log('Removing slot from selection');
            selectedSlots.delete(slotId);
            updateSlotButton(courtId, time, false);
        } else {
            console.log('Adding slot to selection');
            selectedSlots.set(slotId, {
                court_id: courtId,
                time: time,
                courtName: courtName,
                price: slotPrice,
                priceLabel: priceLabel
            });
            updateSlotButton(courtId, time, true);
        }
        
        console.log('Current selectedSlots:', selectedSlots);
        updateMultiSlotPanel();
    }

    function updateSlotButton(courtId, time, isSelected) {
        const buttons = document.querySelectorAll(`button[data-court="${courtId}"][data-time="${time}"]`);
        if (!buttons.length) return;

        buttons.forEach(button => {
            const variant = button.dataset.variant === 'mobile' ? 'mobile' : 'desktop';
            const priceLabelText = button.dataset.priceLabel ? `<span class="slot-price text-xs text-blue-600 font-semibold">${button.dataset.priceLabel}/hr</span>` : '';

            if (variant === 'mobile') {
                if (isSelected) {
                    button.className = 'select-slot-btn w-full py-2.5 px-3 font-semibold rounded-lg border-2 border-blue-500 bg-blue-100 text-blue-800 hover:bg-blue-200 transition-all text-sm';
                    button.innerHTML = `
                        <div class="flex items-center justify-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Selected
                        </div>
                    `;
                } else {
                    button.className = 'select-slot-btn w-full py-2.5 px-3 font-semibold rounded-lg border-2 border-green-200 bg-green-50 text-green-800 hover:bg-green-100 hover:border-green-300 transition-all text-sm';
                    button.innerHTML = `
                        <div class="flex items-center justify-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Select
                            ${priceLabelText}
                        </div>
                    `;
                }
                return;
            }

            if (isSelected) {
                button.className = 'select-slot-btn w-full py-3 px-4 font-semibold rounded-xl border-2 border-blue-500 bg-blue-100 text-blue-800 hover:bg-blue-200 transition-all transform hover:scale-105 shadow-sm';
                button.innerHTML = `
                    <div class="flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Selected
                    </div>
                `;
            } else {
                button.className = 'select-slot-btn w-full py-3 px-4 font-semibold rounded-xl border-2 border-green-200 bg-green-50 text-green-800 hover:bg-green-100 hover:border-green-300 transition-all transform hover:scale-105 shadow-sm';
                button.innerHTML = `
                    <div class="flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Select
                        ${priceLabelText}
                    </div>
                `;
            }
        });
    }

    function updateMultiSlotPanel() {
        const panel = document.getElementById('multi-slot-panel');
        const selectedSlotsContainer = document.getElementById('selected-slots'); // Mobile
        const selectedSlotsContainerDesktop = document.getElementById('selected-slots-desktop'); // Desktop
        const totalSlots = document.getElementById('total-slots'); // Desktop
        const totalPrice = document.getElementById('total-price'); // Desktop
        const totalSlotsMobile = document.getElementById('total-slots-mobile'); // Mobile
        const totalPriceMobile = document.getElementById('total-price-mobile'); // Mobile
        const confirmButton = document.getElementById('confirm-multi-booking'); // Desktop
        const confirmButtonMobile = document.getElementById('confirm-multi-booking-mobile'); // Mobile

        console.log('updateMultiSlotPanel called, selectedSlots.size:', selectedSlots.size);
        console.log('Panel element found:', !!panel);

        if (selectedSlots.size > 0) {
            const totalPriceValue = calculateSelectedTotal();
            // Show panel
            console.log('Showing panel...');
            panel.classList.remove('translate-y-full');
            panel.style.display = 'block';
            isMultiSelectMode = true;
            
            // Create slot element HTML
            const createSlotElement = (isMobile) => {
                const slotElement = document.createElement('div');
                const sizeClass = isMobile ? 'px-2 py-1.5 text-xs' : 'px-3 py-2 text-sm';
                slotElement.className = `flex items-center gap-1.5 md:gap-2 bg-blue-50 text-blue-800 ${sizeClass} rounded-lg font-medium border border-blue-200 whitespace-nowrap shadow-sm flex-shrink-0`;
                return slotElement;
            };
            
            // Update mobile selected slots display
            if (selectedSlotsContainer) {
                selectedSlotsContainer.innerHTML = '';
                selectedSlots.forEach((slot, slotId) => {
                    const slotElement = createSlotElement(true);
                    slotElement.innerHTML = `
                        <div class="w-1.5 h-1.5 md:w-2 md:h-2 bg-blue-500 rounded-full"></div>
                        <span class="font-semibold">${formatTime(slot.time)}</span>
                        <span class="text-xs text-blue-600 font-semibold">${slot.priceLabel || formatCurrency(slot.price)}/hr</span>
                        <button onclick="removeSlot('${slotId}')" class="text-blue-600 hover:text-blue-800 hover:bg-blue-200 rounded-full p-0.5 transition-colors" title="Remove this slot">
                            <svg class="w-2.5 h-2.5 md:w-3 md:h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    selectedSlotsContainer.appendChild(slotElement);
                });
            }
            
            // Update desktop selected slots display
            if (selectedSlotsContainerDesktop) {
                selectedSlotsContainerDesktop.innerHTML = '';
                selectedSlots.forEach((slot, slotId) => {
                    const slotElement = createSlotElement(false);
                    slotElement.innerHTML = `
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="font-semibold">${formatTime(slot.time)}</span>
                        <span class="text-xs text-blue-600 font-semibold">${slot.priceLabel || formatCurrency(slot.price)}/hr</span>
                        <button onclick="removeSlot('${slotId}')" class="text-blue-600 hover:text-blue-800 hover:bg-blue-200 rounded-full p-1 transition-colors" title="Remove this slot">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    selectedSlotsContainerDesktop.appendChild(slotElement);
                });
            }
            
            // Update totals - Desktop
            if (totalSlots) totalSlots.textContent = selectedSlots.size;
            if (totalPrice) totalPrice.textContent = formatCurrency(totalPriceValue);
            if (confirmButton) confirmButton.disabled = false;
            
            // Update totals - Mobile
            if (totalSlotsMobile) totalSlotsMobile.textContent = selectedSlots.size;
            if (totalPriceMobile) totalPriceMobile.textContent = formatCurrency(totalPriceValue);
            if (confirmButtonMobile) confirmButtonMobile.disabled = false;
        } else {
            // Hide panel
            console.log('Hiding panel...');
            panel.classList.add('translate-y-full');
            panel.style.display = 'none';
            isMultiSelectMode = false;
            if (confirmButton) confirmButton.disabled = true;
            if (confirmButtonMobile) confirmButtonMobile.disabled = true;
            if (totalSlots) totalSlots.textContent = '0';
            if (totalPrice) totalPrice.textContent = formatCurrency(0);
            if (totalSlotsMobile) totalSlotsMobile.textContent = '0';
            if (totalPriceMobile) totalPriceMobile.textContent = formatCurrency(0);
        }
    }

    function removeSlot(slotId) {
        console.log('removeSlot called with slotId:', slotId);
        const slot = selectedSlots.get(slotId);
        if (slot) {
            console.log('Removing slot:', slot);
            updateSlotButton(slot.court_id, slot.time, false);
            selectedSlots.delete(slotId);
            updateMultiSlotPanel();
            console.log('Slot removed, remaining slots:', selectedSlots.size);
        } else {
            console.log('Slot not found:', slotId);
        }
    }

    function clearAllSelections() {
        console.log('clearAllSelections called, selectedSlots.size:', selectedSlots.size);
        selectedSlots.forEach((slot, slotId) => {
            console.log('Clearing slot:', slotId, slot);
            updateSlotButton(slot.court_id, slot.time, false);
        });
        selectedSlots.clear();
        console.log('selectedSlots cleared, new size:', selectedSlots.size);
        updateMultiSlotPanel();
        console.log('All selections cleared and panel updated');
    }

    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    }

    function confirmMultiBooking() {
        if (selectedSlots.size === 0) return;

        const date = document.getElementById('date-input').value;
        const slots = Array.from(selectedSlots.values());
        const totalPrice = calculateSelectedTotal();
        const summaries = summarizeSlotsByCourt(slots);
        
        // Create booking data
        const bookingData = {
            date: date,
            slots: slots,
            summary: summaries,
            total_price: totalPrice
        };

        // Show confirmation modal
        showMultiBookingConfirmation(bookingData);
    }

    function showMultiBookingConfirmation(bookingData) {
        const modal = document.getElementById('booking-modal');
        const modalContent = document.getElementById('modal-content');
        
        const slotsHtml = (bookingData.summary || []).map(slot => 
            `<div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                <span class="font-medium">${slot.courtName}</span>
                <div class="text-right">
                    <span class="block text-gray-600">${formatTime(slot.start)} - ${formatTime(slot.end)}</span>
                    <span class="block text-blue-600 font-semibold text-sm">${formatCurrency(slot.total)}</span>
                </div>
            </div>`
        ).join('');

        modalContent.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 text-gray-800">Confirm Multi-Slot Booking</h2>
                <div class="bg-gray-50 rounded-xl p-4 md:p-6 mb-4 md:mb-6">
                    <div class="text-left mb-3 md:mb-4">
                        <span class="text-gray-600 text-xs md:text-sm">Date</span>
                        <p class="font-semibold text-gray-800 text-sm md:text-base">${bookingData.date}</p>
                    </div>
                    <div class="text-left mb-3 md:mb-4">
                        <span class="text-gray-600 text-xs md:text-sm">Selected Slots</span>
                        <div class="mt-2">
                            ${slotsHtml}
                        </div>
                    </div>
                    <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-base md:text-lg font-semibold text-gray-800">Total Price</span>
                            <span class="text-xl md:text-2xl font-bold text-blue-600">${formatCurrency(bookingData.total_price)}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 md:gap-3">
                    <button id="cancel-multi-booking" class="flex-1 px-4 py-2.5 md:py-3 text-gray-600 hover:text-gray-800 font-medium text-sm md:text-base">
                        Cancel
                    </button>
                    <button id="submit-multi-booking" class="flex-1 px-4 py-2.5 md:py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm md:text-base">
                        Confirm & Pay
                    </button>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
        
        // Add event listeners
        document.getElementById('cancel-multi-booking').onclick = () => {
            modal.classList.add('hidden');
        };
        
        document.getElementById('submit-multi-booking').onclick = () => {
            submitMultiBooking(bookingData);
        };
    }

    function submitMultiBooking(bookingData) {
        console.log('Submitting multi-booking with data:', bookingData);
        
        // Disable the submit button to prevent double submission
        const submitButton = document.getElementById('submit-multi-booking');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
        }
        
        // Create form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('date', bookingData.date);
        formData.append('slots', JSON.stringify(bookingData.slots));
        formData.append('total_price', bookingData.total_price);
        
        console.log('Form data slots:', JSON.stringify(bookingData.slots));

        // Submit booking with cache-busting
        const url = '/bookings/multi?v=' + Date.now(); // Cache busting
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            },
            cache: 'no-cache'
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            // Clear selections and hide panel
            clearAllSelections();
            document.getElementById('booking-modal').classList.add('hidden');

            if (data.success && data.bookings_created === data.total_slots) {
                alert('✓ Booking Confirmation\n\nAll your selected bookings have been confirmed successfully.\n\nYou will be redirected to complete your payment.');
                window.location.href = myBookingsUrl;
                return;
            }

            if (data.bookings_created > 0) {
                const bookingText = data.bookings_created === 1 ? 'booking has' : 'bookings have';
                alert(`✓ Booking Confirmation\n\n${data.bookings_created} ${bookingText} been confirmed successfully.\n\nYou will be redirected to complete your payment.`);
                window.location.href = myBookingsUrl;
                return;
            }

            // No bookings were created at all
            let errorMsg = data.message || 'Failed to create booking';
            if (data.errors && data.errors.length > 0) {
                errorMsg += '\n\nUnavailable slots:\n' + data.errors.join('\n');
            }
            alert('Error: ' + errorMsg);
            // Re-enable the button
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Confirm & Pay';
            }
        })
        .catch(error => {
            console.error('Network or parsing error:', error);
            alert('Network error creating booking. Please check your connection and try again.');
            // Clear selections and hide panel
            clearAllSelections();
            document.getElementById('booking-modal').classList.add('hidden');
            // Re-enable the button
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Confirm & Pay';
            }
        });
    }

    // DISABLED: Enhanced Live Grid Coloring (was causing incorrect display)
    // const userId = {{ auth()->id() }};
    // const courts = @json($courts->pluck('id'));
    // const slots = @json($timeSlots);
    
    // function updateGrid() {
    //     // This function was overriding the correct server-side rendering
    //     // and causing "My Booking" slots to show as "Book Now"
    // }

    function showBookingModal(courtId, slot) {
        console.log('showBookingModal called with:', { courtId, slot });
        const courtName = document.querySelector(`th[data-court-id='${courtId}']`)?.innerText || '';
        const date = document.getElementById('date-input').value;
        const startTime = slot;
        // Calculate end time correctly (8am -> 9am, not 9am -> 10am)
        const startHour = parseInt(startTime.split(':')[0]);
        const endHour = startHour + 1;
        const endTime = endHour.toString().padStart(2, '0') + ':00';
        const price = 20;
        
        console.log('Modal data:', { courtName, date, startTime, endTime, price });
        
        const modal = document.getElementById('booking-modal');
        const modalContent = document.getElementById('modal-content');
        
        modalContent.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-4 md:mb-6">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-xl md:text-2xl font-bold mb-3 md:mb-4 text-gray-800">Confirm Your Booking</h2>
                <div class="bg-gray-50 rounded-xl p-4 md:p-6 mb-4 md:mb-6">
                    <div class="grid grid-cols-2 gap-3 md:gap-4 text-left">
                        <div>
                            <span class="text-gray-600 text-xs md:text-sm">Court</span>
                            <p class="font-semibold text-gray-800 text-sm md:text-base">${courtName}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-xs md:text-sm">Date</span>
                            <p class="font-semibold text-gray-800 text-sm md:text-base">${date}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-xs md:text-sm">Time</span>
                            <p class="font-semibold text-gray-800 text-sm md:text-base">${startTime} - ${endTime}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-xs md:text-sm">Duration</span>
                            <p class="font-semibold text-gray-800 text-sm md:text-base">1 hour</p>
                        </div>
                    </div>
                    <div class="mt-3 md:mt-4 pt-3 md:pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-base md:text-lg font-semibold text-gray-800">Total Price</span>
                            <span class="text-xl md:text-2xl font-bold text-blue-600">RM ${price}</span>
                        </div>
                    </div>
                </div>
                <button id="single-booking-submit" type="button" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 md:py-4 rounded-xl font-bold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 text-base md:text-lg shadow-lg">
                    Confirm Booking
                </button>
            </div>
        `;
        
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('opacity-100'), 10);
        
        // Add event listener for the submit button
        document.getElementById('single-booking-submit').onclick = function() {
            submitSingleBooking(courtId, date, startTime, endTime);
        };
    }
    
    function submitSingleBooking(courtId, date, startTime, endTime) {
        const submitButton = document.getElementById('single-booking-submit');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Processing...';
        }
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('court_id', courtId);
        formData.append('date', date);
        formData.append('start_time', startTime);
        formData.append('end_time', endTime);
        
        // Submit with cache-busting
        const url = '{{ route("bookings.store") }}?v=' + Date.now();
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Cache-Control': 'no-cache'
            },
            cache: 'no-cache'
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    throw err;
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            // Hide modal
            document.getElementById('booking-modal').classList.add('hidden');
            
            if (data.success) {
                alert('Booking created successfully! The page will refresh to show your booking.');
                setTimeout(() => location.reload(), 500);
            } else {
                alert('Error: ' + (data.message || 'Failed to create booking'));
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Confirm Booking';
                }
            }
        })
        .catch(error => {
            console.error('Booking error:', error);
            document.getElementById('booking-modal').classList.add('hidden');
            
            let errorMessage = 'Failed to create booking. ';
            if (error.message) {
                errorMessage += error.message;
            } else if (error.errors) {
                errorMessage += Object.values(error.errors).flat().join(', ');
            } else {
                errorMessage += 'Please check your connection and try again.';
            }
            
            alert(errorMessage);
            
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Confirm Booking';
            }
        });
    }

    // Event Listeners
    // DISABLED: These were causing the grid to override correct display
    // document.getElementById('date-input').addEventListener('change', updateGrid);
    // window.addEventListener('DOMContentLoaded', updateGrid);
    
    const closeModalBtn = document.getElementById('close-modal');
    if (closeModalBtn) {
        closeModalBtn.onclick = function() {
            document.getElementById('booking-modal').classList.add('hidden');
        };
    }
    
    const closeDetailsModalBtn = document.getElementById('close-details-modal');
    if (closeDetailsModalBtn) {
        closeDetailsModalBtn.onclick = function() {
            document.getElementById('booking-details-modal').classList.add('hidden');
        };
    }
    
    const closeMyBookingsModalBtn = document.getElementById('close-my-bookings-modal');
    if (closeMyBookingsModalBtn) {
        closeMyBookingsModalBtn.onclick = function() {
            document.getElementById('my-bookings-modal').classList.add('hidden');
        };
    }

    // Enhanced My Booking Button Listeners
    function addMyBookingBtnListeners() {
        document.querySelectorAll('.my-booking-btn').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                const bookingId = this.getAttribute('data-booking-id');
                fetch(`/booking-details/${bookingId}`)
                    .then(r => r.json())
                    .then(booking => {
                        let html = `
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold mb-4 text-gray-800">Booking Details</h2>
                                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                                    <div class="space-y-3 text-left">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Court:</span>
                                            <span class="font-semibold">${booking.court.name}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Date:</span>
                                            <span class="font-semibold">${booking.date}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Time:</span>
                                            <span class="font-semibold">${booking.start_time} - ${booking.end_time}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Price:</span>
                                            <span class="font-bold text-blue-600">RM ${booking.total_price}</span>
                                        </div>
                                    </div>
                                </div>
                        `;
                        
                        if (booking.payment && booking.payment.status === 'pending') {
                            html += `
                                <a href="/payments/${booking.payment.id}/pay" class="block w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-xl font-bold text-center mb-3 hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                                    Proceed to Payment
                                </a>
                            `;
                        }
                        
                        html += `
                                <form method="POST" action="/bookings/${booking.id}" class="mt-3">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-3 rounded-xl font-bold hover:from-red-700 hover:to-red-800 transition-all transform hover:scale-105">
                                        Cancel Booking
                                    </button>
                                </form>
                            </div>
                        `;
                        
                        document.getElementById('details-modal-content').innerHTML = html;
                        document.getElementById('booking-details-modal').classList.remove('hidden');
                    });
            };
        });
    }

    // Enhanced Slot Selection Listeners
    function addSlotSelectionListeners() {
        console.log('Setting up slot selection listeners...');
        const buttons = document.querySelectorAll('.select-slot-btn');
        console.log('Found', buttons.length, 'Select buttons');
        
        buttons.forEach((btn, index) => {
            console.log('Setting up listener for button', index + 1, 'with data:', {
                court: btn.getAttribute('data-court'),
                time: btn.getAttribute('data-time')
            });
            
            btn.onclick = function(e) {
                e.preventDefault();
                console.log('Select button clicked!');
                const courtId = this.getAttribute('data-court');
                const slot = this.getAttribute('data-time');
                const courtName = document.querySelector(`th[data-court-id='${courtId}']`)?.innerText || `Court ${courtId}`;
                console.log('Court ID:', courtId, 'Slot:', slot, 'Court Name:', courtName);
                toggleSlotSelection(courtId, slot, courtName);
            };
        });
    }

    // Enhanced My Bookings Modal
    const openMyBookingsBtn = document.getElementById('open-my-bookings');
    if (openMyBookingsBtn) {
        openMyBookingsBtn.onclick = function(e) {
        e.preventDefault();
        fetch('/user-bookings')
            .then(r => r.json())
            .then(bookings => {
                let html = '';
                if (bookings.length === 0) {
                    html = `
                        <div class="text-center py-12">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Bookings Yet</h3>
                            <p class="text-gray-500">You haven't made any bookings yet. Start by booking a court!</p>
                        </div>
                    `;
                } else {
                    html = `
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-200">
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700">Court</th>
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700">Date</th>
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700">Time</th>
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700">Status</th>
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    bookings.forEach(b => {
                        let status = b.payment && b.payment.status === 'pending' ? 'Pending Payment' : 'Booked';
                        let statusClass = b.payment && b.payment.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                        
                        html += `
                            <tr class="hover:bg-gray-50 cursor-pointer my-booking-row border-b border-gray-100" data-booking-id="${b.id}">
                                <td class="py-4 px-4 font-medium">${b.court.name}</td>
                                <td class="py-4 px-4">${b.date}</td>
                                <td class="py-4 px-4">${b.start_time} - ${b.end_time}</td>
                                <td class="py-4 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold ${statusClass}">
                                        ${status}
                                    </span>
                                </td>
                                <td class="py-4 px-4">
                                    <button class="text-blue-600 hover:text-blue-800 font-medium details-btn" data-booking-id="${b.id}">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                document.getElementById('my-bookings-list').innerHTML = html;
                document.getElementById('my-bookings-modal').classList.remove('hidden');
                
                // Add click listeners for details
                document.querySelectorAll('.details-btn, .my-booking-row').forEach(btn => {
                    btn.onclick = function(e) {
                        e.preventDefault();
                        const bookingId = this.getAttribute('data-booking-id');
                        fetch(`/booking-details/${bookingId}`)
                            .then(r => r.json())
                            .then(booking => {
                                let html = `
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <h2 class="text-2xl font-bold mb-4 text-gray-800">Booking Details</h2>
                                        <div class="bg-gray-50 rounded-xl p-6 mb-6">
                                            <div class="space-y-3 text-left">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Court:</span>
                                                    <span class="font-semibold">${booking.court.name}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Date:</span>
                                                    <span class="font-semibold">${booking.date}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Time:</span>
                                                    <span class="font-semibold">${booking.start_time} - ${booking.end_time}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Total Price:</span>
                                                    <span class="font-bold text-blue-600">RM ${booking.total_price}</span>
                                                </div>
                                            </div>
                                        </div>
                                `;
                                
                                if (booking.payment && booking.payment.status === 'pending') {
                                    html += `
                                        <a href="/payments/${booking.payment.id}/pay" class="block w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-xl font-bold text-center mb-3 hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                                            Proceed to Payment
                                        </a>
                                    `;
                                }
                                
                                html += `
                                        <form method="POST" action="/bookings/${booking.id}" class="mt-3">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 text-white py-3 rounded-xl font-bold hover:from-red-700 hover:to-red-800 transition-all transform hover:scale-105">
                                                Cancel Booking
                                            </button>
                                        </form>
                                    </div>
                                `;
                                
                                document.getElementById('details-modal-content').innerHTML = html;
                                document.getElementById('booking-details-modal').classList.remove('hidden');
                            });
                    };
                });
            });
        };
    }

    // Multi-slot panel event listeners
    function setupMultiSlotEventListeners() {
        console.log('Setting up multi-slot event listeners...');
        const clearButton = document.getElementById('clear-selection');
        const clearButtonMobile = document.getElementById('clear-selection-mobile');
        const cancelButton = document.getElementById('cancel-selection');
        const confirmButton = document.getElementById('confirm-multi-booking');
        const confirmButtonMobile = document.getElementById('confirm-multi-booking-mobile');
        
        console.log('Buttons found:', {
            clearButton: !!clearButton,
            clearButtonMobile: !!clearButtonMobile,
            cancelButton: !!cancelButton,
            confirmButton: !!confirmButton,
            confirmButtonMobile: !!confirmButtonMobile
        });
        
        if (clearButton) {
            clearButton.onclick = clearAllSelections;
            console.log('Clear button event listener set');
        }
        if (clearButtonMobile) {
            clearButtonMobile.onclick = clearAllSelections;
            console.log('Clear button mobile event listener set');
        }
        if (cancelButton) {
            cancelButton.onclick = clearAllSelections;
            console.log('Cancel button event listener set');
        }
        if (confirmButton) {
            confirmButton.onclick = confirmMultiBooking;
            console.log('Confirm button event listener set');
        }
        if (confirmButtonMobile) {
            confirmButtonMobile.onclick = confirmMultiBooking;
            console.log('Confirm button mobile event listener set');
        }
    }
    
    // Setup event listeners when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupMultiSlotEventListeners);
    } else {
        setupMultiSlotEventListeners();
    }

    // Initial event listeners setup
    console.log('Initializing booking page...');
    addSlotSelectionListeners();
    console.log('Booking page initialization complete.');
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-fade-in { animation: fade-in 0.3s cubic-bezier(0.4, 0, 0.2, 1) both; }

/* Mobile-friendly improvements */
@media (max-width: 768px) {
    /* Ensure proper spacing for mobile */
    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom, 0.5rem);
    }
    
    /* Better touch targets on mobile */
    button, .select-slot-btn, .my-booking-btn {
        min-height: 44px;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
    }
    
    /* Prevent horizontal scroll on mobile */
    body {
        overflow-x: hidden;
    }
    
    /* Custom scrollbar for selected slots */
    #selected-slots::-webkit-scrollbar,
    #selected-slots-desktop::-webkit-scrollbar {
        height: 6px;
    }
    
    #selected-slots::-webkit-scrollbar-track,
    #selected-slots-desktop::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    #selected-slots::-webkit-scrollbar-thumb,
    #selected-slots-desktop::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    #selected-slots::-webkit-scrollbar-thumb:hover,
    #selected-slots-desktop::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
}

/* Professional Tutorial Styling - Matching First Image Style */
.customTooltip {
    border-radius: 16px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
    border: none !important;
    max-width: 400px !important;
    padding: 0 !important;
    background: white !important;
    overflow: hidden !important;
}

/* Gradient Header like first image */
.introjs-tooltip-header {
    background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%) !important;
    padding: 20px 20px 16px 20px !important;
    border-bottom: none !important;
    color: white !important;
    position: relative !important;
}

.introjs-tooltip-header h3,
.introjs-tooltip-header h4 {
    color: white !important;
    margin: 0 !important;
    font-weight: 600 !important;
}

.introjs-tooltipcontent {
    padding: 20px !important;
    font-size: 14px !important;
    line-height: 1.6 !important;
    color: #374151 !important;
    background: white !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.introjs-tooltipbuttons {
    padding: 16px 20px 20px 20px !important;
    border-top: 1px solid #e5e7eb !important;
    text-align: center !important;
    background: white !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 12px !important;
}

.introjs-tooltip {
    z-index: 999999 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: absolute !important;
    max-width: 400px !important;
    min-width: 300px !important;
}

.introjs-tooltip * {
    visibility: visible !important;
}

.customHighlight {
    border-radius: 12px !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3) !important;
}

/* Skip button styled like Next/Previous buttons */
.introjs-skipbutton {
    position: absolute !important;
    top: 12px !important;
    right: 12px !important;
    z-index: 10 !important;
    margin: 0 !important;
    color: white !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    padding: 8px 16px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    cursor: pointer !important;
    backdrop-filter: blur(4px) !important;
}

.introjs-skipbutton:hover {
    background: rgba(255, 255, 255, 0.3) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    transform: translateY(-1px) !important;
}

/* Button styling */
.introjs-button {
    border-radius: 8px !important;
    padding: 10px 20px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    transition: all 0.2s ease !important;
    border: none !important;
    cursor: pointer !important;
}

.introjs-button.introjs-prevbutton {
    background: #f3f4f6 !important;
    color: #374151 !important;
    border: 1px solid #e5e7eb !important;
    flex: 1 !important;
    max-width: 48% !important;
}

.introjs-button.introjs-prevbutton:hover {
    background: #e5e7eb !important;
    border-color: #d1d5db !important;
}

.introjs-button.introjs-nextbutton {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: white !important;
    border: none !important;
    flex: 1 !important;
    max-width: 48% !important;
}

.introjs-button.introjs-nextbutton:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;
}
</style>

@if(isset($showTutorial) && $showTutorial)
    @push('scripts')
    <script>
    (function() {
        'use strict';
        
        function initBookingTutorial() {
            if (typeof introJs === 'undefined') {
                console.error('Intro.js library not loaded');
                return;
            }
            
            function elementExists(selector) {
                const element = document.querySelector(selector);
                if (!element) return false;
                const rect = element.getBoundingClientRect();
                const style = window.getComputedStyle(element);
                return rect.width > 0 && rect.height > 0 && style.display !== 'none' && style.visibility !== 'hidden';
            }
            
            // Find the first available slot button for tutorial
            function findFirstAvailableSlot() {
                const availableSlots = document.querySelectorAll('.select-slot-btn');
                if (availableSlots.length > 0) {
                    // Use the first available slot
                    const firstSlot = availableSlots[0];
                    // Add a unique ID or data attribute for targeting
                    if (!firstSlot.id) {
                        firstSlot.id = 'tutorial-first-slot';
                    }
                    return '#tutorial-first-slot';
                }
                return null;
            }
            
            const steps = [
                {
                    element: '[data-tutorial="legend"]',
                    intro: '<div style="text-align: center;"><h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 600; color: #1f2937;">🎯 Step 1: Understand the Color Legend</h3><p style="margin: 0; color: #6b7280; line-height: 1.6;">Before booking, let\'s understand what the colors mean:<br><strong style="color: #10b981;">🟢 Green</strong> = Available slots you can book<br><strong style="color: #ef4444;">🔴 Red</strong> = Already booked by others<br><strong style="color: #3b82f6;">🔵 Blue</strong> = Your own bookings</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="date-navigation"]',
                    intro: '<div style="text-align: center;"><h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 600; color: #1f2937;">📅 Step 2: Navigate Dates</h3><p style="margin: 0; color: #6b7280; line-height: 1.6;">This is the date navigation bar. You can move between dates using the arrow buttons or select a specific date. Let\'s explore each option!</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="prev-day-btn"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">⬅️ Previous Day Button</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click this <strong>left arrow</strong> button to go back to the previous day. This helps you check availability for earlier dates.</p></div>',
                    position: 'right'
                },
                {
                    element: '[data-tutorial="date-input"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📆 Step 3: Select a Date</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click on this <strong>date field</strong> to open a calendar popup. You can then click any date to jump directly to that day. The system will automatically load availability for your selected date.</p></div>',
                    position: 'top'
                },
                {
                    element: '[data-tutorial="today-btn"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📌 Today Button</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click the <strong>"Today"</strong> button to quickly jump back to today\'s date. This is useful if you\'ve navigated to a future date and want to return to today.</p></div>',
                    position: 'top'
                },
                {
                    element: '[data-tutorial="next-day-btn"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">➡️ Next Day Button</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click this <strong>right arrow</strong> button to move forward to the next day. Use this to check availability for future dates.</p></div>',
                    position: 'left'
                },
                {
                    element: '[data-tutorial="booking-table"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🏟️ Step 4: Understanding the Booking Table</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">This table shows all courts and their time slots. Each row is a time slot (like 8:00 AM, 9:00 AM), and each column is a different court. Let\'s learn how to read it!</p></div>',
                    position: 'top'
                },
                {
                    element: '[data-tutorial="time-column-header"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">⏰ Time Slots Column</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">This left column shows all available <strong>time slots</strong> throughout the day. Each row represents one hour (e.g., 8:00 AM - 9:00 AM). Scroll down to see more time slots!</p></div>',
                    position: 'right'
                },
                {
                    element: '[data-tutorial="court-column-header"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🏸 Court Columns</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Each column represents a different <strong>court</strong>. The court name is shown at the top. Scroll horizontally on mobile to see more courts. Now let\'s see how to select a slot!</p></div>',
                    position: 'bottom'
                }
            ];
            
            // Add slot selection step - will be added dynamically after table loads
            // We'll add it to validSteps after checking
            
            // Filter valid steps
            const validSteps = steps.filter(step => elementExists(step.element));
            
            // Add slot selection steps if available slots exist
            const slotSelector = findFirstAvailableSlot();
            if (slotSelector && elementExists(slotSelector)) {
                validSteps.push({
                    element: slotSelector,
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">✅ Step 5: Select Your Time Slot</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Look for <strong style="color: #10b981;">green "Select" buttons</strong> - these are available slots you can book! Each button shows:<br>• The price per hour (e.g., RM 20.00/hr)<br>• The time slot for that court<br><br>Click on any green button to select that slot. You\'ll see it highlighted when selected!</p></div>',
                    position: 'top'
                });
                
                // Add additional helpful step about multiple slots
                validSteps.push({
                    element: '[data-tutorial="booking-table"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🎯 Step 6: Booking Multiple Slots</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;"><strong>Tip:</strong> You can select multiple consecutive time slots for the same court to book longer sessions! For example, if you want to play for 2 hours, click two consecutive green buttons (like 8:00 AM and 9:00 AM) for the same court.<br><br>Each time you click a green button, it will be highlighted to show it\'s selected. You can click it again to deselect it.</p></div>',
                    position: 'top'
                });
                
                // Add step about the confirmation panel (will show when slots are selected)
                validSteps.push({
                    element: '[data-tutorial="booking-table"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">✅ Step 7: Complete Your Booking</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">After selecting your slots, a <strong>confirmation panel</strong> will appear at the bottom of the screen showing:<br>• All your selected slots<br>• Total number of slots<br>• Total price<br><br>Click <strong>"Confirm Booking"</strong> to proceed to payment, or <strong>"Clear"</strong> to start over. That\'s it! You\'re ready to book! 🎉</p></div>',
                    position: 'top'
                });
            } else {
                // If no slots available, still add a helpful final step
                validSteps.push({
                    element: '[data-tutorial="booking-table"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">✅ Step 5: How to Select a Slot</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">When slots are available, you\'ll see <strong style="color: #10b981;">green "Select" buttons</strong> in the table. Click any green button to select that time slot for booking. You can select multiple consecutive slots for longer sessions!<br><br>After selecting, a confirmation panel will appear at the bottom. Click "Confirm Booking" to complete your reservation!</p></div>',
                    position: 'top'
                });
            }
            
            if (validSteps.length === 0) return;
            
            const intro = introJs();
            intro.setOptions({
                steps: validSteps,
                showProgress: true,
                showBullets: true,
                exitOnOverlayClick: true, // Allow clicking outside to exit
                exitOnEsc: true,
                keyboardNavigation: true,
                disableInteraction: false, // Allow interactions
                scrollToElement: true,
                scrollPadding: 20,
                nextLabel: 'Next →',
                prevLabel: '← Previous',
                skipLabel: 'Skip',
                doneLabel: 'Got it! 🎉',
                tooltipClass: 'customTooltip',
                highlightClass: 'customHighlight',
                buttonClass: 'introjs-button',
                tooltipPosition: 'auto' // Let intro.js decide the best position
            });
            
            // Ensure tooltip is visible after each step and style properly
            intro.onchange(function(targetElement) {
                setTimeout(function() {
                    const tooltip = document.querySelector('.introjs-tooltip');
                    if (tooltip) {
                        tooltip.style.display = 'block';
                        tooltip.style.visibility = 'visible';
                        tooltip.style.opacity = '1';
                        tooltip.style.zIndex = '999999';
                        
                        // Ensure header has gradient and white text
                        const header = tooltip.querySelector('.introjs-tooltip-header');
                        if (header) {
                            header.style.background = 'linear-gradient(135deg, #3b82f6 0%, #10b981 100%)';
                            header.style.color = 'white';
                            const headerText = header.querySelector('h3, h4');
                            if (headerText) {
                                headerText.style.color = 'white';
                            }
                        }
                        
                        // Ensure skip button is styled as button
                        const skipButton = tooltip.querySelector('.introjs-skipbutton');
                        if (skipButton) {
                            skipButton.style.color = 'white';
                            skipButton.style.background = 'rgba(255, 255, 255, 0.2)';
                            skipButton.style.border = '1px solid rgba(255, 255, 255, 0.3)';
                            skipButton.style.padding = '8px 16px';
                            skipButton.style.borderRadius = '8px';
                            skipButton.style.fontWeight = '500';
                        }
                        
                        // Also ensure content is visible
                        const content = tooltip.querySelector('.introjs-tooltipcontent');
                        if (content) {
                            content.style.display = 'block';
                            content.style.visibility = 'visible';
                            content.style.opacity = '1';
                        }
                    }
                }, 100);
            });
            
            // Mark booking tutorial as completed
            function markBookingTutorialComplete() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    return;
                }
                
                fetch('{{ route("tutorial.complete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ type: 'booking' })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('✅ Booking tutorial completed successfully!');
                    }
                })
                .catch(error => {
                    console.error('❌ Error marking booking tutorial as completed:', error);
                });
            }
            
            // Debug: Log when tutorial starts
            intro.onstart(function() {
                console.log('Booking tutorial started with', validSteps.length, 'steps');
            });
            
            // Mark tutorial as completed when finished
            intro.oncomplete(function() {
                markBookingTutorialComplete();
            });
            
            // Ensure tutorial can be exited (also mark as completed if user exits)
            intro.onexit(function() {
                console.log('Tutorial exited');
                markBookingTutorialComplete();
            });
            
            // Wait a bit longer to ensure table is fully rendered
            setTimeout(() => {
                // Re-check for slot button before starting
                const slotSelector = findFirstAvailableSlot();
                if (slotSelector && !elementExists(slotSelector)) {
                    // Try again after a short delay
                    setTimeout(() => intro.start(), 500);
                } else {
                    intro.start();
                }
            }, 1200);
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initBookingTutorial);
        } else {
            setTimeout(initBookingTutorial, 100);
        }
    })();
    </script>
    @endpush
@endif

@endsection 