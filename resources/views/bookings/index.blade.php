@extends('layouts.app')

@section('content')
<div class="relative mb-8">
    <img src="/images/badminton-hero.jpg" alt="Badminton Hero" class="w-full h-56 object-cover rounded-2xl shadow-lg">
    <div class="absolute inset-0 flex flex-col justify-center items-center bg-gradient-to-t from-black/60 to-transparent rounded-2xl">
        <h1 class="text-4xl font-extrabold text-white drop-shadow mb-2">Book Your Badminton Court</h1>
        <p class="text-lg text-blue-100 font-semibold drop-shadow">Real-time availability. Easy booking. Play more.</p>
    </div>
</div>
<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div class="flex items-center gap-2">
            <button onclick="changeDate(-1)" class="p-2 rounded-full hover:bg-blue-100 transition"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-blue-600' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 19l-7-7 7-7' /></svg></button>
            <form method="GET" action="" class="flex items-center gap-2">
                <input id="date-input" type="date" name="date" value="{{ $selectedDate }}" class="border-2 border-blue-200 rounded-lg px-3 py-1 shadow-sm focus:ring-2 focus:ring-blue-300 text-lg font-semibold" onchange="this.form.submit()">
                <span class="text-blue-700 font-bold text-lg">{{ \Carbon\Carbon::parse($selectedDate)->format('j F Y') }}</span>
                <button type="button" onclick="setToday()" class="ml-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 hover:bg-blue-100 transition">Today</button>
            </form>
            <button onclick="changeDate(1)" class="p-2 rounded-full hover:bg-blue-100 transition"><svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5 text-blue-600' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7' /></svg></button>
        </div>
        <a href="#" id="open-my-bookings" class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 font-semibold">My Bookings</a>
    </div>
    <div class="overflow-x-auto rounded-2xl shadow-lg border border-blue-100 bg-white">
        <table class="min-w-full">
            <thead class="sticky top-0 z-20 shadow-md">
                <tr>
                    <th class="px-4 py-3 border-b text-center bg-blue-50 rounded-tl-2xl text-blue-700 text-lg font-bold w-28"></th>
                    @foreach($courts as $court)
                        <th class="px-4 py-3 border-b text-center bg-blue-50 text-blue-700 text-lg font-bold whitespace-nowrap shadow-sm" data-court-id="{{ $court->id }}">{{ $court->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($timeSlots as $slotIdx => $slot)
                <tr>
                    <td class="px-2 py-3 border-b text-right font-semibold text-blue-700 bg-blue-50 sticky left-0 z-10 text-base">{{ \Carbon\Carbon::createFromFormat('H:i', $slot)->format('g:i a') }}</td>
                    @foreach($courts as $court)
                        @php
                            $booking = $bookings->first(function($b) use ($court, $slot) {
                                return $b->court_id == $court->id && $b->start_time <= $slot && $b->end_time > $slot;
                            });
                            $isMine = $booking && $booking->user_id == auth()->id();
                            $isBooked = $booking && !$isMine;
                            // Detect start and end
                            $isStart = $booking && $booking->start_time == $slot;
                            $isEnd = $booking && (
                                (isset($timeSlots[$slotIdx+1]) && $booking->end_time == $timeSlots[$slotIdx+1]) ||
                                (!isset($timeSlots[$slotIdx+1]) && $booking->end_time == \Carbon\Carbon::createFromFormat('H:i', $slot)->addHour()->format('H:i'))
                            );
                            $borderClass = '';
                            if ($isStart) {
                                $borderClass .= $isMine ? ' border-l-4 border-blue-500' : ' border-l-4 border-red-500';
                            }
                            if ($isEnd) {
                                $borderClass .= $isMine ? ' border-r-4 border-blue-500' : ' border-r-4 border-red-500';
                            }
                            $bgClass = '';
                            if ($booking) {
                                $bgClass = $isMine ? ' bg-blue-100 text-blue-700 border-blue-300' : ' bg-red-100 text-red-600 border-red-200';
                            }
                        @endphp
                        <td class="px-2 py-3 border-b text-center{{ $borderClass }}{{ $bgClass }}" data-court="{{ $court->id }}" data-time="{{ $slot }}">
                            @if($booking)
                                @if($isMine && $isStart)
                                    <button class="my-booking-btn border rounded-xl w-full py-2 font-semibold shadow-sm border-blue-300 bg-blue-100 text-blue-700"
                                        data-booking-id="{{ $booking->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg> My Booking
                                    </button>
                                @elseif($isBooked && $isStart)
                                    <div class="relative group select-none">
                                        <div class="flex items-center justify-center gap-1 rounded-xl py-2 px-2 w-full font-semibold text-base shadow-sm bg-red-100 text-red-600 border border-red-200 cursor-not-allowed">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            Booked
                                        </div>
                                    </div>
                                @endif
                            @else
                                <button class="bg-green-50 hover:bg-green-200 text-green-800 font-semibold rounded-xl w-full py-2 flex items-center justify-center gap-1 border border-green-200 shadow-sm transition select-slot-btn text-base" data-court="{{ $court->id }}" data-time="{{ $slot }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Book
                                </button>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Booking Modal -->
    <div id="booking-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full relative border border-blue-100 animate-fade-in">
            <button id="close-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            <div id="modal-content">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>
    <!-- Booking Details Modal -->
    <div id="booking-details-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full relative border border-blue-100">
            <button id="close-details-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            <div id="details-modal-content">
                <!-- Filled by JS -->
            </div>
        </div>
    </div>
    <!-- My Bookings List Modal -->
    <div id="my-bookings-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-2xl w-full relative border border-blue-100 animate-fade-in">
            <button id="close-my-bookings-modal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
            <h2 class="text-2xl font-bold mb-4 text-blue-700">My Bookings</h2>
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
    // --- Live grid coloring ---
    const userId = {{ auth()->id() }};
    const courts = @json($courts->pluck('id'));
    const slots = @json($timeSlots);
    function updateGrid() {
        const date = document.getElementById('date-input').value;
        fetch(`/booking-grid-availability?date=${date}`)
            .then(r => r.json())
            .then(bookings => {
                courts.forEach((courtId, cIdx) => {
                    slots.forEach((slot, sIdx) => {
                        const cell = document.querySelector(`[data-court='${courtId}'][data-time='${slot}']`);
                        if (!cell) return;
                        const booking = bookings.find(b => b.court_id == courtId && b.start_time <= slot && b.end_time > slot);
                        cell.classList.remove('bg-red-100', 'text-red-600', 'bg-blue-100', 'text-blue-700', 'bg-green-50', 'hover:bg-green-200', 'text-green-800', 'border-green-200', 'border-blue-300', 'border-red-200', 'cursor-not-allowed');
                        if (booking) {
                            if (booking.user_id == userId) {
                                cell.classList.add('bg-blue-100', 'text-blue-700', 'border', 'border-blue-300');
                                cell.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg> My Booking`;
                                cell.onclick = null;
                                cell.classList.add('cursor-not-allowed');
                            } else {
                                cell.classList.add('bg-red-100', 'text-red-600', 'border', 'border-red-200', 'cursor-not-allowed');
                                cell.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg> Booked`;
                                cell.onclick = null;
                            }
                        } else {
                            cell.classList.add('bg-green-50', 'hover:bg-green-200', 'text-green-800', 'border', 'border-green-200');
                            cell.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Book`;
                            cell.onclick = function(e) {
                                e.preventDefault();
                                showBookingModal(courtId, slot);
                            };
                        }
                    });
                });
                addMyBookingBtnListeners();
            });
    }
    function showBookingModal(courtId, slot) {
        const courtName = document.querySelector(`th[data-court-id='${courtId}']`)?.innerText || '';
        const date = document.getElementById('date-input').value;
        const startTime = slot;
        const endTime = (parseInt(startTime.split(':')[0]) + 1).toString().padStart(2, '0') + ':00';
        const price = 20;
        const modal = document.getElementById('booking-modal');
        const modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = `
            <div class="mb-4">
                <img src="/images/default-badminton-court.jpg" class="w-full h-32 object-cover rounded-lg mb-4" alt="Court">
                <h2 class="text-2xl font-bold mb-2 text-blue-700 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>Book ${courtName}</h2>
                <div class="mb-2">Date: <span class="font-semibold">${date}</span></div>
                <div class="mb-2">Time: <span class="font-semibold">${startTime} - ${endTime}</span></div>
                <div class="mb-2">Duration: 1 hour</div>
                <div class="mb-2">Price: <span class="font-semibold text-blue-700">RM ${price}</span></div>
            </div>
            <form method="POST" action="{{ route('bookings.store') }}">
                @csrf
                <input type="hidden" name="court_id" value="${courtId}">
                <input type="hidden" name="date" value="${date}">
                <input type="hidden" name="start_time" value="${startTime}">
                <input type="hidden" name="end_time" value="${endTime}">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition mt-2 text-lg shadow-lg">Book Now</button>
            </form>
        `;
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('opacity-100'), 10);
    }
    document.getElementById('date-input').addEventListener('change', updateGrid);
    window.addEventListener('DOMContentLoaded', updateGrid);
    // Add event listeners for My Booking buttons
    function addMyBookingBtnListeners() {
        document.querySelectorAll('.my-booking-btn').forEach(btn => {
            btn.onclick = function(e) {
                e.preventDefault();
                const bookingId = this.getAttribute('data-booking-id');
                fetch(`/booking-details/${bookingId}`)
                    .then(r => r.json())
                    .then(booking => {
                        let html = `
                            <h2 class="text-2xl font-bold mb-2 text-blue-700">Booking Details</h2>
                            <div class="mb-2"><b>Court:</b> ${booking.court.name}</div>
                            <div class="mb-2"><b>Date:</b> ${booking.date}</div>
                            <div class="mb-2"><b>Time:</b> ${booking.start_time} - ${booking.end_time}</div>
                            <div class="mb-2"><b>Total Price:</b> RM ${booking.total_price}</div>
                        `;
                        if (booking.payment && booking.payment.status === 'pending') {
                            html += `<a href="/payments/${booking.payment.id}/pay" class="block w-full bg-green-600 text-white py-2 rounded-lg font-bold text-center mt-4">Proceed to Payment</a>`;
                        }
                        html += `<form method="POST" action="/bookings/${booking.id}" class="mt-2">@csrf @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg font-bold mt-2">Cancel Booking</button>
                        </form>`;
                        document.getElementById('details-modal-content').innerHTML = html;
                        document.getElementById('booking-details-modal').classList.remove('hidden');
                    });
            };
        });
    }
    document.getElementById('close-details-modal').onclick = function() {
        document.getElementById('booking-details-modal').classList.add('hidden');
    };
    // --- My Bookings Modal ---
    document.getElementById('open-my-bookings').onclick = function(e) {
        e.preventDefault();
        fetch('/user-bookings')
            .then(r => r.json())
            .then(bookings => {
                let html = '';
                if (bookings.length === 0) {
                    html = '<div class="text-gray-500 text-center py-8">You have no bookings yet.</div>';
                } else {
                    html = `<table class='min-w-full text-base'><thead><tr><th class='py-2 px-3 text-left'>Court</th><th class='py-2 px-3 text-left'>Date</th><th class='py-2 px-3 text-left'>Time</th><th class='py-2 px-3 text-left'>Status</th><th></th></tr></thead><tbody>`;
                    bookings.forEach(b => {
                        let status = b.payment && b.payment.status === 'pending' ? 'Pending Payment' : 'Booked';
                        html += `<tr class='hover:bg-blue-50 cursor-pointer my-booking-row' data-booking-id='${b.id}'>
                            <td class='py-2 px-3'>${b.court.name}</td>
                            <td class='py-2 px-3'>${b.date}</td>
                            <td class='py-2 px-3'>${b.start_time} - ${b.end_time}</td>
                            <td class='py-2 px-3'>${status}</td>
                            <td class='py-2 px-3 text-right'><button class='text-blue-600 underline details-btn' data-booking-id='${b.id}'>Details</button></td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
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
                                    <h2 class=\"text-2xl font-bold mb-2 text-blue-700\">Booking Details</h2>
                                    <div class=\"mb-2\"><b>Court:</b> ${booking.court.name}</div>
                                    <div class=\"mb-2\"><b>Date:</b> ${booking.date}</div>
                                    <div class=\"mb-2\"><b>Time:</b> ${booking.start_time} - ${booking.end_time}</div>
                                    <div class=\"mb-2\"><b>Total Price:</b> RM ${booking.total_price}</div>
                                `;
                                if (booking.payment && booking.payment.status === 'pending') {
                                    html += `<a href=\"/payments/${booking.payment.id}/pay\" class=\"block w-full bg-green-600 text-white py-2 rounded-lg font-bold text-center mt-4\">Proceed to Payment</a>`;
                                }
                                html += `<form method=\"POST\" action=\"/bookings/${booking.id}\" class=\"mt-2\">@csrf @method('DELETE')
                                    <button type=\"submit\" class=\"w-full bg-red-600 text-white py-2 rounded-lg font-bold mt-2\">Cancel Booking</button>
                                </form>`;
                                document.getElementById('details-modal-content').innerHTML = html;
                                document.getElementById('booking-details-modal').classList.remove('hidden');
                            });
                    };
                });
            });
    };
    document.getElementById('close-my-bookings-modal').onclick = function() {
        document.getElementById('my-bookings-modal').classList.add('hidden');
    };
    // Remove range selection logic, restore single-slot booking logic
    document.querySelectorAll('.select-slot-btn').forEach(btn => {
        btn.onclick = function(e) {
            e.preventDefault();
            const courtId = this.getAttribute('data-court');
            const slot = this.getAttribute('data-time');
            const courtName = document.querySelector(`th[data-court-id='${courtId}']`)?.innerText || '';
            const date = document.getElementById('date-input').value;
            const startTime = slot;
            const endTime = (parseInt(startTime.split(':')[0]) + 1).toString().padStart(2, '0') + ':00';
            const price = 20;
            const modal = document.getElementById('booking-modal');
            const modalContent = document.getElementById('modal-content');
            modalContent.innerHTML = `
                <div class="mb-4">
                    <img src="/images/default-badminton-court.jpg" class="w-full h-32 object-cover rounded-lg mb-4" alt="Court">
                    <h2 class="text-2xl font-bold mb-2 text-blue-700 flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>Book ${courtName}</h2>
                    <div class="mb-2">Date: <span class="font-semibold">${date}</span></div>
                    <div class="mb-2">Time: <span class="font-semibold">${startTime} - ${endTime}</span></div>
                    <div class="mb-2">Duration: 1 hour</div>
                    <div class="mb-2">Price: <span class="font-semibold text-blue-700">RM ${price}</span></div>
                </div>
                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="court_id" value="${courtId}">
                    <input type="hidden" name="date" value="${date}">
                    <input type="hidden" name="start_time" value="${startTime}">
                    <input type="hidden" name="end_time" value="${endTime}">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition mt-2 text-lg shadow-lg">Book Now</button>
                </form>
            `;
            modal.classList.remove('hidden');
            setTimeout(() => modal.classList.add('opacity-100'), 10);
        };
    });
</script>
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.3s cubic-bezier(.4,0,.2,1) both; }
</style>
@endsection 