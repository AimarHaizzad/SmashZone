@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Management</h1>
        <p class="text-gray-600">Manage and track all customer orders</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4">
            <div class="text-sm text-gray-600 mb-1">Total Orders</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-yellow-50 rounded-xl shadow-lg border border-yellow-200 p-4">
            <div class="text-sm text-yellow-700 mb-1">Pending</div>
            <div class="text-2xl font-bold text-yellow-800">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-purple-50 rounded-xl shadow-lg border border-purple-200 p-4">
            <div class="text-sm text-purple-700 mb-1">Processing</div>
            <div class="text-2xl font-bold text-purple-800">{{ $stats['processing'] }}</div>
        </div>
        <div class="bg-indigo-50 rounded-xl shadow-lg border border-indigo-200 p-4">
            <div class="text-sm text-indigo-700 mb-1">Shipped</div>
            <div class="text-2xl font-bold text-indigo-800">{{ $stats['shipped'] }}</div>
        </div>
        <div class="bg-green-50 rounded-xl shadow-lg border border-green-200 p-4">
            <div class="text-sm text-green-700 mb-1">Delivered</div>
            <div class="text-2xl font-bold text-green-800">{{ $stats['delivered'] }}</div>
        </div>
        <div class="bg-red-50 rounded-xl shadow-lg border border-red-200 p-4">
            <div class="text-sm text-red-700 mb-1">Cancelled</div>
            <div class="text-2xl font-bold text-red-800">{{ $stats['cancelled'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Order #, Customer name..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Order Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Shipping Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Shipping Status</label>
                <select name="shipping_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Shipping Statuses</option>
                    <option value="pending" {{ request('shipping_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="preparing" {{ request('shipping_status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready_for_pickup" {{ request('shipping_status') == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                    <option value="in_transit" {{ request('shipping_status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="out_for_delivery" {{ request('shipping_status') == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ request('shipping_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            <!-- Delivery Method -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Method</label>
                <select name="delivery_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Methods</option>
                    <option value="pickup" {{ request('delivery_method') == 'pickup' ? 'selected' : '' }}>Self Pickup</option>
                    <option value="delivery" {{ request('delivery_method') == 'delivery' ? 'selected' : '' }}>Home Delivery</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Apply Filters
                </button>
                <a href="{{ route('orders.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipping</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $order->order_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">{{ $order->formatted_total }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->status_badge_class }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->shipping)
                                    <div class="text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $order->shipping->status_badge_class }}">
                                            {{ $order->shipping->status_label }}
                                        </span>
                                        @if($order->shipping->tracking_number)
                                            <div class="text-xs text-gray-500 mt-1">{{ $order->shipping->tracking_number }}</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">No shipping</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-600">{{ $order->delivery_method_label }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('orders.show', $order) }}" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No orders found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

