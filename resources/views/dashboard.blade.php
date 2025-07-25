@extends('layouts.app')

@section('content')
@php $user = Auth::user(); @endphp

@if($user->isOwner())
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Owner Dashboard</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded shadow text-center">
                <div class="text-2xl font-bold text-blue-700">{{ $user->courts->count() }}</div>
                <div class="text-gray-500">Courts Owned</div>
            </div>
            <div class="bg-white p-6 rounded shadow text-center">
                <div class="text-2xl font-bold text-green-700">{{ $user->courts->flatMap->bookings->count() }}</div>
                <div class="text-gray-500">Total Bookings</div>
            </div>
            <div class="bg-white p-6 rounded shadow text-center">
                <div class="text-2xl font-bold text-yellow-700">RM {{ number_format($user->courts->flatMap->bookings->sum('total_price'), 2) }}</div>
                <div class="text-gray-500">Total Revenue</div>
            </div>
        </div>
        <h2 class="text-xl font-bold mb-4">Recent Bookings</h2>
        <div class="bg-white rounded shadow p-4 mb-8">
            <table class="w-full text-left">
                <thead><tr><th>Court</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($user->courts->flatMap->bookings->sortByDesc('date')->take(5) as $booking)
                    <tr>
                        <td>{{ $booking->court->name }}</td>
                        <td>{{ $booking->date }}</td>
                        <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                        <td>{{ ucfirst($booking->status) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@elseif($user->isStaff())
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Staff Dashboard</h1>
        <h2 class="text-xl font-bold mb-4">All Bookings</h2>
        <div class="bg-white rounded shadow p-4 mb-8">
            <table class="w-full text-left">
                <thead><tr><th>Court</th><th>Date</th><th>Time</th><th>User</th><th>Status</th></tr></thead>
                <tbody>
                @foreach(\App\Models\Booking::orderBy('date', 'desc')->take(10)->get() as $booking)
                    <tr>
                        <td>{{ $booking->court->name }}</td>
                        <td>{{ $booking->date }}</td>
                        <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ ucfirst($booking->status) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Welcome, {{ $user->name }}!</h1>
        <h2 class="text-xl font-bold mb-4">Your Upcoming Bookings</h2>
        <div class="bg-white rounded shadow p-4 mb-8">
            <table class="w-full text-left">
                <thead><tr><th>Court</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($user->bookings()->where('date', '>=', now()->toDateString())->orderBy('date')->take(5)->get() as $booking)
                    <tr>
                        <td>{{ $booking->court->name }}</td>
                        <td>{{ $booking->date }}</td>
                        <td>{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                        <td>{{ ucfirst($booking->status) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <h2 class="text-xl font-bold mb-4">Shop Badminton Gear</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            @foreach(\App\Models\Product::take(3)->get() as $product)
                <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center border-t-4 border-green-200">
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : '/images/default-badminton-court.jpg' }}" class="h-24 w-24 object-cover rounded-xl border border-green-100 mb-4" alt="Product">
                    <div class="text-lg font-bold text-green-700 mb-2">{{ $product->name }}</div>
                    <div class="text-gray-500 mb-2">RM {{ number_format($product->price, 2) }}</div>
                    <a href="#" class="px-6 py-2 bg-green-600 text-white rounded-full font-semibold shadow hover:bg-green-700 transition">Add to Cart</a>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection
