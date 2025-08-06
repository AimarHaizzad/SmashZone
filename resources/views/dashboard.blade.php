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
            <a href="#" class="inline-flex items-center px-5 py-3 bg-yellow-500 text-white rounded-xl font-semibold shadow hover:bg-yellow-600 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7"/></svg>
                Reports (Coming Soon)
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
@endsection
