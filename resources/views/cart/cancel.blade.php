@extends('layouts.app')

@section('content')
<!-- Enhanced Cancel Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-red-900/90 to-orange-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-red-600 to-orange-600 rounded-3xl p-8 text-center">
        <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-2">Payment Cancelled</h1>
        <p class="text-xl text-red-100 font-medium">Your payment was not completed</p>
    </div>
</div>

<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Cancel Card -->
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-red-50 to-orange-50 px-8 py-6 border-b border-gray-100">
            <div class="flex items-center justify-center gap-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Payment Not Completed</h2>
                    <p class="text-gray-600">Your payment was cancelled and no charges were made</p>
                </div>
            </div>
        </div>
        
        <div class="p-8">
            <!-- What Happened -->
            <div class="bg-red-50 rounded-2xl p-6 mb-8 border border-red-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    What Happened?
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Payment Cancelled</p>
                            <p class="text-sm text-gray-600">You cancelled the payment process before it was completed.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">No Charges Made</p>
                            <p class="text-sm text-gray-600">No money was deducted from your account.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Items Still in Cart</p>
                            <p class="text-sm text-gray-600">Your items are still in your cart and ready for checkout.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Common Reasons -->
            <div class="bg-orange-50 rounded-2xl p-6 mb-8 border border-orange-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    Common Reasons for Cancellation
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-orange-400 rounded-full mt-2 flex-shrink-0"></div>
                        <p class="text-gray-700">Changed your mind about the purchase</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-orange-400 rounded-full mt-2 flex-shrink-0"></div>
                        <p class="text-gray-700">Payment method issues or concerns</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-orange-400 rounded-full mt-2 flex-shrink-0"></div>
                        <p class="text-gray-700">Accidental cancellation</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="w-2 h-2 bg-orange-400 rounded-full mt-2 flex-shrink-0"></div>
                        <p class="text-gray-700">Technical difficulties</p>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('cart.index') }}" 
                   class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                    </svg>
                    Back to Cart
                </a>
                <a href="{{ route('products.index') }}" 
                   class="flex-1 inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
    
    <!-- Help Section -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                </svg>
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">Items Saved</h4>
            <p class="text-sm text-gray-600">Your cart items are still available for checkout</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">Try Again</h4>
            <p class="text-sm text-gray-600">You can attempt the payment again anytime</p>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 text-center">
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h4 class="font-semibold text-gray-800 mb-2">Need Help?</h4>
            <p class="text-sm text-gray-600">Contact support if you need assistance</p>
        </div>
    </div>
    
    <!-- Support Information -->
    <div class="mt-8 bg-gray-50 rounded-2xl p-6 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z" />
            </svg>
            Having Payment Issues?
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Payment Methods</h4>
                <p class="text-gray-600 mb-2">We accept various payment methods including:</p>
                <ul class="space-y-1 text-gray-600">
                    <li>• Credit and Debit Cards</li>
                    <li>• Malaysian Online Banking (FPX)</li>
                    <li>• Digital Wallets</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-gray-800 mb-2">Security</h4>
                <p class="text-gray-600 mb-2">Your payment information is secure:</p>
                <ul class="space-y-1 text-gray-600">
                    <li>• SSL Encrypted</li>
                    <li>• PCI Compliant</li>
                    <li>• No card data stored</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 