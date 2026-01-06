@extends('layouts.app')

@section('content')
@php 
    use Illuminate\Support\Facades\Storage;
    $user = Auth::user(); 
@endphp

@if($user->isOwner())
    <div class="mb-12">
        <!-- Owner Dashboard Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-blue-100 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    @if($user->profile_picture)
                        <img src="{{ Storage::url($user->profile_picture) }}" 
                             alt="{{ $user->name }}" 
                             class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl object-cover shadow-lg border-2 border-white">
                    @else
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-white text-lg sm:text-2xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Owner Dashboard</h1>
                        <p class="text-sm sm:text-base text-gray-600">Welcome back, {{ $user->name }}! Manage your courts and monitor business performance.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('courts.index') }}" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-xs sm:text-sm font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/>
                        </svg>
                        <span class="hidden sm:inline">Manage Courts</span>
                        <span class="sm:hidden">Courts</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Owner Analytics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-10">
            <div class="bg-gradient-to-br from-blue-100 to-blue-300 rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8 flex flex-col items-center border-t-4 border-blue-200 animate-fade-in">
                <div class="bg-blue-500 text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z"/></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-blue-800">{{ $user->courts->count() }}</div>
                <div class="text-gray-700 mt-1 font-medium text-sm sm:text-base">Courts Owned</div>
            </div>
            <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8 flex flex-col items-center border-t-4 border-green-200 animate-fade-in">
                <div class="bg-green-500 text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-green-800">{{ $user->courts->flatMap->bookings->count() }}</div>
                <div class="text-gray-700 mt-1 font-medium text-sm sm:text-base">Total Bookings</div>
            </div>
            <div class="bg-gradient-to-br from-yellow-100 to-yellow-300 rounded-2xl shadow-lg p-4 sm:p-6 lg:p-8 flex flex-col items-center border-t-4 border-yellow-200 animate-fade-in sm:col-span-2 lg:col-span-1">
                <div class="bg-yellow-500 text-white rounded-full p-3 sm:p-4 mb-2 sm:mb-3">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7"/></svg>
                </div>
                <div class="text-2xl sm:text-3xl font-bold text-yellow-800">RM {{ number_format($user->courts->flatMap->bookings->sum('total_price'), 2) }}</div>
                <div class="text-gray-700 mt-1 font-medium text-sm sm:text-base">Total Revenue</div>
            </div>
        </div>
        <!-- Recent Bookings Table -->
        <div class="mb-6 sm:mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 border-t-4 border-blue-100">
                <h2 class="text-lg sm:text-xl font-bold mb-4 flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                    Recent Bookings
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-gray-600 text-xs sm:text-sm">
                                <th class="py-2">Court</th>
                                <th class="hidden sm:table-cell">Date</th>
                                <th class="hidden sm:table-cell">Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    <tbody>
                    @foreach($allBookings->sortByDesc('date')->take(5) as $booking)
                        <tr class="bg-blue-50 hover:bg-blue-100 transition rounded-xl">
                            <td class="py-2 font-semibold text-sm sm:text-base">
                                <div class="sm:hidden">
                                    <div>{{ $booking->court->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-600 mt-1">{{ $booking->date }} {{ $booking->start_time }}-{{ $booking->end_time }}</div>
                                </div>
                                <div class="hidden sm:block">{{ $booking->court->name ?? 'N/A' }}</div>
                            </td>
                            <td class="hidden sm:table-cell text-sm">{{ $booking->date }}</td>
                            <td class="hidden sm:table-cell text-sm">{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                            <td>
                                <span class="inline-block px-2 sm:px-3 py-1 rounded-full text-xs font-bold
                                    @if($booking->status === 'paid') bg-green-200 text-green-800
                                    @elseif($booking->status === 'confirmed') bg-blue-200 text-blue-800
                                    @elseif($booking->status === 'completed') bg-gray-200 text-gray-800
                                    @elseif($booking->status === 'pending') bg-yellow-200 text-yellow-800
                                    @else bg-red-200 text-red-800 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
@elseif($user->isStaff())
    <div class="mb-12">
        <!-- Professional Staff Dashboard Header -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-blue-100 p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 sm:gap-4">
                    @if($user->profile_picture)
                        <img src="{{ Storage::url($user->profile_picture) }}" 
                             alt="{{ $user->name }}" 
                             class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl object-cover shadow-lg border-2 border-white">
                    @else
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-white text-lg sm:text-2xl font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Staff Dashboard</h1>
                        <p class="text-sm sm:text-base text-gray-600">Welcome back, {{ $user->name }}! Manage bookings and monitor court operations.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('staff.bookings') }}" class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent text-xs sm:text-sm font-medium rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="hidden sm:inline">View All Bookings</span>
                        <span class="sm:hidden">Bookings</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Analytics Cards -->
        @php
            $allBookings = \App\Models\Booking::with(['court', 'user', 'payment'])->get();
            $todayBookings = $allBookings->where('date', now()->toDateString());
            $pendingPayments = $allBookings->where('payment.status', 'pending');
            $totalRevenue = $allBookings->where('payment.status', 'paid')->sum('payment.amount');
        @endphp
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Total Bookings Card -->
            <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 border-t-4 border-blue-200 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Bookings</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $allBookings->count() }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">All time</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Today's Bookings Card -->
            <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 border-t-4 border-green-200 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Today's Bookings</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $todayBookings->count() }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ now()->format('M d, Y') }}</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Payments Card -->
            <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 border-t-4 border-yellow-200 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Pending Payments</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $pendingPayments->count() }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Requires attention</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="bg-white rounded-2xl shadow-lg p-4 sm:p-6 border-t-4 border-purple-200 hover:shadow-xl transition-shadow sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900">RM {{ number_format($totalRevenue, 2) }}</p>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">From paid bookings</p>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-t-4 border-indigo-200">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Quick Actions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('staff.bookings') }}" class="flex items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Manage Bookings</p>
                        <p class="text-sm text-gray-600">View and manage all bookings</p>
                    </div>
                </a>
                
                <a href="{{ route('payments.index') }}" class="flex items-center p-4 bg-green-50 rounded-xl hover:bg-green-100 transition-colors">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Payment Management</p>
                        <p class="text-sm text-gray-600">Process and track payments</p>
                    </div>
                </a>
                
                <a href="{{ route('courts.index') }}" class="flex items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Court Management</p>
                        <p class="text-sm text-gray-600">Manage court availability</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Bookings Table -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-green-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/>
                    </svg>
                    Recent Bookings
                </h2>
                <a href="{{ route('staff.bookings') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                    View All →
                </a>
            </div>
            
            @if($allBookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Court</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($allBookings->sortByDesc('date')->take(5) as $booking)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-green-500 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z" />
                                                </svg>
                                            </div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $booking->court->name ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $booking->date }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->start_time }} - {{ $booking->end_time }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'completed') bg-gray-100 text-gray-800
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($booking->payment)
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                                    @if($booking->payment->status === 'paid') bg-green-100 text-green-800
                                                    @elseif($booking->payment->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($booking->payment->status) }}
                                                </span>
                                                <div class="text-sm font-semibold text-gray-900">
                                                    RM {{ number_format($booking->payment->amount, 2) }}
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">No payment</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Bookings Yet</h3>
                    <p class="text-gray-500">Bookings will appear here once customers start making reservations.</p>
                </div>
            @endif
        </div>

        <!-- Recent Activity Feed -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-orange-200 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Recent Activity
            </h2>
            
            <div class="space-y-4">
                @php
                    $recentBookings = $allBookings->sortByDesc('created_at')->take(5);
                @endphp
                
                @forelse($recentBookings as $booking)
                    <div class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-4 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        New booking by {{ $booking->user->name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $booking->court->name }} • {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }} at {{ $booking->start_time }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $booking->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Recent Activity</h3>
                        <p class="text-gray-500">Activity will appear here as bookings are made.</p>
                    </div>
                @endforelse
            </div>
            
            @if($recentBookings->count() > 0)
                <div class="mt-6 text-center">
                    <a href="{{ route('staff.bookings') }}" class="inline-flex items-center px-4 py-2 bg-orange-100 text-orange-800 rounded-lg hover:bg-orange-200 transition-colors text-sm font-medium">
                        View All Activity
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
@else
    <div class="mb-12">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-2xl shadow-lg p-8 flex items-center mb-10 border-t-4 border-green-200 animate-fade-in" data-tutorial="welcome-card">
            <div class="flex-shrink-0 mr-6">
                @if($user->profile_picture)
                    <img src="{{ Storage::url($user->profile_picture) }}" 
                         alt="{{ $user->name }}" 
                         class="w-20 h-20 rounded-full object-cover shadow-lg border-4 border-white">
                @else
                    <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome, {{ $user->name }}!</h1>
                <p class="text-gray-600 mb-2">Ready to play? Book your next court or shop for new gear below.</p>
                <div class="flex flex-wrap gap-3 mt-2">
                    <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold shadow hover:bg-blue-700 transition" data-tutorial="book-court-btn">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                        Book a Court
                    </a>
                    <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl font-semibold shadow hover:bg-green-700 transition" data-tutorial="my-bookings-btn">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6"/></svg>
                        My Bookings
                    </a>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-xl font-semibold shadow hover:bg-yellow-600 transition" data-tutorial="shop-products-btn">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"/></svg>
                        Shop Products
                    </a>
                </div>
            </div>
        </div>
        <!-- Upcoming Bookings -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-t-4 border-blue-100" data-tutorial="upcoming-bookings">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                Your Upcoming Bookings
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead><tr class="text-gray-600 text-sm"><th class="py-2">Court</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($allBookings->where('date', '>=', now()->toDateString())->sortBy('date')->take(5) as $booking)
                        <tr class="bg-blue-50 hover:bg-blue-100 transition rounded-xl">
                            <td class="py-2 font-semibold">{{ $booking->court->name ?? 'N/A' }}</td>
                        <td>{{ $booking->date }}</td>
                        <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                            <td>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                                    @if($booking->status === 'paid') bg-green-200 text-green-800
                                    @elseif($booking->status === 'confirmed') bg-blue-200 text-blue-800
                                    @elseif($booking->status === 'completed') bg-gray-200 text-gray-800
                                    @elseif($booking->status === 'pending') bg-yellow-200 text-yellow-800
                                    @else bg-red-200 text-red-800 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        </div>
        <!-- Shop Badminton Gear -->
        <h2 class="text-xl font-bold mb-4 flex items-center" data-tutorial="shop-section">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"/></svg>
            Shop Badminton Gear
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            @foreach(\App\Models\Product::take(3)->get() as $product)
                <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-green-200 hover:shadow-xl transition" data-tutorial="product-card">
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : '/images/default-badminton-court.jpg' }}" class="h-24 w-24 object-cover rounded-xl border border-green-100 mb-4" alt="Product">
                    <div class="text-lg font-bold text-green-700 mb-2">{{ $product->name }}</div>
                    <div class="text-gray-500 mb-2">RM {{ number_format($product->price, 2) }}</div>
                    <form action="{{ route('cart.add') }}" method="POST" class="flex flex-col gap-2 mt-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center gap-2">
                            <label for="qty-{{ $product->id }}" class="text-sm">Qty:</label>
                            <input id="qty-{{ $product->id }}" name="quantity" type="number" min="1" value="1" class="w-16 border rounded px-2 py-1">
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 font-semibold">Add to Cart</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(isset($showTutorial) && $showTutorial)
    @push('scripts')
    <script>
    (function() {
        'use strict';
        
        // Wait for page to fully load
        function initTutorial() {
            // Check if introJs is available
            if (typeof introJs === 'undefined') {
                console.error('Intro.js library not loaded');
                return;
            }
            
            // Helper function to check if element exists and is visible
            function elementExists(selector) {
                const element = document.querySelector(selector);
                if (!element) return false;
                
                const rect = element.getBoundingClientRect();
                const style = window.getComputedStyle(element);
                
                return (
                    rect.width > 0 &&
                    rect.height > 0 &&
                    style.display !== 'none' &&
                    style.visibility !== 'hidden' &&
                    style.opacity !== '0'
                );
            }
            
            // Helper function to verify element is still valid
            function verifyElement(element) {
                if (!element) return false;
                
                try {
                    const rect = element.getBoundingClientRect();
                    const style = window.getComputedStyle(element);
                    
                    return (
                        rect.width > 0 &&
                        rect.height > 0 &&
                        style.display !== 'none' &&
                        style.visibility !== 'hidden' &&
                        style.opacity !== '0' &&
                        element.offsetParent !== null
                    );
                } catch (e) {
                    return false;
                }
            }
            
            // Define comprehensive tutorial steps - starting with navigation
            const allSteps = [
                {
                    element: '[data-tutorial="nav-dashboard"]',
                    intro: '<div style="text-align: center;"><h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 600; color: #1f2937;">🎯 Navigation Bar</h3><p style="margin: 0; color: #6b7280; line-height: 1.6;">This is your main navigation bar at the top. It gives you quick access to all features. Let\'s learn about each menu item!</p></div>',
                    position: 'bottom',
                    tooltipClass: 'introjs-tooltip-custom'
                },
                {
                    element: '[data-tutorial="nav-dashboard"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📊 Dashboard</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click here to return to your dashboard anytime. The dashboard shows your overview, upcoming bookings, and featured products.</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="nav-courts"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🏟️ Courts</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click "Courts" to view all available courts and make bookings. You\'ll see a calendar view with real-time availability for each court.</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="nav-bookings"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📅 Bookings</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">View all your reservations here. You can see upcoming bookings, past bookings, cancel reservations, and check booking status.</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="nav-shop"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🛒 Shop Menu</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Hover over "Shop" to see a dropdown menu. You can browse Products or view your Orders. This is where you\'ll shop for badminton gear!</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="nav-payments"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">💳 Payments</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">View your payment history and transaction details. All your payments for bookings and purchases are tracked here.</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="welcome-card"]',
                    intro: '<div style="text-align: center;"><h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 600; color: #1f2937;">Welcome to SmashZone! 👋</h3><p style="margin: 0; color: #6b7280; line-height: 1.6;">This is your dashboard - your home base! From here you can quickly access all features. Let\'s explore what you can do!</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="book-court-btn"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📅 Book a Court</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Click this button to start booking a court. You\'ll be taken to the booking page where you can select a date, time slot, and court. The system shows real-time availability!</p></div>',
                    position: 'top'
                },
                {
                    element: '[data-tutorial="my-bookings-btn"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📋 My Bookings</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">View all your bookings in one place. You can see upcoming bookings, past bookings, cancel reservations, and check the status of each booking.</p></div>',
                    position: 'top'
                },
                {
                    element: '[data-tutorial="shop-products-btn"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🛒 Shop Products</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Browse our full catalog of badminton equipment! You\'ll see all products with prices, descriptions, and can add items directly to your cart.</p></div>',
                    position: 'top'
                },
                {
                    element: '[data-tutorial="upcoming-bookings"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">📊 Upcoming Bookings</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">This section shows your upcoming court bookings at a glance. You can see the date, time, court name, and payment status. Click "My Bookings" to manage them.</p></div>',
                    position: 'top'
                },
                {
                    element: '[data-tutorial="shop-section"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🏸 Shop Badminton Gear</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Browse featured products right from your dashboard. We offer premium badminton equipment including rackets, shoes, clothing, and accessories.</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="product-card"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">🛍️ How to Shop</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Each product card shows the item name, image, and price. Select the quantity you want, then click "Add to Cart". To see more products, click "Shop Products" button above or use the Shop menu in navigation.</p></div>',
                    position: 'top'
                }
            ];
            
            // Filter steps to only include elements that exist
            const validSteps = [];
            
            for (let i = 0; i < allSteps.length; i++) {
                const step = allSteps[i];
                
                // For product-card, check if at least one exists
                if (step.element === '[data-tutorial="product-card"]') {
                    const productCards = document.querySelectorAll('[data-tutorial="product-card"]');
                    if (productCards.length > 0) {
                        // Find the first visible product card
                        let firstVisibleCard = null;
                        for (let j = 0; j < productCards.length; j++) {
                            if (verifyElement(productCards[j])) {
                                firstVisibleCard = productCards[j];
                                break;
                            }
                        }
                        
                        if (firstVisibleCard) {
                            // Add a unique ID to the first card for targeting
                            if (!firstVisibleCard.id) {
                                firstVisibleCard.id = 'tutorial-product-card-first';
                            }
                            validSteps.push({
                                ...step,
                                element: '#tutorial-product-card-first'
                            });
                        }
                    }
                } else if (elementExists(step.element)) {
                    validSteps.push(step);
                }
            }
            
            // If no valid steps, don't start tutorial
            if (validSteps.length === 0) {
                console.warn('No valid tutorial elements found');
                return;
            }
            
            // Initialize intro.js
            const intro = introJs();
            
            intro.setOptions({
                steps: validSteps,
                showProgress: true,
                showBullets: true,
                exitOnOverlayClick: true, // Allow clicking outside to exit
                exitOnEsc: true,
                keyboardNavigation: true,
                disableInteraction: false, // Allow interactions with highlighted elements
                scrollToElement: true,
                scrollPadding: 20,
                nextLabel: 'Next →',
                prevLabel: '← Previous',
                skipLabel: 'Skip Tutorial',
                doneLabel: 'Got it! 🎉',
                tooltipClass: 'customTooltip',
                highlightClass: 'customHighlight',
                buttonClass: 'introjs-button',
                hidePrev: false,
                hideNext: false,
                tooltipPosition: 'auto' // Let intro.js decide the best position
            });
            
            // Ensure tooltip is visible after each step
            intro.onchange(function(targetElement) {
                setTimeout(function() {
                    const tooltip = document.querySelector('.introjs-tooltip');
                    if (tooltip) {
                        tooltip.style.display = 'block';
                        tooltip.style.visibility = 'visible';
                        tooltip.style.opacity = '1';
                        tooltip.style.zIndex = '999999';
                        
                        // Also ensure content is visible
                        const content = tooltip.querySelector('.introjs-tooltipcontent');
                        if (content) {
                            content.style.display = 'block';
                            content.style.visibility = 'visible';
                            content.style.opacity = '1';
                        }
                        
                        console.log('Tooltip should be visible now', tooltip);
                    } else {
                        console.warn('Tooltip not found!');
                    }
                }, 100);
            });
            
            // Debug: Log when tutorial starts
            intro.onstart(function() {
                console.log('Tutorial started with', validSteps.length, 'steps');
            });
            
            // Ensure overlay is clickable to exit
            intro.onbeforechange(function(targetElement) {
                const overlay = document.querySelector('.introjs-overlay');
                if (overlay) {
                    overlay.style.pointerEvents = 'auto';
                    overlay.style.cursor = 'pointer';
                }
                
                // Verify element exists before proceeding
                if (targetElement) {
                    const rect = targetElement.getBoundingClientRect();
                    const style = window.getComputedStyle(targetElement);
                    
                    // If element is not visible, try to scroll it into view
                    if (rect.width === 0 || rect.height === 0 || style.display === 'none' || style.visibility === 'hidden') {
                        setTimeout(function() {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center',
                                inline: 'nearest'
                            });
                        }, 100);
                    }
                }
            });
            
            // Handle step change - ensure element is visible
            intro.onchange(function(targetElement) {
                try {
                    if (targetElement) {
                        // Wait a bit for intro.js to position the tooltip
                        setTimeout(function() {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center',
                                inline: 'nearest'
                            });
                        }, 200);
                    }
                } catch (error) {
                    console.warn('Error in tutorial step change:', error);
                    // Don't exit tutorial on error, just log it
                }
            });
            
            // Handle errors during step navigation
            intro.onbeforeexit(function() {
                // This is called before exit, we can prevent it if needed
                return true; // Allow exit
            });
            
            // Mark tutorial as completed
            function markTutorialComplete() {
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
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log('✅ Tutorial completed successfully!');
                    }
                })
                .catch(error => {
                    console.error('❌ Error marking tutorial as completed:', error);
                });
            }
            
            // On tutorial completion
            intro.oncomplete(function() {
                markTutorialComplete();
            });
            
            // On tutorial exit (skip)
            intro.onexit(function() {
                markTutorialComplete();
            });
            
            // Start tutorial after a short delay to ensure page is ready
            setTimeout(function() {
                try {
                    // Double-check all steps have valid elements before starting
                    const finalValidSteps = [];
                    for (let i = 0; i < validSteps.length; i++) {
                        const step = validSteps[i];
                        let element;
                        
                        try {
                            element = document.querySelector(step.element);
                        } catch (e) {
                            console.warn('Invalid selector:', step.element, e);
                            continue;
                        }
                        
                        if (element && verifyElement(element)) {
                            finalValidSteps.push(step);
                        } else {
                            console.warn('Skipping step - element not valid:', step.element);
                        }
                    }
                    
                    if (finalValidSteps.length === 0) {
                        console.warn('No valid tutorial elements found after final check');
                        return;
                    }
                    
                    // Update steps if some were filtered out
                    if (finalValidSteps.length !== validSteps.length) {
                        console.log('Filtered out invalid steps:', validSteps.length - finalValidSteps.length);
                        intro.setOptions({ steps: finalValidSteps });
                    }
                    
                    console.log('Starting tutorial with', finalValidSteps.length, 'valid steps');
                    
                    intro.start();
                    
                    // Add error handler for step navigation
                    window.addEventListener('error', function(e) {
                        if (e.message && e.message.includes('introjs')) {
                            console.error('Tutorial error detected:', e);
                            // Don't exit, just log
                        }
                    }, true);
                    
                    // Safety mechanism: If tutorial overlay is stuck, allow emergency exit
                    setTimeout(function() {
                        const overlay = document.querySelector('.introjs-overlay');
                        if (overlay && overlay.style.display !== 'none') {
                            // Add emergency exit message
                            overlay.setAttribute('title', 'Double-click to exit tutorial');
                            overlay.style.cursor = 'pointer';
                            
                            // Double-click to force exit
                            overlay.addEventListener('dblclick', function() {
                                intro.exit();
                                markTutorialComplete();
                            }, { once: true });
                        }
                    }, 2000);
                } catch (error) {
                    console.error('Error starting tutorial:', error);
                    // If tutorial fails to start, mark as completed to prevent blocking
                    markTutorialComplete();
                }
            }, 1000); // Increased delay to ensure page is fully loaded
        }
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTutorial);
        } else {
            // DOM is already ready
            setTimeout(initTutorial, 100);
        }
    })();
    </script>
    <style>
    /* Professional Tutorial Styling */
    .customTooltip {
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        max-width: 400px !important;
        padding: 0 !important;
        background: white !important;
    }
    
    .introjs-tooltip-header {
        padding: 20px 20px 12px 20px !important;
        border-bottom: 1px solid #e5e7eb !important;
    }
    
    .introjs-tooltipcontent {
        padding: 16px 20px !important;
        font-size: 14px !important;
        line-height: 1.6 !important;
        color: #374151 !important;
    }
    
    .introjs-tooltipbuttons {
        padding: 12px 20px 20px 20px !important;
        border-top: 1px solid #e5e7eb !important;
        text-align: right !important;
    }
    
    .customHighlight {
        border-radius: 12px !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3) !important;
    }
    
    .introjs-overlay {
        background: rgba(0, 0, 0, 0.7) !important;
        backdrop-filter: blur(2px) !important;
        cursor: pointer !important;
    }
    
    /* Ensure highlighted elements are clickable */
    .introjs-helperLayer + * {
        pointer-events: auto !important;
    }
    
    /* Make sure tutorial buttons are always clickable */
    .introjs-tooltip,
    .introjs-tooltip * {
        pointer-events: auto !important;
    }
    
    .introjs-button {
        border-radius: 8px !important;
        padding: 10px 20px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
        border: none !important;
        cursor: pointer !important;
    }
    
    .introjs-button.introjs-nextbutton {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
        border: none !important;
    }
    
    .introjs-button.introjs-nextbutton:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;
    }
    
    .introjs-button.introjs-prevbutton {
        background: #f3f4f6 !important;
        color: #374151 !important;
        margin-right: 8px !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .introjs-button.introjs-prevbutton:hover {
        background: #e5e7eb !important;
        border-color: #d1d5db !important;
    }
    
    .introjs-button.introjs-donebutton {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
        border: none !important;
    }
    
    .introjs-button.introjs-donebutton:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4) !important;
    }
    
    .introjs-skipbutton {
        color: #6b7280 !important;
        font-size: 14px !important;
        padding: 8px 12px !important;
        border-radius: 6px !important;
        transition: all 0.2s ease !important;
    }
    
    .introjs-skipbutton:hover {
        color: #374151 !important;
        background: #f3f4f6 !important;
    }
    
    .introjs-progress {
        background: #e5e7eb !important;
        height: 4px !important;
        border-radius: 2px !important;
    }
    
    .introjs-progressbar {
        background: linear-gradient(90deg, #3b82f6 0%, #10b981 100%) !important;
        height: 4px !important;
        border-radius: 2px !important;
        transition: width 0.3s ease !important;
    }
    
    .introjs-bullets {
        text-align: center !important;
        padding: 12px 0 0 0 !important;
    }
    
    .introjs-bullets ul li a {
        background: #d1d5db !important;
        width: 8px !important;
        height: 8px !important;
        border-radius: 50% !important;
        transition: all 0.3s ease !important;
    }
    
    .introjs-bullets ul li a.active {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%) !important;
        width: 24px !important;
        border-radius: 4px !important;
    }
    
    /* Ensure highlighted elements are visible with brand colors */
    .introjs-helperLayer {
        border-radius: 12px !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2), 0 0 0 8px rgba(16, 185, 129, 0.1) !important;
    }
    
    /* Custom tooltip header with brand gradient */
    .customTooltip .introjs-tooltip-header {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%) !important;
        color: white !important;
        padding: 16px 20px !important;
        border-radius: 16px 16px 0 0 !important;
    }
    
    .customTooltip .introjs-tooltipcontent {
        background: white !important;
    }
    
    /* Smooth transitions */
    .introjs-tooltip {
        z-index: 999999 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: absolute !important;
        animation: fadeIn 0.3s ease-in-out !important;
        max-width: 400px !important;
        min-width: 300px !important;
    }
    
    /* Ensure tooltip content is visible */
    .introjs-tooltip .introjs-tooltipcontent {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        color: #374151 !important;
    }
    
    /* Ensure tooltip header is visible */
    .introjs-tooltip .introjs-tooltipheader {
        display: block !important;
        visibility: visible !important;
    }
    
    /* Ensure all tooltip inner elements are visible */
    .introjs-tooltip * {
        visibility: visible !important;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
    @endpush
@endif

<!-- Live Badminton News Section -->
<div class="max-w-7xl mx-auto px-4 mb-16">
    <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-orange-200">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                Live Badminton News & Updates
            </h2>
            @if($newsStatus['status'] === 'active')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    Live
                </span>
            @elseif($newsStatus['status'] === 'not_configured')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                    Demo Mode
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                    Offline
                </span>
            @endif
        </div>
        
        <!-- Live News Articles -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($badmintonNews as $index => $article)
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100 hover:shadow-lg transition-all cursor-pointer" 
                     onclick="window.open('{{ $article['url'] }}', '_blank')">
                    <div class="flex items-start mb-4">
                        @if($article['image'] && $article['image'] !== asset('images/badminton-news-default.jpg'))
                            <img src="{{ $article['image'] }}" 
                                 alt="{{ $article['title'] }}" 
                                 class="w-16 h-16 object-cover rounded-lg mr-4 flex-shrink-0">
                        @else
                            <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="font-bold text-blue-900 text-sm leading-tight mb-2">{{ $article['title'] }}</h3>
                            <p class="text-xs text-blue-600">{{ $article['source'] }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 mb-3 line-clamp-3">{{ $article['description'] }}</p>
                    <div class="flex items-center justify-between">
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            {{ $article['published_at'] }}
                        </span>
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </div>
                </div>
            @empty
                <!-- Fallback content when no news available -->
                <div class="col-span-full text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No News Available</h3>
                    <p class="text-gray-500">Check back later for the latest badminton news and updates.</p>
                </div>
            @endforelse
        </div>

        <!-- Featured News Article -->
        @if(!empty($badmintonNews))
            @php $featuredArticle = $badmintonNews[0]; @endphp
            <div class="mt-8 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-6 border border-orange-200 cursor-pointer" 
                 onclick="window.open('{{ $featuredArticle['url'] }}', '_blank')">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-4">
                        @if($featuredArticle['image'] && $featuredArticle['image'] !== asset('images/badminton-news-default.jpg'))
                            <img src="{{ $featuredArticle['image'] }}" 
                                 alt="{{ $featuredArticle['title'] }}" 
                                 class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <h3 class="text-lg font-bold text-orange-900 mr-3">{{ $featuredArticle['title'] }}</h3>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Featured
                            </span>
                        </div>
                        <p class="text-gray-700 mb-3">{{ $featuredArticle['description'] }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $featuredArticle['published_date'] }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ $featuredArticle['source'] }}
                                </span>
                            </div>
                            <div class="flex items-center text-orange-600 hover:text-orange-700 transition-colors">
                                <span class="text-sm font-semibold mr-2">Read More</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-100 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-800">15+</div>
                <div class="text-sm text-blue-600">Tournaments</div>
            </div>
            <div class="bg-green-100 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-800">500+</div>
                <div class="text-sm text-green-600">Active Players</div>
            </div>
            <div class="bg-purple-100 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-purple-800">50+</div>
                <div class="text-sm text-purple-600">Coaches</div>
            </div>
            <div class="bg-orange-100 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-orange-800">1000+</div>
                <div class="text-sm text-orange-600">Matches Played</div>
            </div>
        </div>
    </div>
</div>
@endsection
