@extends('layouts.app')

@section('content')
<!-- Hero Banner -->
<div class="relative mb-12">
    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80" alt="Badminton Hero" class="w-full h-64 object-cover rounded-2xl shadow-lg">
    <div class="absolute inset-0 flex flex-col justify-center items-center bg-gradient-to-t from-black/60 to-transparent rounded-2xl">
        <h1 class="text-5xl font-extrabold text-white drop-shadow mb-2 tracking-widest">WELCOME TO SMASHZONE</h1>
        <p class="text-xl text-blue-100 font-semibold drop-shadow mb-4 tracking-wide">Book courts, shop gear, and play more badminton!</p>
        <a href="{{ route('bookings.index') }}" class="px-8 py-3 bg-blue-600 text-white text-lg font-bold rounded-full shadow hover:bg-blue-700 transition">Book Now</a>
    </div>
</div>

<!-- Section: Upcoming Bookings -->
<h2 class="text-center text-gray-700 text-xl font-bold tracking-widest mb-6 mt-12">UPCOMING BOOKINGS</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
    <!-- Example booking card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-blue-200">
        <div class="flex items-center gap-4 mb-4">
            <img src="/images/default-badminton-court.jpg" class="h-16 w-16 object-cover rounded-xl border border-blue-100" alt="Court">
            <div>
                <div class="text-lg font-bold text-blue-700">Court 1</div>
                <div class="text-gray-500">2025-07-25, 8:00 AM - 9:00 AM</div>
            </div>
        </div>
        <div class="flex gap-2 mt-2">
            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Confirmed</span>
            <a href="#" class="text-red-500 hover:underline text-xs">Cancel</a>
        </div>
    </div>
    <!-- Repeat for more bookings or loop through user's bookings -->
</div>

<!-- Section: Shop Badminton Gear -->
<h2 class="text-center text-gray-700 text-xl font-bold tracking-widest mb-6">SHOP BADMINTON GEAR</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
    <!-- Example product card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-green-200">
        <img src="/images/default-badminton-court.jpg" class="h-24 w-24 object-cover rounded-xl border border-green-100 mb-4" alt="Product">
        <div class="text-lg font-bold text-green-700 mb-2">Yonex Astrox 99 Pro</div>
        <div class="text-gray-500 mb-2">RM 799.00</div>
        <a href="#" class="px-6 py-2 bg-green-600 text-white rounded-full font-semibold shadow hover:bg-green-700 transition">Add to Cart</a>
    </div>
    <!-- Repeat for more products or loop through products -->
</div>

<!-- Section: Latest News -->
<h2 class="text-center text-gray-700 text-xl font-bold tracking-widest mb-6">LATEST NEWS</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
    <!-- Example news card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col border-t-4 border-yellow-200">
        <div class="text-xs text-gray-400 mb-2">GENERAL - JULY 2025</div>
        <div class="text-2xl font-extrabold text-gray-800 mb-2">SmashZone Launches New Booking System</div>
        <div class="text-gray-600 mb-4">We are excited to announce the launch of our new real-time badminton court booking system and e-commerce platform. Book your court, shop gear, and enjoy more badminton!</div>
        <a href="#" class="text-blue-600 hover:underline font-semibold">Read More</a>
    </div>
    <!-- Repeat for more news or loop through news -->
</div>

<!-- Footer -->
<footer class="text-center text-gray-400 text-sm py-8">
    &copy; {{ date('Y') }} SmashZone. All rights reserved.
</footer>
@endsection
