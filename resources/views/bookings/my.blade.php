@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Bookings</h1>
        <p class="text-gray-600">View your bookings, check details, and proceed to payment.</p>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-white rounded-2xl shadow p-10 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" /></svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">No bookings yet</h2>
            <p class="text-gray-600 mb-6">Start by booking a court from the courts page.</p>
            <a href="{{ route('courts.index') }}" class="inline-flex items-center px-5 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">Browse Courts</a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Court</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $booking->court->name }}</td>
                                <td class="px-6 py-4 text-gray-800">{{ $booking->date }}</td>
                                <td class="px-6 py-4 text-gray-800">{{ $booking->start_time }} - {{ $booking->end_time }}</td>
                                <td class="px-6 py-4 font-semibold text-blue-700">RM {{ number_format($booking->total_price, 2) }}</td>
                                <td class="px-6 py-4">
                                    @php $status = $booking->payment->status ?? 'pending'; @endphp
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold
                                        {{ $status === 'paid' ? 'bg-green-100 text-green-800' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button class="details-btn text-blue-600 hover:text-blue-800 font-medium" data-booking-id="{{ $booking->id }}">Details</button>
                                        @if($booking->payment && $booking->payment->status === 'pending')
                                            <a href="{{ route('payments.pay', $booking->payment) }}" class="text-green-600 hover:text-green-800 font-medium">Pay</a>
                                        @endif
                                        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Cancel this booking?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Cancel</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Details Modal -->
        <div id="booking-details-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-md w-full mx-4 relative">
                <button id="close-details-modal" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold">&times;</button>
                <div id="details-modal-content"></div>
            </div>
        </div>

        <script>
            document.querySelectorAll('.details-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.bookingId;
                    fetch(`/booking-details/${id}`)
                        .then(r => r.json())
                        .then(booking => {
                            let html = `
                                <h2 class="text-2xl font-bold mb-4 text-gray-800">Booking Details</h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between"><span class="text-gray-600">Court:</span><span class="font-semibold">${booking.court.name}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-600">Date:</span><span class="font-semibold">${booking.date}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-600">Time:</span><span class="font-semibold">${booking.start_time} - ${booking.end_time}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-600">Amount:</span><span class="font-bold text-blue-600">RM ${Number(booking.total_price).toFixed(2)}</span></div>
                                </div>
                                `;
                            if (booking.payment && booking.payment.status === 'pending') {
                                html += `<a href="/payments/${booking.payment.id}/pay" class="mt-6 block w-full bg-green-600 text-white py-3 rounded-xl font-bold text-center">Proceed to Payment</a>`
                            }
                            document.getElementById('details-modal-content').innerHTML = html;
                            document.getElementById('booking-details-modal').classList.remove('hidden');
                        });
                });
            });
            document.getElementById('close-details-modal').onclick = function(){
                document.getElementById('booking-details-modal').classList.add('hidden');
            }
        </script>
    @endif
</div>
@endsection

