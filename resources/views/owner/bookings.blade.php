@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Bookings for Your Courts</h1>
    <div class="bg-white shadow rounded p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2">Court</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Time</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td class="px-4 py-2">{{ $booking->court->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $booking->user->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $booking->date }}</td>
                    <td class="px-4 py-2">{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                    <td class="px-4 py-2">{{ ucfirst($booking->status) }}</td>
                    <td class="px-4 py-2">
                        @if($booking->payment)
                            {{ ucfirst($booking->payment->status) }}<br>
                            RM {{ number_format($booking->payment->amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($bookings->isEmpty())
            <div class="text-center text-gray-500 py-8">No bookings found for your courts.</div>
        @endif
    </div>
</div>
@endsection 