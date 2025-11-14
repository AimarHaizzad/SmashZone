@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-green-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-blue-600 to-green-600 rounded-3xl p-8 text-center">
        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-2">My Bookings</h1>
        <p class="text-xl text-blue-100 font-medium">Manage your court reservations and payments</p>
    </div>
</div>

<div class="max-w-7xl mx-auto py-8 px-4">

    @if($bookings->isEmpty())
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-12 text-center">
            <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-3">No Bookings Yet</h2>
            <p class="text-gray-600 mb-8 text-lg">Ready to play? Book your first court and start enjoying your game!</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('courts.index') }}" 
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4" />
                    </svg>
                    Browse Courts
                </a>
                <a href="{{ route('bookings.create') }}" 
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Book Now
                </a>
            </div>
        </div>
    @else
        <!-- Enhanced Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $bookings->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Payment</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $pendingPaymentsCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Paid</p>
                        <p class="text-2xl font-bold text-green-600">{{ $paidPaymentsCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Spent</p>
                        <p class="text-2xl font-bold text-blue-600">RM {{ number_format($totalSpent, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Table -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-green-50 px-6 py-4 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                    </svg>
                    Booking History
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Court</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php $renderedPaymentButtons = []; @endphp
                        @foreach($bookings as $booking)
                            @php
                                $startDateTime = \Carbon\Carbon::parse("{$booking->date} {$booking->start_time}");
                                $cancelDeadline = $startDateTime->copy()->subHour();
                                $canCancel = now()->lt($cancelDeadline);
                                $payment = $booking->payment;
                                $paymentStatus = strtolower($payment->status ?? 'pending');
                                $paymentExpired = $paymentStatus === 'pending' && now()->greaterThanOrEqualTo($startDateTime);
                                
                                if ($paymentExpired) {
                                    $paymentStatus = 'failed';
                                }
                                $paymentId = $payment->id ?? null;
                                $showPayButton = $payment && $paymentStatus === 'pending' && $paymentId && !in_array($paymentId, $renderedPaymentButtons);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-green-100 rounded-xl flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4" />
                                            </svg>
                                        </div>
                                        <div class="font-semibold text-gray-900">{{ $booking->court->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->date)->format('l') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-medium">{{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('g:i A') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->diffInHours(\Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)) }} hour(s)</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-lg font-bold text-blue-600">RM {{ number_format($booking->total_price, 2) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusConfig = match($paymentStatus) {
                                            'paid' => ['label' => 'Paid', 'classes' => 'bg-green-100 text-green-800', 'icon' => 'check'],
                                            'failed' => ['label' => 'Failed', 'classes' => 'bg-red-100 text-red-800', 'icon' => 'x'],
                                            'cancelled' => ['label' => 'Cancelled', 'classes' => 'bg-gray-100 text-gray-600', 'icon' => 'x'],
                                            default => ['label' => 'Pending', 'classes' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusConfig['classes'] }}">
                                        @if($statusConfig['icon'] === 'check')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @elseif($statusConfig['icon'] === 'clock')
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 18L18 6M6 6l12 12" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <button class="details-btn px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg font-medium hover:bg-blue-100 transition-colors text-sm" data-booking-id="{{ $booking->id }}">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Details
                                        </button>
                                        @if($showPayButton)
                                            @php $renderedPaymentButtons[] = $paymentId; @endphp
                                            <a href="{{ route('payments.pay', $payment) }}" class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg font-medium hover:bg-green-100 transition-colors text-sm">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                Pay {{ $payment->bookings->count() > 1 ? '(' . $payment->bookings->count() . ' slots)' : '' }}
                                            </a>
                                        @elseif($payment && $paymentStatus === 'pending')
                                            <span class="px-3 py-1.5 bg-gray-50 text-gray-400 rounded-lg font-medium text-sm cursor-not-allowed" title="Included in another pending payment">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                                Pay
                                            </span>
                                        @endif
                                        @if($canCancel)
                                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg font-medium hover:bg-red-100 transition-colors text-sm">
                                                    <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span class="px-3 py-1.5 bg-gray-100 text-gray-400 rounded-lg font-medium text-sm cursor-not-allowed" title="Cancellations must be made at least 1 hour before the start time">
                                                <svg class="w-4 h-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Cancel
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Details Modal -->
        <div id="booking-details-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-lg w-full mx-4 relative transform transition-all">
                <button id="close-details-modal" class="absolute top-4 right-4 w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
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
                            const startTime = new Date(`2000-01-01T${booking.start_time}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                            const endTime = new Date(`2000-01-01T${booking.end_time}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                            const bookingDate = new Date(booking.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                            
                            let html = `
                                <div class="text-center mb-6">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Booking Details</h2>
                                    <p class="text-gray-600">Complete booking information</p>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-600">Court</span>
                                            <span class="font-semibold text-gray-900">${booking.court.name}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-600">Date</span>
                                            <span class="font-semibold text-gray-900">${bookingDate}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-600">Time</span>
                                            <span class="font-semibold text-gray-900">${startTime} - ${endTime}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-blue-600">Total Amount</span>
                                            <span class="text-xl font-bold text-blue-700">RM ${Number(booking.total_price).toFixed(2)}</span>
                                        </div>
                                    </div>
                                </div>
                                `;
                            
                            if (booking.payment && booking.payment.status === 'pending') {
                                html += `
                                    <div class="mt-6 pt-6 border-t border-gray-200">
                                        <a href="/payments/${booking.payment.id}/pay" 
                                           class="block w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-xl font-bold text-center hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                                            <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Proceed to Payment
                                        </a>
                                    </div>
                                `;
                            }
                            
                            document.getElementById('details-modal-content').innerHTML = html;
                            document.getElementById('booking-details-modal').classList.remove('hidden');
                        });
                });
            });
            
            document.getElementById('close-details-modal').onclick = function(){
                document.getElementById('booking-details-modal').classList.add('hidden');
            }
            
            // Close modal when clicking outside
            document.getElementById('booking-details-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        </script>
    @endif
</div>
@endsection

