@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-green-900/90 rounded-3xl"></div>
    <img src="/images/badminton-hero.jpg" alt="Badminton Hero" class="w-full h-64 object-cover rounded-3xl shadow-2xl">
    <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4">
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
            <h1 class="text-5xl font-extrabold text-white drop-shadow-lg mb-4">Book Your Court</h1>
            <p class="text-xl text-blue-100 font-medium drop-shadow mb-6">Real-time availability • Instant booking • Professional courts</p>
            <div class="flex items-center justify-center gap-6 text-white/90">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                    <span class="text-sm font-medium">Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                    <span class="text-sm font-medium">Booked</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                    <span class="text-sm font-medium">My Booking</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Enhanced Date Navigation -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <button onclick="changeDate(-1)" class="p-3 rounded-xl hover:bg-blue-50 transition-colors border border-gray-200"
                        title="Previous day"
                        aria-label="Go to previous day">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Previous day</span>
                </button>
                <form method="GET" action="" class="flex items-center gap-3">
                    <div class="relative">
                        <input id="date-input" type="date" name="date" value="{{ $selectedDate }}" 
                               class="border-2 border-blue-200 rounded-xl px-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-300 text-lg font-semibold bg-white" 
                               onchange="this.form.submit()"
                               title="Select booking date"
                               aria-label="Select booking date">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <span class="text-blue-700 font-bold text-xl">{{ \Carbon\Carbon::parse($selectedDate)->format('j F Y') }}</span>
                    <button type="button" onclick="setToday()" 
                            class="px-4 py-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200 hover:bg-blue-100 transition font-medium"
                            title="Go to today"
                            aria-label="Go to today's date">
                        Today
                    </button>
                </form>
                <button onclick="changeDate(1)" class="p-3 rounded-xl hover:bg-blue-50 transition-colors border border-gray-200"
                        title="Next day"
                        aria-label="Go to next day">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sr-only">Next day</span>
                </button>
            </div>

        </div>
    </div>



    <!-- Enhanced Booking Grid -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="sticky top-0 z-20">
                    <tr class="bg-gradient-to-r from-blue-50 to-green-50">
                        <th class="px-6 py-4 border-b border-gray-200 text-center text-blue-700 text-lg font-bold w-32 bg-white"></th>
                        @foreach($courts as $court)
                            <th class="px-6 py-4 border-b border-gray-200 text-center text-blue-700 text-lg font-bold whitespace-nowrap shadow-sm" data-court-id="{{ $court->id }}">
                                <div class="flex flex-col items-center">
                                    <span class="font-bold">{{ $court->name }}</span>
                                    <span class="text-sm font-normal text-gray-600">Court {{ $loop->iteration }}</span>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $slotIdx => $slot)
                        @php
                            // Check if any court in this time slot is booked by current user
                            $hasMyBooking = false;
                            foreach($courts as $court) {
                                $booking = $bookings->first(function($b) use ($court, $slot, $timeSlots, $slotIdx) {
                                    return $b->court_id == $court->id && $b->start_time <= $slot && $b->end_time >= $slot;
                                });
                                if ($booking && $booking->user_id == auth()->id()) {
                                    $hasMyBooking = true;
                                    break;
                                }
                            }
                            $rowClass = $hasMyBooking ? 'bg-blue-50 hover:bg-blue-100' : 'hover:bg-gray-50';
                        @endphp
                    <tr class="{{ $rowClass }} transition-colors">
                        <td class="px-4 py-4 border-b border-gray-100 text-right font-bold text-blue-700 {{ $hasMyBooking ? 'bg-blue-100' : 'bg-blue-50' }} sticky left-0 z-10 text-lg">
                            {{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}
                        </td>
                        @foreach($courts as $court)
                            @php
                                $booking = $bookings->first(function($b) use ($court, $slot, $timeSlots, $slotIdx) {
                                    return $b->court_id == $court->id && $b->start_time <= $slot && $b->end_time >= $slot;
                                });
                                $isMine = $booking && $booking->user_id == auth()->id();
                                $isBooked = $booking && !$isMine;
                                $isStart = $booking && $booking->start_time == $slot;
                                $isEnd = $booking && $booking->end_time == $slot;
                                
                                // Check if user has any booking in this court during this time period
                                $hasMyBookingInCourt = $bookings->contains(function($b) use ($court, $slot) {
                                    return $b->court_id == $court->id && 
                                           $b->user_id == auth()->id() && 
                                           $b->start_time <= $slot && 
                                           $b->end_time > $slot;
                                });
                                
                                // Alternative check - maybe the time format is different
                                if (!$hasMyBookingInCourt) {
                                    $hasMyBookingInCourt = $bookings->contains(function($b) use ($court, $slot) {
                                        $slotTime = \Carbon\Carbon::createFromFormat('H:i', $slot);
                                        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->start_time);
                                        $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->end_time);
                                        
                                        return $b->court_id == $court->id && 
                                               $b->user_id == auth()->id() && 
                                               $slotTime->between($startTime, $endTime);
                                    });
                                }
                                

                                
                                // If we found a booking, use it
                                if ($hasMyBookingInCourt) {
                                    $booking = $bookings->first(function($b) use ($court, $slot) {
                                        return $b->court_id == $court->id && 
                                               $b->user_id == auth()->id() && 
                                               $b->start_time <= $slot && 
                                               $b->end_time > $slot;
                                    });
                                    
                                    // If the first check didn't find it, try the Carbon time check
                                    if (!$booking) {
                                        $booking = $bookings->first(function($b) use ($court, $slot) {
                                            $slotTime = \Carbon\Carbon::createFromFormat('H:i', $slot);
                                            $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->start_time);
                                            $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $b->end_time);
                                            
                                            return $b->court_id == $court->id && 
                                                   $b->user_id == auth()->id() && 
                                                   $slotTime->between($startTime, $endTime);
                                        });
                                    }
                                }
                                
                                $borderClass = '';
                                if ($isStart) {
                                    $borderClass .= $isMine ? ' border-l-4 border-blue-500' : ' border-l-4 border-red-500';
                                }
                                if ($isEnd) {
                                    $borderClass .= $isMine ? ' border-r-4 border-blue-500' : ' border-r-4 border-red-500';
                                }
                                $bgClass = '';
                                if ($booking) {
                                    $bgClass = $isMine ? ' bg-blue-100 text-blue-700 border-blue-300' : ' bg-blue-100 text-blue-600 border-blue-200';
                                }
                            @endphp
                            <td class="px-3 py-4 border-b border-gray-100 text-center{{ $borderClass }}{{ $bgClass }}" data-court="{{ $court->id }}" data-time="{{ $slot }}">
                                @if($hasMyBookingInCourt && $booking)
                                    <button class="my-booking-btn w-full py-3 px-4 font-semibold rounded-xl border-2 border-blue-300 bg-blue-100 text-blue-700 hover:bg-blue-200 transition-all transform hover:scale-105 shadow-sm"
                                            data-booking-id="{{ $booking->id }}">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            My Booking
                                        </div>
                                    </button>
                                @elseif($booking)
                                    <div class="flex items-center justify-center gap-2 rounded-xl py-3 px-4 w-full font-semibold text-base shadow-sm bg-blue-100 text-blue-600 border-2 border-blue-200 cursor-not-allowed">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Booked
                                    </div>
                                @else
                                    <button class="select-slot-btn w-full py-3 px-4 font-semibold rounded-xl border-2 border-green-200 bg-green-50 text-green-800 hover:bg-green-100 hover:border-green-300 transition-all transform hover:scale-105 shadow-sm" 
                                            data-court="{{ $court->id }}" data-time="{{ $slot }}"
                                            title="Book {{ $court->name }} at {{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}"
                                            aria-label="Book {{ $court->name }} at {{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i A') }}">
                                        <div class="flex items-center justify-center gap-2">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Book Now
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

    <!-- Enhanced Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full mx-4 relative border border-gray-100 animate-fade-in">
            <button id="close-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl font-bold" title="Close modal" aria-label="Close booking modal">&times;</button>
            <div id="modal-content">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <!-- Enhanced Booking Details Modal -->
    <div id="booking-details-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full mx-4 relative border border-gray-100">
            <button id="close-details-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl font-bold" title="Close modal" aria-label="Close details modal">&times;</button>
            <div id="details-modal-content">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>

    <!-- Enhanced My Bookings Modal -->
    <div id="my-bookings-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden transition-all duration-300">
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-4xl w-full mx-4 relative border border-gray-100 animate-fade-in">
            <button id="close-my-bookings-modal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl font-bold" title="Close modal" aria-label="Close my bookings modal">&times;</button>
            <h2 class="text-3xl font-bold mb-6 text-blue-700 flex items-center gap-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
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
        const endTime = (parseInt(startTime.split(':')[0]) + 1).toString().padStart(2, '0') + ':00';
        const price = 20;
        
        console.log('Modal data:', { courtName, date, startTime, endTime, price });
        
        const modal = document.getElementById('booking-modal');
        const modalContent = document.getElementById('modal-content');
        
        modalContent.innerHTML = `
            <div class="text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Confirm Your Booking</h2>
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div>
                            <span class="text-gray-600 text-sm">Court</span>
                            <p class="font-semibold text-gray-800">${courtName}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Date</span>
                            <p class="font-semibold text-gray-800">${date}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Time</span>
                            <p class="font-semibold text-gray-800">${startTime} - ${endTime}</p>
                        </div>
                        <div>
                            <span class="text-gray-600 text-sm">Duration</span>
                            <p class="font-semibold text-gray-800">1 hour</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-800">Total Price</span>
                            <span class="text-2xl font-bold text-blue-600">RM ${price}</span>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="court_id" value="${courtId}">
                    <input type="hidden" name="date" value="${date}">
                    <input type="hidden" name="start_time" value="${startTime}">
                    <input type="hidden" name="end_time" value="${endTime}">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 rounded-xl font-bold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 text-lg shadow-lg">
                        Confirm Booking
                    </button>
                </form>
            </div>
        `;
        
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('opacity-100'), 10);
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
        console.log('Found', buttons.length, 'Book Now buttons');
        
        buttons.forEach((btn, index) => {
            console.log('Setting up listener for button', index + 1, 'with data:', {
                court: btn.getAttribute('data-court'),
                time: btn.getAttribute('data-time')
            });
            
            btn.onclick = function(e) {
                e.preventDefault();
                console.log('Book Now button clicked!');
                const courtId = this.getAttribute('data-court');
                const slot = this.getAttribute('data-time');
                console.log('Court ID:', courtId, 'Slot:', slot);
                showBookingModal(courtId, slot);
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
</style>
@endsection 