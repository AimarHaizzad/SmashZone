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
                        <img src="{{ asset('storage/' . $item->product->image) }}" 
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
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
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
</div>
@endsection

