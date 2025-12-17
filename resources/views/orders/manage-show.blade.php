@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Management</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Orders
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Status Management -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Status</h2>
                <form action="{{ route('orders.update-status', $order, absolute: false) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $order->status_badge_class }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="return_requested" {{ $order->status == 'return_requested' ? 'selected' : '' }}>Return Requested</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Update Order Status
                    </button>
                </form>
            </div>

            <!-- Return Request Management -->
            @if($order->status === 'return_requested' && $order->return_requested_at)
                <div class="bg-orange-50 border-2 border-orange-200 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-orange-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Return Request Pending Approval
                    </h2>
                    
                    <div class="mb-4 p-4 bg-white rounded-lg border border-orange-200">
                        <div class="mb-3">
                            <span class="text-sm font-semibold text-gray-700">Return Requested:</span>
                            <span class="text-sm text-gray-600 ml-2">{{ $order->return_requested_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @if($order->return_reason)
                            <div>
                                <span class="text-sm font-semibold text-gray-700">Reason:</span>
                                <p class="text-sm text-gray-600 mt-1">{{ $order->return_reason }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Approve Return -->
                        <form action="{{ route('orders.approve-return', $order, absolute: false) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this return? This will cancel the order and process a refund.')">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Approve Return & Refund
                            </button>
                        </form>

                        <!-- Reject Return -->
                        <button type="button" onclick="showRejectModal()" class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reject Return
                        </button>
                    </div>

                    <!-- Reject Modal -->
                    <div id="reject-modal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                        <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Reject Return Request</h3>
                            <form action="{{ route('orders.reject-return', $order, absolute: false) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason *</label>
                                    <textarea name="rejection_reason" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Please provide a reason for rejecting this return request..."></textarea>
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" onclick="hideRejectModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                                        Cancel
                                    </button>
                                    <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                                        Reject Return
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function showRejectModal() {
                            document.getElementById('reject-modal').classList.remove('hidden');
                        }
                        function hideRejectModal() {
                            document.getElementById('reject-modal').classList.add('hidden');
                        }
                        // Close modal when clicking outside
                        document.getElementById('reject-modal').addEventListener('click', function(e) {
                            if (e.target === this) {
                                hideRejectModal();
                            }
                        });
                    </script>
                </div>
            @endif

            <!-- Shipping Management -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Shipping Management</h2>
                
                @if($order->shipping)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600">Current Shipping Status</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->shipping->status_badge_class }}">
                                {{ $order->shipping->status_label }}
                            </span>
                        </div>
                        @if($order->shipping->tracking_number)
                            <div class="text-sm">
                                <span class="text-gray-600">Tracking Number:</span>
                                <span class="font-bold text-gray-900">{{ $order->shipping->tracking_number }}</span>
                            </div>
                        @endif
                        @if($order->shipping->carrier)
                            <div class="text-sm mt-1">
                                <span class="text-gray-600">Carrier:</span>
                                <span class="font-semibold text-gray-900">{{ $order->shipping->carrier }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <form action="{{ route('orders.update-shipping', $order, absolute: false) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Status</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="preparing" {{ $order->shipping && $order->shipping->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="out_for_delivery" {{ $order->shipping && $order->shipping->status == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->shipping && $order->shipping->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->shipping && $order->shipping->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Carrier</label>
                        <select name="carrier" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Carrier</option>
                            <option value="Self Pickup" {{ $order->shipping && $order->shipping->carrier == 'Self Pickup' ? 'selected' : '' }}>Self Pickup</option>
                            <option value="PosLaju" {{ $order->shipping && $order->shipping->carrier == 'PosLaju' ? 'selected' : '' }}>PosLaju</option>
                            <option value="J&T Express" {{ $order->shipping && $order->shipping->carrier == 'J&T Express' ? 'selected' : '' }}>J&T Express</option>
                            <option value="DHL" {{ $order->shipping && $order->shipping->carrier == 'DHL' ? 'selected' : '' }}>DHL</option>
                            <option value="GDEX" {{ $order->shipping && $order->shipping->carrier == 'GDEX' ? 'selected' : '' }}>GDEX</option>
                            <option value="Ninja Van" {{ $order->shipping && $order->shipping->carrier == 'Ninja Van' ? 'selected' : '' }}>Ninja Van</option>
                            <option value="Lalamove" {{ $order->shipping && $order->shipping->carrier == 'Lalamove' ? 'selected' : '' }}>Lalamove</option>
                            <option value="Other" {{ $order->shipping && $order->shipping->carrier == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                        <input type="text" name="tracking_number" 
                               value="{{ $order->shipping ? $order->shipping->tracking_number : '' }}"
                               placeholder="Leave empty to auto-generate"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate when status changes to "Out for Delivery"</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Add shipping notes...">{{ $order->shipping ? $order->shipping->notes : '' }}</textarea>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                        Update Shipping Status
                    </button>
                </form>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
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
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Customer Information -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Customer Information</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Name:</span>
                        <p class="font-semibold text-gray-900">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Email:</span>
                        <p class="font-semibold text-gray-900">{{ $order->user->email }}</p>
                    </div>
                    @if($order->user->phone)
                        <div>
                            <span class="text-gray-600">Phone:</span>
                            <p class="font-semibold text-gray-900">{{ $order->user->phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Delivery Information</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Method:</span>
                        <p class="font-semibold text-gray-900">{{ $order->delivery_method_label }}</p>
                    </div>
                    @if($order->delivery_method === 'delivery')
                        <div>
                            <span class="text-gray-600">Address:</span>
                            <p class="font-semibold text-gray-900">{{ $order->full_delivery_address }}</p>
                        </div>
                        @if($order->delivery_phone)
                            <div>
                                <span class="text-gray-600">Phone:</span>
                                <p class="font-semibold text-gray-900">{{ $order->delivery_phone }}</p>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-600">Customer will pick up from store</p>
                    @endif
                    @if($order->notes)
                        <div>
                            <span class="text-gray-600">Notes:</span>
                            <p class="text-gray-900">{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            @if($order->payment)
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Payment Information</h2>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-600">Status:</span>
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $order->payment->status_badge_class }}">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Amount:</span>
                            <p class="font-semibold text-gray-900">{{ $order->payment->formatted_amount }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Method:</span>
                            <p class="font-semibold text-gray-900">{{ $order->payment->payment_method }}</p>
                        </div>
                        @if($order->payment->payment_date)
                            <div>
                                <span class="text-gray-600">Paid On:</span>
                                <p class="font-semibold text-gray-900">{{ $order->payment->payment_date->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Order Timeline -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Order Timeline</h2>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Order Placed</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @if($order->shipping && $order->shipping->shipped_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Shipped</p>
                                <p class="text-xs text-gray-500">{{ $order->shipping->shipped_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($order->shipping && $order->shipping->delivered_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Delivered</p>
                                <p class="text-xs text-gray-500">{{ $order->shipping->delivered_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

