@extends('layouts.app')

@section('content')
<!-- Enhanced Hero Section -->
<div class="relative mb-12">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-green-900/90 rounded-3xl"></div>
    <div class="relative bg-gradient-to-r from-blue-600 to-green-600 rounded-3xl p-8 text-center">
        <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold text-white mb-2">Your Shopping Cart</h1>
        <p class="text-xl text-blue-100 font-medium">Review your items and proceed to checkout</p>
    </div>
</div>

<div class="max-w-6xl mx-auto py-8 px-4">
    @if(count($cart) === 0)
        <!-- Enhanced Empty Cart State -->
        <div class="text-center py-16">
            <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-700 mb-4">Your Cart is Empty</h2>
            <p class="text-gray-500 mb-8 text-lg">Looks like you haven't added any items to your cart yet.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('products.index') }}" 
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Start Shopping
                </a>
                <a href="{{ route('bookings.index') }}" 
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Book a Court
                </a>
            </div>
        </div>
    @else
        <!-- Enhanced Cart Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xl border border-gray-100 overflow-hidden" data-tutorial="cart-items">
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                            </svg>
                            Cart Items ({{ count($cart) }})
                        </h2>
                    </div>
                    
                    <form action="{{ route('cart.update', absolute: false) }}" method="POST" class="p-4 sm:p-6">
                        @csrf
                        <div class="space-y-4 sm:space-y-6">
                            @php $total = 0; @endphp
                            @foreach($products as $product)
                                @php $qty = $cart[$product->id] ?? 0; $subtotal = $qty * ($product->price ?? 0); $total += $subtotal; @endphp
                                <div class="flex items-center gap-2 sm:gap-3 md:gap-4 p-3 sm:p-4 md:p-6 bg-gray-50 rounded-xl md:rounded-2xl hover:bg-gray-100 transition-colors">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @if($product->image)
                                            <img src="{{ $product->image_url ?? asset('images/default-badminton-court.jpg') }}" 
                                                 alt="{{ $product->name }}"
                                                 onerror="this.onerror=null; this.src='{{ asset('images/default-badminton-court.jpg') }}';" 
                                                 class="h-14 w-14 sm:h-18 sm:w-18 md:h-20 md:w-20 lg:h-24 lg:w-24 object-cover rounded-lg md:rounded-xl border-2 border-gray-200">
                                        @else
                                            <div class="h-14 w-14 sm:h-18 sm:w-18 md:h-20 md:w-20 lg:h-24 lg:w-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-lg md:rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 sm:w-6 sm:h-6 md:w-8 md:h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0 pr-2">
                                        <h3 class="text-sm sm:text-base md:text-lg font-bold text-gray-900 mb-0.5 sm:mb-1 truncate">{{ $product->name }}</h3>
                                        <p class="text-xs text-gray-500 mb-1 hidden sm:block">{{ $product->brand ?? 'Brand' }}</p>
                                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                            <span class="text-sm sm:text-base md:text-lg lg:text-2xl font-bold text-green-600">RM {{ number_format($product->price, 2) }}</span>
                                            @if($product->old_price)
                                                <span class="text-xs sm:text-sm md:text-base lg:text-lg text-gray-400 line-through hidden sm:inline">RM {{ number_format($product->old_price, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-1.5 sm:gap-2 md:gap-3">
                                        <div class="flex items-center border-2 border-gray-200 rounded-lg md:rounded-xl overflow-hidden">
                                            <button type="button" onclick="updateQuantity({{ $product->id }}, -1)" 
                                                    class="px-2 sm:px-2.5 md:px-3 py-1.5 sm:py-2 bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 md:w-4 md:h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <input type="number" name="quantities[{{ $product->id }}]" 
                                                   id="qty-{{ $product->id }}"
                                                   value="{{ $qty }}" min="1" 
                                                   class="w-9 sm:w-12 md:w-14 lg:w-16 text-center border-0 focus:ring-0 text-xs sm:text-sm md:text-base lg:text-lg font-semibold">
                                            <button type="button" onclick="updateQuantity({{ $product->id }}, 1)" 
                                                    class="px-2 sm:px-2.5 md:px-3 py-1.5 sm:py-2 bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 md:w-4 md:h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Subtotal -->
                                    <div class="text-right hidden md:block min-w-[80px]">
                                        <div class="text-sm md:text-base lg:text-lg xl:text-xl font-bold text-gray-900">RM {{ number_format($subtotal, 2) }}</div>
                                        <div class="text-xs md:text-sm text-gray-500">{{ $qty }} × RM {{ number_format($product->price, 2) }}</div>
                                    </div>
                                    
                                    <!-- Mobile/Tablet Subtotal (shown on mobile and small tablets) -->
                                    <div class="text-right md:hidden min-w-[60px]">
                                        <div class="text-sm sm:text-base font-bold text-gray-900">RM {{ number_format($subtotal, 2) }}</div>
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <div class="flex-shrink-0">
                                        <button type="button" 
                                                onclick="removeItem({{ $product->id }})"
                                                class="p-1 sm:p-1.5 md:p-2 text-red-500 hover:bg-red-50 rounded-full transition-colors">
                                            <svg class="w-4 h-4 sm:w-4.5 sm:h-4.5 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Update Cart Button -->
                        <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-2.5 sm:py-3 rounded-lg sm:rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg text-sm sm:text-base">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Update Cart
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden sticky top-8" data-tutorial="order-summary">
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Order Summary
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <!-- Summary Items -->
                        <div class="space-y-3">
                            @foreach($products as $product)
                                @php $qty = $cart[$product->id]; $subtotal = $qty * $product->price; @endphp
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-medium text-gray-900 truncate">{{ $product->name }}</div>
                                        <div class="text-gray-500">Qty: {{ $qty }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900">RM {{ number_format($subtotal, 2) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Divider -->
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>RM {{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                        
                        <!-- Checkout Button -->
                        <a href="{{ route('cart.checkout') }}" 
                           class="block w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-xl font-bold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg text-lg text-center"
                           data-tutorial="checkout-button">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Proceed to Checkout
                            </div>
                        </a>
                        
                        <!-- Security Notice -->
                        <div class="text-center pt-4">
                            <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Secure Payment with Stripe
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function updateQuantity(productId, change) {
    const input = document.getElementById('qty-' + productId);
    const newValue = Math.max(1, parseInt(input.value) + change);
    input.value = newValue;
}

function removeItem(productId) {
    if (confirm('Are you sure you want to remove this item?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("cart.remove", absolute: false) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const productIdInput = document.createElement('input');
        productIdInput.type = 'hidden';
        productIdInput.name = 'product_id';
        productIdInput.value = productId;
        
        form.appendChild(csrfToken);
        form.appendChild(productIdInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-submit form when quantity changes
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('input[name^="quantities"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Add a small delay to allow the user to finish typing
            setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    });
});
</script>

@if(isset($showTutorial) && $showTutorial)
    @push('scripts')
    <script>
    (function() {
        'use strict';
        
        function initCartTutorial() {
            if (typeof introJs === 'undefined') {
                console.error('Intro.js library not loaded');
                return;
            }
            
            function elementExists(selector) {
                const element = document.querySelector(selector);
                if (!element) return false;
                const rect = element.getBoundingClientRect();
                const style = window.getComputedStyle(element);
                return rect.width > 0 && rect.height > 0 && style.display !== 'none' && style.visibility !== 'hidden';
            }
            
            const steps = [
                {
                    element: '[data-tutorial="cart-items"]',
                    intro: '<div style="text-align: center;"><h3 style="margin: 0 0 10px 0; font-size: 20px; font-weight: 600; color: #1f2937;">🛒 Your Cart Items</h3><p style="margin: 0; color: #6b7280; line-height: 1.6;">Here are all the items in your cart. You can update quantities or remove items. Click "Update Cart" to save changes.</p></div>',
                    position: 'bottom'
                },
                {
                    element: '[data-tutorial="order-summary"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">💰 Order Summary</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Review your order total here. This shows the breakdown of all items and the final amount you\'ll pay.</p></div>',
                    position: 'left'
                },
                {
                    element: '[data-tutorial="checkout-button"]',
                    intro: '<div><h4 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">💳 Proceed to Checkout</h4><p style="margin: 0; color: #6b7280; line-height: 1.6;">Ready to purchase? Click this button to proceed to secure checkout. We use Stripe for safe and secure payments.</p></div>',
                    position: 'top'
                }
            ];
            
            const validSteps = steps.filter(step => elementExists(step.element));
            
            if (validSteps.length === 0) return;
            
            const intro = introJs();
            intro.setOptions({
                steps: validSteps,
                showProgress: true,
                showBullets: true,
                exitOnOverlayClick: true, // Allow clicking outside to exit
                exitOnEsc: true,
                keyboardNavigation: true,
                disableInteraction: false, // Allow interactions
                scrollToElement: true,
                scrollPadding: 20,
                nextLabel: 'Next →',
                prevLabel: '← Previous',
                skipLabel: 'Skip',
                doneLabel: 'Got it! 🎉',
                tooltipClass: 'customTooltip',
                highlightClass: 'customHighlight',
                buttonClass: 'introjs-button',
                tooltipPosition: 'auto' // Let intro.js decide the best position
            });
            
            // Ensure tooltip is visible after each step and style properly
            intro.onchange(function(targetElement) {
                setTimeout(function() {
                    const tooltip = document.querySelector('.introjs-tooltip');
                    if (tooltip) {
                        tooltip.style.display = 'block';
                        tooltip.style.visibility = 'visible';
                        tooltip.style.opacity = '1';
                        tooltip.style.zIndex = '999999';
                        
                        // Ensure header has gradient and white text
                        const header = tooltip.querySelector('.introjs-tooltip-header');
                        if (header) {
                            header.style.background = 'linear-gradient(135deg, #3b82f6 0%, #10b981 100%)';
                            header.style.color = 'white';
                            const headerText = header.querySelector('h3, h4');
                            if (headerText) {
                                headerText.style.color = 'white';
                            }
                        }
                        
                        // Ensure skip button is styled as button
                        const skipButton = tooltip.querySelector('.introjs-skipbutton');
                        if (skipButton) {
                            skipButton.style.color = 'white';
                            skipButton.style.background = 'rgba(255, 255, 255, 0.2)';
                            skipButton.style.border = '1px solid rgba(255, 255, 255, 0.3)';
                            skipButton.style.padding = '8px 16px';
                            skipButton.style.borderRadius = '8px';
                            skipButton.style.fontWeight = '500';
                        }
                        
                        // Also ensure content is visible
                        const content = tooltip.querySelector('.introjs-tooltipcontent');
                        if (content) {
                            content.style.display = 'block';
                            content.style.visibility = 'visible';
                            content.style.opacity = '1';
                        }
                    }
                }, 100);
            });
            
            // Debug: Log when tutorial starts
            intro.onstart(function() {
                console.log('Cart tutorial started with', validSteps.length, 'steps');
            });
            
            // Ensure tutorial can be exited
            intro.onexit(function() {
                console.log('Tutorial exited');
            });
            
            setTimeout(() => intro.start(), 800);
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCartTutorial);
        } else {
            setTimeout(initCartTutorial, 100);
        }
    })();
    </script>
    <style>
    /* Professional Tutorial Styling - Matching First Image Style */
    .customTooltip {
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2) !important;
        border: none !important;
        max-width: 400px !important;
        padding: 0 !important;
        background: white !important;
        overflow: hidden !important;
    }

    /* Gradient Header like first image */
    .introjs-tooltip-header {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%) !important;
        padding: 20px 20px 16px 20px !important;
        border-bottom: none !important;
        color: white !important;
        position: relative !important;
    }

    .introjs-tooltip-header h3,
    .introjs-tooltip-header h4 {
        color: white !important;
        margin: 0 !important;
        font-weight: 600 !important;
    }

    .introjs-tooltipcontent {
        padding: 20px !important;
        font-size: 14px !important;
        line-height: 1.6 !important;
        color: #374151 !important;
        background: white !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .introjs-tooltipbuttons {
        padding: 16px 20px 20px 20px !important;
        border-top: 1px solid #e5e7eb !important;
        text-align: center !important;
        background: white !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 12px !important;
    }

    .introjs-tooltip {
        z-index: 999999 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: absolute !important;
        max-width: 400px !important;
        min-width: 300px !important;
    }

    .introjs-tooltip * {
        visibility: visible !important;
    }

    .customHighlight {
        border-radius: 12px !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3) !important;
    }

    /* Skip button styled like Next/Previous buttons */
    .introjs-skipbutton {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        z-index: 10 !important;
        margin: 0 !important;
        color: white !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        transition: all 0.2s ease !important;
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        cursor: pointer !important;
        backdrop-filter: blur(4px) !important;
    }

    .introjs-skipbutton:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        border-color: rgba(255, 255, 255, 0.5) !important;
        transform: translateY(-1px) !important;
    }

    /* Button styling */
    .introjs-button {
        border-radius: 8px !important;
        padding: 10px 20px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
        border: none !important;
        cursor: pointer !important;
    }

    .introjs-button.introjs-prevbutton {
        background: #f3f4f6 !important;
        color: #374151 !important;
        border: 1px solid #e5e7eb !important;
        flex: 1 !important;
        max-width: 48% !important;
    }

    .introjs-button.introjs-prevbutton:hover {
        background: #e5e7eb !important;
        border-color: #d1d5db !important;
    }

    .introjs-button.introjs-nextbutton {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
        border: none !important;
        flex: 1 !important;
        max-width: 48% !important;
    }

    .introjs-button.introjs-nextbutton:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;
    }
    </style>
    @endpush
@endif

@endsection 