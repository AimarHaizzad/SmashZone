@extends('layouts.app')

@section('content')
<!-- Enhanced Success Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-green-900/90 to-blue-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-green-600 to-blue-600 rounded-3xl p-8 text-center">
        <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-2">Payment Successful!</h1>
        <p class="text-xl text-green-100 font-medium">Your order has been confirmed and payment processed</p>
    </div>
</div>

<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Success Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-blue-50 px-8 py-6 border-b border-gray-100">
            <div class="flex items-center justify-center gap-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Thank You for Your Purchase!</h2>
                    <p class="text-gray-600">Your payment was processed successfully</p>
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <!-- Order Confirmation -->
            <div class="bg-blue-50 rounded-2xl p-6 mb-8 border border-blue-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Order Confirmation
                </h3>
                @if(isset($order))
                    <div class="mb-4 p-4 bg-white rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Order Number</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $order->order_number }}</p>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-semibold text-gray-700">Order Date:</span>
                        <span class="text-gray-600 ml-2">{{ now()->format('F j, Y') }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Order Time:</span>
                        <span class="text-gray-600 ml-2">{{ now()->format('g:i A') }}</span>
                    </div>
                    @if(isset($order))
                        <div>
                            <span class="font-semibold text-gray-700">Total Amount:</span>
                            <span class="text-gray-600 ml-2">{{ $order->formatted_total }}</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-700">Delivery Method:</span>
                            <span class="text-gray-600 ml-2">{{ $order->delivery_method_label }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="font-semibold text-gray-700">Payment Method:</span>
                        <span class="text-gray-600 ml-2">Stripe (Secure Payment)</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-700">Status:</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Paid
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- What's Next -->
            <div class="bg-green-50 rounded-2xl p-6 mb-8 border border-green-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    What's Next?
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-green-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Order Confirmation Email</p>
                            <p class="text-sm text-gray-600">You'll receive a confirmation email with your order details shortly.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-green-600 text-xs font-bold">2</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Order Processing</p>
                            <p class="text-sm text-gray-600">Your order is being processed and will be prepared for delivery.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-green-600 text-xs font-bold">3</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Delivery Updates</p>
                            <p class="text-sm text-gray-600">You'll receive updates about your order status and delivery.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                @if(isset($order))
                    <a href="{{ route('orders.show', $order) }}" 
                       class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-xl font-semibold hover:from-purple-700 hover:to-purple-800 transition-all transform hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        View Order Details
                    </a>
                @endif
                <a href="{{ route('products.index') }}" 
                   class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Continue Shopping
                </a>
                <a href="{{ route('orders.index') }}" 
                   class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    My Orders
                </a>
            </div>
        </div>
    </div>
    
    <!-- Additional Information -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">Secure Payment</h4>
            <p class="text-sm text-gray-600">Your payment was processed securely through Stripe</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">Order Confirmed</h4>
            <p class="text-sm text-gray-600">Your order has been confirmed and is being processed</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">Email Confirmation</h4>
            <p class="text-sm text-gray-600">You'll receive an email confirmation shortly</p>
        </div>
    </div>
</div>

<style>
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -30px, 0);
    }
    70% {
        transform: translate3d(0, -15px, 0);
    }
    90% {
        transform: translate3d(0, -4px, 0);
    }
}
.animate-bounce {
    animation: bounce 2s infinite;
}
</style>
@endsection 