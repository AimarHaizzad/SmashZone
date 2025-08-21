@extends('layouts.app')

@section('content')
@php $user = Auth::user(); @endphp

@if($user->isOwner())
    <div class="mb-12">
        <!-- Owner Analytics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="bg-gradient-to-br from-blue-100 to-blue-300 rounded-2xl shadow-lg p-8 flex flex-col items-center border-t-4 border-blue-400 animate-fade-in">
                <div class="bg-blue-500 text-white rounded-full p-4 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z"/></svg>
                </div>
                <div class="text-3xl font-bold text-blue-800">{{ $user->courts->count() }}</div>
                <div class="text-gray-700 mt-1 font-medium">Courts Owned</div>
            </div>
            <div class="bg-gradient-to-br from-green-100 to-green-300 rounded-2xl shadow-lg p-8 flex flex-col items-center border-t-4 border-green-400 animate-fade-in">
                <div class="bg-green-500 text-white rounded-full p-4 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                </div>
                <div class="text-3xl font-bold text-green-800">{{ $user->courts->flatMap->bookings->count() }}</div>
                <div class="text-gray-700 mt-1 font-medium">Total Bookings</div>
            </div>
            <div class="bg-gradient-to-br from-yellow-100 to-yellow-300 rounded-2xl shadow-lg p-8 flex flex-col items-center border-t-4 border-yellow-400 animate-fade-in">
                <div class="bg-yellow-500 text-white rounded-full p-4 mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7"/></svg>
                </div>
                <div class="text-3xl font-bold text-yellow-800">RM {{ number_format($user->courts->flatMap->bookings->sum('total_price'), 2) }}</div>
                <div class="text-gray-700 mt-1 font-medium">Total Revenue</div>
            </div>
        </div>
        <!-- Quick Links -->
        <div class="flex flex-wrap gap-4 mb-10">
            <a href="{{ route('courts.index') }}" class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold shadow hover:bg-blue-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 21m5.25-4l.75 4m-7.5-4h10.5a2.25 2.25 0 002.25-2.25V7.5A2.25 2.25 0 0017.25 5.25H6.75A2.25 2.25 0 004.5 7.5v7.25A2.25 2.25 0 006.75 17z"/></svg>
                Manage Courts
            </a>
            <a href="{{ route('owner.bookings') }}" class="inline-flex items-center px-5 py-3 bg-green-600 text-white rounded-xl font-semibold shadow hover:bg-green-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                View Bookings
            </a>
            <a href="{{ route('staff.index') }}" class="inline-flex items-center px-5 py-3 bg-purple-600 text-white rounded-xl font-semibold shadow hover:bg-purple-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Staff Management
            </a>
            <a href="{{ route('analytics.index') }}" class="inline-flex items-center px-5 py-3 bg-yellow-500 text-white rounded-xl font-semibold shadow hover:bg-yellow-600 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                Analytics & Reports
            </a>
        </div>
        <!-- Recent Bookings Table -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-t-4 border-blue-100">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                Recent Bookings
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-gray-600 text-sm">
                            <th class="py-2">Court</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                <tbody>
                @foreach($user->courts->flatMap->bookings->sortByDesc('date')->take(5) as $booking)
                                                <tr class="bg-blue-50 hover:bg-blue-100 transition rounded-xl">
                            <td class="py-2 font-semibold">{{ $booking->court->name }}</td>
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
    </div>
@elseif($user->isStaff())
    <div class="mb-12">
        <h1 class="text-3xl font-bold mb-6">Staff Dashboard</h1>
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-t-4 border-green-100">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                All Bookings
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead><tr class="text-gray-600 text-sm"><th class="py-2">Court</th><th>Date</th><th>Time</th><th>User</th><th>Status</th></tr></thead>
                <tbody>
                @foreach(\App\Models\Booking::orderBy('date', 'desc')->take(10)->get() as $booking)
                                                <tr class="bg-green-50 hover:bg-green-100 transition rounded-xl">
                            <td class="py-2 font-semibold">{{ $booking->court->name }}</td>
                        <td>{{ $booking->date }}</td>
                        <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                        <td>{{ $booking->user->name }}</td>
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
    </div>
@else
    <div class="mb-12">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-2xl shadow-lg p-8 flex items-center mb-10 border-t-4 border-green-200 animate-fade-in">
            <div class="flex-shrink-0 mr-6">
                <div class="w-20 h-20 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome, {{ $user->name }}!</h1>
                <p class="text-gray-600 mb-2">Ready to play? Book your next court or shop for new gear below.</p>
                <div class="flex flex-wrap gap-3 mt-2">
                    <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold shadow hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                        Book a Court
                    </a>
                    <a href="{{ route('bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-xl font-semibold shadow hover:bg-green-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6"/></svg>
                        My Bookings
                    </a>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-xl font-semibold shadow hover:bg-yellow-600 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"/></svg>
                        Shop Products
                    </a>
                </div>
            </div>
        </div>
        <!-- Upcoming Bookings -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-t-4 border-blue-100">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0V3m0 14a4 4 0 01-8 0"/></svg>
                Your Upcoming Bookings
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-2">
                    <thead><tr class="text-gray-600 text-sm"><th class="py-2">Court</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($user->bookings()->where('date', '>=', now()->toDateString())->orderBy('date')->take(5)->get() as $booking)
                        <tr class="bg-blue-50 hover:bg-blue-100 transition rounded-xl">
                            <td class="py-2 font-semibold">{{ $booking->court->name }}</td>
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
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7"/></svg>
            Shop Badminton Gear
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            @foreach(\App\Models\Product::take(3)->get() as $product)
                <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-green-200 hover:shadow-xl transition">
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

<!-- Badminton News Section -->
<div class="max-w-7xl mx-auto px-4 mb-16">
    <div class="bg-white rounded-2xl shadow-lg p-6 border-t-4 border-orange-200">
        <h2 class="text-xl font-bold mb-6 flex items-center">
            <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            Badminton News & Updates
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Latest Tournament News -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100 hover:shadow-lg transition-all">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-blue-900">Tournament Updates</h3>
                        <p class="text-sm text-blue-600">Latest Results</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="bg-white rounded-lg p-3 border-l-4 border-blue-500">
                        <h4 class="font-semibold text-gray-800 text-sm">Malaysia Open 2025</h4>
                        <p class="text-xs text-gray-600 mt-1">Registration now open for the upcoming Malaysia Open tournament in Kuala Lumpur.</p>
                        <span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Jan 15-20, 2025</span>
                    </div>
                    <div class="bg-white rounded-lg p-3 border-l-4 border-green-500">
                        <h4 class="font-semibold text-gray-800 text-sm">All England Championships</h4>
                        <p class="text-xs text-gray-600 mt-1">Top players confirmed for the prestigious All England Open Badminton Championships.</p>
                        <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Mar 12-17, 2025</span>
                    </div>
                </div>
            </div>

            <!-- Equipment & Gear News -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100 hover:shadow-lg transition-all">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-green-900">Equipment News</h3>
                        <p class="text-sm text-green-600">Latest Gear</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="bg-white rounded-lg p-3 border-l-4 border-green-500">
                        <h4 class="font-semibold text-gray-800 text-sm">New Yonex Racket Series</h4>
                        <p class="text-xs text-gray-600 mt-1">Yonex launches new Astrox series with improved aerodynamics and power.</p>
                        <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">New Release</span>
                    </div>
                    <div class="bg-white rounded-lg p-3 border-l-4 border-purple-500">
                        <h4 class="font-semibold text-gray-800 text-sm">Victor Shoes Collection</h4>
                        <p class="text-xs text-gray-600 mt-1">Victor introduces lightweight badminton shoes with enhanced grip technology.</p>
                        <span class="inline-block mt-2 px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Limited Edition</span>
                    </div>
                </div>
            </div>

            <!-- Training & Tips -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100 hover:shadow-lg transition-all">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-purple-900">Training Tips</h3>
                        <p class="text-sm text-purple-600">Improve Your Game</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="bg-white rounded-lg p-3 border-l-4 border-purple-500">
                        <h4 class="font-semibold text-gray-800 text-sm">Footwork Mastery</h4>
                        <p class="text-xs text-gray-600 mt-1">Master the basics of badminton footwork to improve your court coverage.</p>
                        <span class="inline-block mt-2 px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Beginner</span>
                    </div>
                    <div class="bg-white rounded-lg p-3 border-l-4 border-orange-500">
                        <h4 class="font-semibold text-gray-800 text-sm">Smash Technique</h4>
                        <p class="text-xs text-gray-600 mt-1">Learn the proper technique for powerful and accurate smashes.</p>
                        <span class="inline-block mt-2 px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">Advanced</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured News Article -->
        <div class="mt-8 bg-gradient-to-r from-orange-50 to-red-50 rounded-xl p-6 border border-orange-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-16 h-16 bg-orange-600 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-orange-900 mb-2">Featured: Badminton's Growing Popularity in Malaysia</h3>
                    <p class="text-gray-700 mb-3">Badminton continues to be one of Malaysia's most beloved sports, with increasing participation rates and growing interest among young players. The sport's accessibility and the success of Malaysian players on the international stage have contributed to its popularity.</p>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                January 8, 2025
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                2.5k views
                            </span>
                        </div>
                        <button class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-orange-700 transition-colors">
                            Read More
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
