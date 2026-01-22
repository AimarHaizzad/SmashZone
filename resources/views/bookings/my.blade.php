@extends('layouts.app')

@section('content')
<!-- Hero Section with Gradient Background -->
<div class="relative mb-8 sm:mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-green-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-blue-600 to-green-600 rounded-3xl p-4 sm:p-6 lg:p-8 text-center">
        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
            </svg>
        </div>
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-2">My Bookings</h1>
        <p class="text-base sm:text-lg lg:text-xl text-blue-100 font-medium mb-4">Manage your reservations • Track payments • View history</p>
        <div class="flex flex-wrap items-center justify-center gap-3 md:gap-6 text-white/90 mt-4">
            <div class="flex items-center gap-1 md:gap-2">
                <div class="w-2 h-2 md:w-3 md:h-3 bg-green-400 rounded-full"></div>
                <span class="text-xs md:text-sm font-medium">Paid</span>
            </div>
            <div class="flex items-center gap-1 md:gap-2">
                <div class="w-2 h-2 md:w-3 md:h-3 bg-yellow-400 rounded-full"></div>
                <span class="text-xs md:text-sm font-medium">Pending Payment</span>
            </div>
            <div class="flex items-center gap-1 md:gap-2">
                <div class="w-2 h-2 md:w-3 md:h-3 bg-red-400 rounded-full"></div>
                <span class="text-xs md:text-sm font-medium">Failed</span>
            </div>
            <div class="flex items-center gap-1 md:gap-2">
                <div class="w-2 h-2 md:w-3 md:h-3 bg-gray-400 rounded-full"></div>
                <span class="text-xs md:text-sm font-medium">Past</span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Date Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Filter by Date</h3>
                <p class="text-sm text-gray-600">View bookings for a specific date</p>
            </div>
            <form method="GET" action="{{ route('bookings.my') }}" class="flex items-center gap-3">
                <div class="relative">
                    <input 
                        type="date" 
                        name="date" 
                        value="{{ $selectedDate ?? '' }}" 
                        class="border-2 border-blue-200 rounded-lg px-4 py-2.5 shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-400 text-sm font-semibold bg-white w-full sm:w-auto"
                        onchange="this.form.submit()"
                    >
                </div>
                @if($selectedDate)
                    <a href="{{ route('bookings.my') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors text-sm">
                        Clear Filter
                    </a>
                @endif
            </form>
        </div>
        @if($selectedDate)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Showing bookings for: <span class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($selectedDate)->format('F d, Y') }}</span>
                    <span class="text-gray-500">({{ $bookings->count() }} {{ $bookings->count() === 1 ? 'booking' : 'bookings' }})</span>
                </p>
            </div>
        @endif
    </div>

    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Bookings -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalBookings ?? $bookings->count() }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Payment -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Pending Payment</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingPaymentsCount ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Paid -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Paid</p>
                    <p class="text-3xl font-bold text-green-600">{{ $paidPaymentsCount ?? 0 }}</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Spent -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Spent</p>
                    <p class="text-3xl font-bold text-blue-600">RM {{ number_format($totalSpent ?? 0, 2) }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking History Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-green-50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Booking History</h2>
            </div>
        </div>

        @if($bookings->isEmpty())
            <div class="p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Bookings Yet</h3>
                <p class="text-gray-600 mb-6">Ready to play? Book your first court and start enjoying your game!</p>
                <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Book Now
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">COURT</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">DATE</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">TIME</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">AMOUNT</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">STATUS</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $booking->court->name ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->date)->format('l') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A') }} - 
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->diffInHours(\Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)) }} hour(s)
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-blue-600">RM {{ number_format($booking->total_price, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $paymentStatus = $booking->payment->status ?? 'pending';
                                        $statusConfig = match($paymentStatus) {
                                            'paid' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'check', 'label' => 'Paid'],
                                            'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock', 'label' => 'Pending'],
                                            'failed' => ['class' => 'bg-red-100 text-red-800', 'icon' => 'x', 'label' => 'Failed'],
                                            default => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'info', 'label' => ucfirst($paymentStatus)]
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $statusConfig['class'] }}">
                                        @if($statusConfig['icon'] === 'check')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @elseif($statusConfig['icon'] === 'clock')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                        @elseif($statusConfig['icon'] === 'x')
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <button class="details-btn inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition-colors text-sm" data-booking-id="{{ $booking->id }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Details
                                        </button>
                                        @if($booking->payment && $booking->payment->status === 'pending')
                                            <a href="{{ route('payments.pay', $booking->payment->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg font-medium hover:bg-green-100 transition-colors text-sm">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                Pay
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Booking Details Modal -->
<div id="booking-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Booking Details</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="booking-details" class="space-y-3 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <span class="text-gray-600">Loading...</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('booking-modal');
    const closeBtn = document.getElementById('close-modal');
    const detailsBtns = document.querySelectorAll('.details-btn');
    
    closeBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });
    
    detailsBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            const detailsDiv = document.getElementById('booking-details');
            detailsDiv.innerHTML = '<div class="flex items-center gap-2"><div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div><span class="text-gray-600">Loading...</span></div>';
            modal.classList.remove('hidden');
            
            fetch(`/booking-details/${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    const startTime = new Date('1970-01-01T' + data.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
                    const endTime = new Date('1970-01-01T' + data.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
                    const date = new Date(data.date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
                    
                    detailsDiv.innerHTML = `
                        <div class="space-y-4">
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Court</p>
                                    <p class="font-semibold text-gray-900">${data.court?.name || 'N/A'}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Date</p>
                                    <p class="font-semibold text-gray-900">${date}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Time</p>
                                    <p class="font-semibold text-gray-900">${startTime} - ${endTime}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Amount</p>
                                    <p class="font-semibold text-blue-600">RM ${parseFloat(data.total_price || 0).toFixed(2)}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500">Status</p>
                                    <p class="font-semibold text-gray-900 capitalize">${data.status}</p>
                                </div>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    detailsDiv.innerHTML = '<div class="text-red-600">Error loading booking details. Please try again.</div>';
                });
        });
    });
    
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection
