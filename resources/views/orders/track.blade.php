@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Order</h1>
        <p class="text-gray-600">Enter your order number to track your shipment</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <form action="{{ route('orders.track') }}" method="GET" class="flex gap-4">
            <input type="text" 
                   name="order_number" 
                   value="{{ request('order_number') }}"
                   placeholder="Enter order number (e.g., ORD-20250115-ABC123)" 
                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   required>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                Track
            </button>
        </form>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if(isset($order))
        <!-- Order Details -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $order->status_badge_class }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            @if($order->shipping)
                <div class="mt-4">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-1">Shipping Status</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->shipping->status_label }}</p>
                    </div>

                    @if($order->shipping->tracking_number)
                        <div class="p-4 bg-gray-50 rounded-lg mb-4">
                            <p class="text-sm text-gray-600 mb-1">Tracking Number</p>
                            <p class="text-xl font-bold text-gray-900">{{ $order->shipping->tracking_number }}</p>
                            @if($order->shipping->carrier)
                                <p class="text-sm text-gray-600 mt-1">Carrier: {{ $order->shipping->carrier }}</p>
                            @endif
                        </div>
                    @endif

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="h-2 bg-gray-200 rounded-full mb-4">
                            <div class="h-2 bg-blue-600 rounded-full transition-all duration-500" 
                                 style="width: {{ $order->shipping->progress_percentage }}%"></div>
                        </div>
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

                    @if($order->shipping->estimated_delivery_date)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Estimated Delivery:</strong> {{ $order->shipping->estimated_delivery_date->format('M d, Y') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600"><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                <p class="text-sm text-gray-600"><strong>Total:</strong> {{ $order->formatted_total }}</p>
            </div>
        </div>
    @endif
</div>
@endsection

