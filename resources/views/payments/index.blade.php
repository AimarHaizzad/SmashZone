@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-purple-900/90 to-blue-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-purple-600 to-blue-600 rounded-3xl p-8 text-center">
        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-2">Financial Management</h1>
        <p class="text-xl text-purple-100 font-medium">Track payments, refunds, and financial transactions</p>
    </div>
</div>

@php
    $user = auth()->user();
    $showRevenueCard = !$user->isCustomer();
    $statsGridCols = $showRevenueCard ? 'lg:grid-cols-5' : 'lg:grid-cols-4';
@endphp

<div class="max-w-7xl mx-auto py-8 px-4">
    <!-- Financial Analytics -->
    <div class="grid grid-cols-1 md:grid-cols-2 {{ $statsGridCols }} gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Payments</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $payments->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Successful Payments</p>
                    <p class="text-2xl font-bold text-green-600">{{ $payments->where('status', 'paid')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Payments</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $payments->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Refunds</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $refunds->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
        </div>
        
        @if($showRevenueCard)
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Net Revenue</p>
                        <p class="text-2xl font-bold text-purple-600">RM {{ number_format($payments->where('status', 'paid')->sum('amount') - $refunds->where('status', 'completed')->sum('amount'), 2) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                        </svg>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Financial Transactions
                </h2>
                
                <!-- Tab Buttons -->
                <div class="flex items-center gap-2">
                    <button onclick="showTab('payments')" id="tab-payments" class="tab-btn active px-4 py-2 rounded-lg text-sm font-medium bg-purple-600 text-white">
                        Payments
                    </button>
                    <button onclick="showTab('refunds')" id="tab-refunds" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-orange-200 hover:text-orange-800">
                        Refunds
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tab Content -->
        <div class="p-6">
            <!-- Payments Tab -->
            <div id="payments-content" class="tab-content">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Payment Transactions</h3>
                    
                    <!-- Payment Filter Buttons -->
                <div class="flex items-center gap-2">
                        <button onclick="filterPayments('all')" id="filter-all" class="filter-btn active px-3 py-1 rounded-lg text-sm font-medium bg-purple-600 text-white">
                        All
                    </button>
                        <button onclick="filterPayments('paid')" id="filter-paid" class="filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-green-200 hover:text-green-800">
                        Paid
                    </button>
                        <button onclick="filterPayments('pending')" id="filter-pending" class="filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-yellow-200 hover:text-yellow-800">
                        Pending
                    </button>
                        <button onclick="filterPayments('failed')" id="filter-failed" class="filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-red-200 hover:text-red-800">
                        Failed
                    </button>
            </div>
        </div>
        
        @if($payments->isEmpty())
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Payments Found</h3>
                <p class="text-gray-500">There are no payment transactions to display.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Booking Details
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Amount
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Payment Date
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            <tr class="payment-row hover:bg-gray-50 transition-colors" data-status="{{ $payment->status }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">
                                                {{ strtoupper(substr($payment->user->name ?? 'N/A', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $payment->user->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($payment->bookings->isNotEmpty())
                                        <div class="space-y-3">
                                            @foreach($payment->bookings as $bookingSummary)
                                                <div class="text-sm text-gray-900 border border-gray-100 rounded-lg p-3 bg-gray-50">
                                                    <div class="font-semibold">{{ $bookingSummary->court->name ?? 'Court ' . $bookingSummary->court_id }}</div>
                                                    <div class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($bookingSummary->date)->format('M d, Y (l)') }}</div>
                                                    <div class="text-gray-500 text-xs">
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingSummary->start_time)->format('g:i A') }} - 
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingSummary->end_time)->format('g:i A') }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500">No linked bookings</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-gray-900">RM {{ number_format($payment->amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $paymentStatus = strtolower($payment->status);
                                        $statusConfig = match($paymentStatus) {
                                            'paid' => ['label' => 'Paid', 'classes' => 'bg-green-100 text-green-800', 'icon' => 'check'],
                                            'failed' => ['label' => 'Failed', 'classes' => 'bg-red-100 text-red-800', 'icon' => 'x'],
                                            'cancelled' => ['label' => 'Cancelled', 'classes' => 'bg-gray-100 text-gray-600', 'icon' => 'x'],
                                            default => ['label' => 'Pending', 'classes' => 'bg-yellow-100 text-yellow-800', 'icon' => 'clock'],
                                        };
                                    @endphp
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $statusConfig['classes'] }}">
                                        @if($statusConfig['icon'] === 'check')
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @elseif($statusConfig['icon'] === 'clock')
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        @endif
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($payment->payment_date)
                                        <div>{{ $payment->payment_date->format('M j, Y') }}</div>
                                        <div class="text-gray-500">{{ $payment->payment_date->format('g:i A') }}</div>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
            <!-- Refunds Tab -->
            <div id="refunds-content" class="tab-content hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Refund History</h3>
                    
                    <!-- Refund Filter Buttons -->
                    <div class="flex items-center gap-2">
                        <button onclick="filterRefunds('all')" id="refund-filter-all" class="refund-filter-btn active px-3 py-1 rounded-lg text-sm font-medium bg-orange-600 text-white">
                            All
                        </button>
                        <button onclick="filterRefunds('completed')" id="refund-filter-completed" class="refund-filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-green-200 hover:text-green-800">
                            Completed
                        </button>
                        <button onclick="filterRefunds('processing')" id="refund-filter-processing" class="refund-filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-blue-200 hover:text-blue-800">
                            Processing
                        </button>
                        <button onclick="filterRefunds('pending')" id="refund-filter-pending" class="refund-filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-yellow-200 hover:text-yellow-800">
                            Pending
                        </button>
                        <button onclick="filterRefunds('failed')" id="refund-filter-failed" class="refund-filter-btn px-3 py-1 rounded-lg text-sm font-medium bg-gray-200 text-gray-700 hover:bg-red-200 hover:text-red-800">
                            Failed
                        </button>
                    </div>
                </div>
                
                @if($refunds->isEmpty())
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Refunds Found</h3>
                        <p class="text-gray-500">There are no refund transactions to display.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Customer
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Booking Details
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Refund Amount
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Refund Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($refunds as $refund)
                                    <tr class="refund-row hover:bg-gray-50 transition-colors" data-status="{{ $refund->status }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center mr-3">
                                                    <span class="text-white font-semibold text-sm">
                                                        {{ strtoupper(substr($refund->user->name ?? 'N/A', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-gray-900">{{ $refund->user->name ?? 'N/A' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $refund->user->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($refund->booking)
                                                <div class="text-sm text-gray-900">
                                                    <div class="font-semibold">{{ $refund->booking->court->name ?? 'N/A' }}</div>
                                                    <div class="text-gray-500">{{ $refund->booking->date }}</div>
                                                    <div class="text-gray-500">{{ $refund->booking->start_time }} - {{ $refund->booking->end_time }}</div>
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">Product Refund</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-lg font-bold text-red-600">-RM {{ number_format($refund->amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $refund->status_badge_class }}">
                                                @if($refund->status === 'completed')
                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @elseif($refund->status === 'processing')
                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                @elseif($refund->status === 'pending')
                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                @endif
                                                {{ ucfirst($refund->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($refund->refunded_at)
                                                <div>{{ $refund->refunded_at->format('M j, Y') }}</div>
                                                <div class="text-gray-500">{{ $refund->refunded_at->format('g:i A') }}</div>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Financial Insights -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    Recent Financial Activity
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($payments->take(3) as $payment)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-800">{{ $payment->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">+RM {{ number_format($payment->amount, 2) }} • {{ $payment->status }}</div>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $payment->payment_date ? $payment->payment_date->diffForHumans() : 'N/A' }}
                            </div>
                        </div>
                    @endforeach
                    
                    @foreach($refunds->take(2) as $refund)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-semibold text-gray-800">{{ $refund->user->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">-RM {{ number_format($refund->amount, 2) }} • {{ $refund->status }}</div>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $refund->refunded_at ? $refund->refunded_at->diffForHumans() : 'N/A' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Payment Methods -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Accepted Payment Methods
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Credit Cards</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">FPX Banking</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Digital Wallets</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-800">Bank Transfer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-purple-600', 'bg-orange-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    // Show selected tab content
    document.getElementById(`${tabName}-content`).classList.remove('hidden');
    
    // Activate selected tab button
    const activeBtn = document.getElementById(`tab-${tabName}`);
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
    activeBtn.classList.add('active');
    
    if (tabName === 'payments') {
        activeBtn.classList.add('bg-purple-600', 'text-white');
    } else if (tabName === 'refunds') {
        activeBtn.classList.add('bg-orange-600', 'text-white');
    }
}

// Payment filtering
function filterPayments(status) {
    const rows = document.querySelectorAll('.payment-row');
    const buttons = document.querySelectorAll('.filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-purple-600', 'text-white', 'bg-green-600', 'bg-yellow-600', 'bg-red-600');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeBtn = document.getElementById(`filter-${status}`);
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
    activeBtn.classList.add('active');
    
    if (status === 'all') {
        activeBtn.classList.add('bg-purple-600', 'text-white');
    } else if (status === 'paid') {
        activeBtn.classList.add('bg-green-600', 'text-white');
    } else if (status === 'pending') {
        activeBtn.classList.add('bg-yellow-600', 'text-white');
    } else if (status === 'failed') {
        activeBtn.classList.add('bg-red-600', 'text-white');
    }
    
    // Filter rows
    rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-status') === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Refund filtering
function filterRefunds(status) {
    const rows = document.querySelectorAll('.refund-row');
    const buttons = document.querySelectorAll('.refund-filter-btn');
    
    // Update button styles
    buttons.forEach(btn => {
        btn.classList.remove('active', 'bg-orange-600', 'text-white', 'bg-green-600', 'bg-blue-600', 'bg-yellow-600', 'bg-red-600');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    const activeBtn = document.getElementById(`refund-filter-${status}`);
    activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
    activeBtn.classList.add('active');
    
    if (status === 'all') {
        activeBtn.classList.add('bg-orange-600', 'text-white');
    } else if (status === 'completed') {
        activeBtn.classList.add('bg-green-600', 'text-white');
    } else if (status === 'processing') {
        activeBtn.classList.add('bg-blue-600', 'text-white');
    } else if (status === 'pending') {
        activeBtn.classList.add('bg-yellow-600', 'text-white');
    } else if (status === 'failed') {
        activeBtn.classList.add('bg-red-600', 'text-white');
    }
    
    // Filter rows
    rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-status') === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection 