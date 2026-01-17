@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-4 sm:py-8">
    <!-- Enhanced Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center gap-3 sm:gap-4 mb-4">
            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-green-500 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Court Bookings</h1>
                <p class="text-sm sm:text-base text-gray-600">Manage all bookings for all courts</p>
            </div>
        </div>
    
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Total Bookings</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-800">{{ $bookings->count() }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Confirmed Bookings</p>
                        <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $bookings->where('status', 'confirmed')->count() }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-4 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600">Completed Sessions</p>
                        <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $bookings->where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Bookings Table -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-3 sm:px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-green-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    All Bookings
                </h2>
                
                <!-- Booking Status Filter Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs sm:text-sm text-gray-600 mr-2">Status:</span>
                    <button onclick="filterBookings('all')" id="booking-filter-all" class="booking-filter-btn active px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium bg-blue-600 text-white">
                        All
                    </button>
                    <button onclick="filterBookings('pending')" id="booking-filter-pending" class="booking-filter-btn px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium bg-gray-200 text-gray-700 hover:bg-yellow-200 hover:text-yellow-800">
                        Not Played
                    </button>
                    <button onclick="filterBookings('confirmed')" id="booking-filter-confirmed" class="booking-filter-btn px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium bg-gray-200 text-gray-700 hover:bg-blue-200 hover:text-blue-800">
                        Confirmed
                    </button>
                    <button onclick="filterBookings('completed')" id="booking-filter-completed" class="booking-filter-btn px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium bg-gray-200 text-gray-700 hover:bg-green-200 hover:text-green-800">
                        Played
                    </button>
                    <button onclick="filterBookings('cancelled')" id="booking-filter-cancelled" class="booking-filter-btn px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm font-medium bg-gray-200 text-gray-700 hover:bg-red-200 hover:text-red-800">
                        Cancelled
                    </button>
                </div>
            </div>
        </div>
        
        @if($bookings->isEmpty())
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Bookings Found</h3>
                <p class="text-gray-500">There are no bookings to display.</p>
            </div>
        @else
            <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Court
                            </th>
                            <th class="hidden sm:table-cell px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-3 sm:px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                </tr>
            </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                @foreach($bookings as $booking)
                        <tr class="booking-row hover:bg-gray-50 transition-colors" data-booking-status="{{ $booking->status }}">
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-blue-500 to-green-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs sm:text-sm font-semibold text-gray-900">{{ $booking->court->name ?? 'N/A' }}</div>
                                        <div class="sm:hidden text-xs text-gray-500 mt-1">{{ $booking->user->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-semibold text-gray-600">
                                            {{ strtoupper(substr($booking->user->name ?? 'N/A', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <div class="text-xs sm:text-sm text-gray-900 font-medium">{{ $booking->date }}</div>
                                <div class="text-xs sm:text-sm text-gray-500">{{ $booking->start_time }} - {{ $booking->end_time }}</div>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 sm:px-3 py-1 text-xs font-semibold rounded-full
                                    @if($booking->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($booking->status === 'completed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($booking->status === 'pending')
                                        ⏳ Not Played Yet
                                    @elseif($booking->status === 'confirmed')
                                        ✅ Confirmed
                                    @elseif($booking->status === 'completed')
                                        🏸 Played
                                    @elseif($booking->status === 'cancelled')
                                        ❌ Cancelled
                                    @else
                                        {{ ucfirst($booking->status) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex flex-col sm:flex-row items-center gap-1 sm:gap-2">
                                    @php
                                        $bookingDateTime = \Carbon\Carbon::parse($booking->date . ' ' . $booking->start_time);
                                        $now = \Carbon\Carbon::now();
                                        $isLate = $now->diffInMinutes($bookingDateTime, false) > 30; // 30 minutes late
                                        $isPastBooking = $now->gt($bookingDateTime);
                                        $canCancel = $isLate && $booking->status !== 'completed' && $booking->status !== 'cancelled';
                                    @endphp
                                    
                                    <!-- Cancel Booking Action -->
                                    @if($canCancel)
                                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors" 
                                                    onclick="return confirm('Cancel this booking? Customer is late for more than 30 minutes.')">
                                                ❌ Cancel
                                            </button>
                                        </form>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="text-gray-500 text-sm bg-gray-100 px-3 py-1 rounded-lg">
                                            ❌ Cancelled
                                        </span>
                                    @elseif($booking->status === 'completed')
                                        <span class="text-gray-500 text-sm bg-gray-100 px-3 py-1 rounded-lg">
                                            ✓ Completed
                                        </span>
                                    @else
                                        <span class="text-gray-500 text-sm bg-gray-100 px-3 py-1 rounded-lg">
                                            ⏰ Active
                                        </span>
                                    @endif
                                    
                                    <!-- Mark as Played/Completed Action -->
                                    @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                        <form action="{{ route('bookings.mark-completed', $booking) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 font-medium bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg transition-colors" 
                                                    onclick="return confirm('Mark this customer as played/completed?')">
                                                ✓ Played
                                            </button>
                                        </form>
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

<script>
function filterBookings(status) {
    const rows = document.querySelectorAll('.booking-row');
    const buttons = document.querySelectorAll('.booking-filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white', 'bg-green-600', 'bg-yellow-600', 'bg-red-600');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeBtn = document.getElementById(`booking-filter-${status}`);
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
    activeBtn.classList.add('active');
    
    if (status === 'all') {
        activeBtn.classList.add('bg-blue-600', 'text-white');
    } else if (status === 'pending') {
        activeBtn.classList.add('bg-yellow-600', 'text-white');
    } else if (status === 'confirmed') {
        activeBtn.classList.add('bg-blue-600', 'text-white');
    } else if (status === 'completed') {
        activeBtn.classList.add('bg-green-600', 'text-white');
    } else if (status === 'cancelled') {
        activeBtn.classList.add('bg-red-600', 'text-white');
    }
    
    // Filter rows
    rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-booking-status') === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection 