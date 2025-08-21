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
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 px-6 py-4 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                            </svg>
                            Cart Items ({{ count($cart) }})
                        </h2>
                    </div>
                    
                    <form action="{{ route('cart.update') }}" method="POST" class="p-6">
                        @csrf
                        <div class="space-y-6">
                            @php $total = 0; @endphp
                            @foreach($products as $product)
                                @php $qty = $cart[$product->id]; $subtotal = $qty * $product->price; $total += $subtotal; @endphp
                                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="h-20 w-20 object-cover rounded-xl border-2 border-gray-200">
                                        @else
                                            <div class="h-20 w-20 bg-gradient-to-br from-gray-200 to-gray-300 rounded-xl flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $product->name }}</h3>
                                        <p class="text-sm text-gray-500 mb-2">{{ $product->brand ?? 'Brand' }}</p>
                                        <div class="flex items-center gap-2">
                                            <span class="text-2xl font-bold text-green-600">RM {{ number_format($product->price, 2) }}</span>
                                            @if($product->old_price)
                                                <span class="text-lg text-gray-400 line-through">RM {{ number_format($product->old_price, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center border-2 border-gray-200 rounded-xl overflow-hidden">
                                            <button type="button" onclick="updateQuantity({{ $product->id }}, -1)" 
                                                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <input type="number" name="quantities[{{ $product->id }}]" 
                                                   id="qty-{{ $product->id }}"
                                                   value="{{ $qty }}" min="1" 
                                                   class="w-16 text-center border-0 focus:ring-0 text-lg font-semibold">
                                            <button type="button" onclick="updateQuantity({{ $product->id }}, 1)" 
                                                    class="px-3 py-2 bg-gray-100 hover:bg-gray-200 transition-colors">
                                                <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Subtotal -->
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900">RM {{ number_format($subtotal, 2) }}</div>
                                        <div class="text-sm text-gray-500">{{ $qty }} × RM {{ number_format($product->price, 2) }}</div>
                                    </div>
                                    
                                    <!-- Remove Button -->
                                    <div class="flex-shrink-0">
                                        <button type="button" 
                                                onclick="removeItem({{ $product->id }})"
                                                class="p-2 text-red-500 hover:bg-red-50 rounded-full transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Update Cart Button -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105 shadow-lg">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden sticky top-8">
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
                        <form action="{{ route('cart.checkout') }}" method="POST" class="pt-4">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-xl font-bold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg text-lg">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Proceed to Checkout
                                </div>
                            </button>
                        </form>
                        
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
        form.action = '{{ route("cart.remove") }}';
        
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
@endsection 