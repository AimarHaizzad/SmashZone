@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Orders</h1>
        <p class="text-gray-600">Track and manage your orders</p>
    </div>

    @if($orders->count() === 0)
        <div class="text-center py-16 bg-white rounded-xl shadow-lg">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-700 mb-2">No Orders Yet</h2>
            <p class="text-gray-500 mb-6">You haven't placed any orders yet.</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Start Shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $order->order_number }}</h3>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->status_badge_class }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 space-y-1">
                                    <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                                    <p><strong>Total:</strong> {{ $order->formatted_total }}</p>
                                    <p><strong>Delivery:</strong> {{ $order->delivery_method_label }}</p>
                                    @if($order->shipping && $order->shipping->tracking_number)
                                        <p><strong>Tracking:</strong> {{ $order->shipping->tracking_number }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                                    View Details
                                </a>
                                @if($order->shipping)
                                    <div class="text-xs text-gray-500 text-center">
                                        {{ $order->shipping->status_label }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

