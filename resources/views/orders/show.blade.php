@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Details</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
        </a>
    </div>

    <!-- Order Status -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">Order Status</h2>
            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $order->status_badge_class }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>
        @if($order->shipping)
            <div class="mt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">Shipping Status</span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->shipping->status_badge_class }}">
                        {{ $order->shipping->status_label }}
                    </span>
                </div>
                @if($order->shipping->tracking_number)
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Tracking Number</p>
                                <p class="text-lg font-bold text-gray-900">{{ $order->shipping->tracking_number }}</p>
                            </div>
                            @if($order->shipping->carrier)
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Carrier</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $order->shipping->carrier }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                @if($order->shipping->estimated_delivery_date)
                    <div class="mt-2 text-sm text-gray-600">
                        <strong>Estimated Delivery:</strong> {{ $order->shipping->estimated_delivery_date->format('M d, Y') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Shipping Progress -->
    @if($order->shipping)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Progress</h2>
            <div class="relative">
                <!-- Progress Bar -->
                <div class="h-2 bg-gray-200 rounded-full mb-6">
                    <div class="h-2 bg-blue-600 rounded-full transition-all duration-500" 
                         style="width: {{ $order->shipping->progress_percentage }}%"></div>
                </div>
                <!-- Status Steps -->
                <div class="grid grid-cols-5 gap-2 text-xs">
                    <div class="text-center {{ $order->shipping->progress_percentage >= 0 ? 'text-blue-600' : 'text-gray-400' }}">
                        <div class="w-3 h-3 rounded-full {{ $order->shipping->progress_percentage >= 0 ? 'bg-blue-600' : 'bg-gray-300' }} mx-auto mb-1"></div>
                        <p>Pending</p>
                    </div>
                    <div class="text-center {{ $order->shipping->progress_percentage >= 25 ? 'text-blue-600' : 'text-gray-400' }}">
                        <div class="w-3 h-3 rounded-full {{ $order->shipping->progress_percentage >= 25 ? 'bg-blue-600' : 'bg-gray-300' }} mx-auto mb-1"></div>
                        <p>Processing</p>
                    </div>
                    <div class="text-center {{ $order->shipping->progress_percentage >= 50 ? 'text-blue-600' : 'text-gray-400' }}">
                        <div class="w-3 h-3 rounded-full {{ $order->shipping->progress_percentage >= 50 ? 'bg-blue-600' : 'bg-gray-300' }} mx-auto mb-1"></div>
                        <p>Shipped</p>
                    </div>
                    <div class="text-center {{ $order->shipping->progress_percentage >= 75 ? 'text-blue-600' : 'text-gray-400' }}">
                        <div class="w-3 h-3 rounded-full {{ $order->shipping->progress_percentage >= 75 ? 'bg-blue-600' : 'bg-gray-300' }} mx-auto mb-1"></div>
                        <p>In Transit</p>
                    </div>
                    <div class="text-center {{ $order->shipping->progress_percentage >= 100 ? 'text-green-600' : 'text-gray-400' }}">
                        <div class="w-3 h-3 rounded-full {{ $order->shipping->progress_percentage >= 100 ? 'bg-green-600' : 'bg-gray-300' }} mx-auto mb-1"></div>
                        <p>Delivered</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Order Items</h2>
        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    @if($item->product && $item->product->image)
                        <img src="{{ $item->product->image_url ?? asset('storage/' . $item->product->image) }}" 
                             alt="{{ $item->product_name }}" 
                             class="w-20 h-20 object-cover rounded-lg">
                    @endif
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                        <p class="text-sm text-gray-600">Price: {{ $item->formatted_price }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">{{ $item->formatted_subtotal }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex justify-between items-center text-xl font-bold text-gray-900">
                <span>Total</span>
                <span>{{ $order->formatted_total }}</span>
            </div>
        </div>
    </div>

    <!-- Delivery Information -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Delivery Information</h2>
        <div class="space-y-2 text-sm">
            <p><strong>Method:</strong> {{ $order->delivery_method_label }}</p>
            @if($order->delivery_method === 'delivery')
                <p><strong>Address:</strong> {{ $order->full_delivery_address }}</p>
                @if($order->delivery_phone)
                    <p><strong>Phone:</strong> {{ $order->delivery_phone }}</p>
                @endif
            @else
                <p class="text-gray-600">Please collect your order from our store location.</p>
            @endif
            @if($order->notes)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm"><strong>Notes:</strong> {{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment Information -->
    @if($order->payment)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Information</h2>
            <div class="space-y-2 text-sm">
                <p><strong>Payment Status:</strong> 
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $order->payment->status_badge_class }}">
                        {{ ucfirst($order->payment->status) }}
                    </span>
                </p>
                <p><strong>Amount:</strong> {{ $order->payment->formatted_amount }}</p>
                <p><strong>Payment Method:</strong> {{ $order->payment->payment_method }}</p>
                @if($order->payment->payment_date)
                    <p><strong>Paid On:</strong> {{ $order->payment->payment_date->format('M d, Y h:i A') }}</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Order Actions (Only for customers when delivered) -->
    @if($order->status === 'delivered' && $order->user_id === auth()->id() && !$order->received_at && !$order->return_requested_at)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Order Actions</h2>
            <p class="text-gray-600 mb-4">Your order has been delivered. Please confirm receipt or request a return if needed.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Mark as Received -->
                <form action="{{ route('orders.mark-received', $order, absolute: false) }}" method="POST" onsubmit="return confirm('Are you sure you want to mark this order as received?');">
                    @csrf
                    <button type="submit" class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-bold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Mark as Received</span>
                        </div>
                    </button>
                </form>

                <!-- Request Return -->
                <button type="button" onclick="showReturnModal()" class="w-full px-6 py-4 bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-xl font-bold hover:from-orange-700 hover:to-orange-800 transition-all transform hover:scale-105 shadow-lg">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Request Return</span>
                    </div>
                </button>
            </div>
        </div>
    @endif

    <!-- Return Requested Message -->
    @if($order->return_requested_at)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-orange-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-orange-900 mb-2">Return Request Submitted</h3>
                    <p class="text-orange-800 mb-2">Your return request was submitted on {{ $order->return_requested_at->format('M d, Y h:i A') }}.</p>
                    @if($order->return_reason)
                        <div class="mt-3 p-3 bg-white rounded-lg">
                            <p class="text-sm font-semibold text-gray-700 mb-1">Return Reason:</p>
                            <p class="text-sm text-gray-600">{{ $order->return_reason }}</p>
                        </div>
                    @endif
                    <p class="text-sm text-orange-700 mt-3">We will process your return request and contact you shortly.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Order Received Message -->
    @if($order->received_at)
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-green-900">Order Received</h3>
                    <p class="text-green-800">You confirmed receipt on {{ $order->received_at->format('M d, Y h:i A') }}. Thank you for your purchase!</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Return Request Modal -->
    <div id="return-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Request Return</h3>
                <button onclick="hideReturnModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('orders.request-return', $order, absolute: false) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Return <span class="text-red-500">*</span>
                    </label>
                    <textarea name="return_reason" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                              placeholder="Please provide a reason for returning this order..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Please provide details about why you want to return this order.</p>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="hideReturnModal()" 
                            class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-semibold">
                        Submit Return Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showReturnModal() {
    document.getElementById('return-modal').classList.remove('hidden');
}

function hideReturnModal() {
    document.getElementById('return-modal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('return-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideReturnModal();
    }
});
</script>
@endsection

