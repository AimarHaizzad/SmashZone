@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
        <p class="text-gray-600">Complete your order by selecting delivery method</p>
    </div>

    <form action="{{ route('stripe.checkout') }}" method="POST" id="checkout-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Delivery Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Method Selection -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        Delivery Method
                    </h2>

                    <div class="space-y-4">
                        <!-- Pickup Option -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 transition-colors delivery-option">
                            <input type="radio" name="delivery_method" value="pickup" class="mt-1 mr-4 w-5 h-5 text-blue-600" checked>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="font-semibold text-gray-900">Self Pickup</span>
                                </div>
                                <p class="text-sm text-gray-600">Pick up your order from our store location</p>
                                <p class="text-xs text-gray-500 mt-1">Free • Available immediately after payment</p>
                            </div>
                        </label>

                        <!-- Delivery Option -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-500 transition-colors delivery-option">
                            <input type="radio" name="delivery_method" value="delivery" class="mt-1 mr-4 w-5 h-5 text-blue-600" id="delivery-radio">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.35 2.7A2 2 0 007.52 19h8.96a2 2 0 001.87-2.3L17 13M7 13V6a1 1 0 011-1h5a1 1 0 011 1v7" />
                                    </svg>
                                    <span class="font-semibold text-gray-900">Home Delivery</span>
                                </div>
                                <p class="text-sm text-gray-600">We'll deliver your order to your address</p>
                                <p class="text-xs text-gray-500 mt-1">Estimated 2-3 business days</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Delivery Address Form (Hidden by default) -->
                <div id="delivery-address-form" class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 hidden">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Delivery Address
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-2">
                                Street Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="delivery_address" name="delivery_address" rows="3" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Enter your full address"></textarea>
                            @error('delivery_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="delivery_city" class="block text-sm font-medium text-gray-700 mb-2">
                                    City <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="delivery_city" name="delivery_city" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., Kuala Lumpur">
                                @error('delivery_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="delivery_postcode" class="block text-sm font-medium text-gray-700 mb-2">
                                    Postcode <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="delivery_postcode" name="delivery_postcode" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 50000">
                                @error('delivery_postcode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="delivery_state" class="block text-sm font-medium text-gray-700 mb-2">
                                    State <span class="text-red-500">*</span>
                                </label>
                                <select id="delivery_state" name="delivery_state" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select State</option>
                                    <option value="Johor">Johor</option>
                                    <option value="Kedah">Kedah</option>
                                    <option value="Kelantan">Kelantan</option>
                                    <option value="Kuala Lumpur">Kuala Lumpur</option>
                                    <option value="Labuan">Labuan</option>
                                    <option value="Malacca">Malacca</option>
                                    <option value="Negeri Sembilan">Negeri Sembilan</option>
                                    <option value="Pahang">Pahang</option>
                                    <option value="Penang">Penang</option>
                                    <option value="Perak">Perak</option>
                                    <option value="Perlis">Perlis</option>
                                    <option value="Putrajaya">Putrajaya</option>
                                    <option value="Sabah">Sabah</option>
                                    <option value="Sarawak">Sarawak</option>
                                    <option value="Selangor">Selangor</option>
                                    <option value="Terengganu">Terengganu</option>
                                </select>
                                @error('delivery_state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="delivery_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="delivery_phone" name="delivery_phone" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="e.g., 0123456789">
                                @error('delivery_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Additional Notes (Optional)
                    </h2>
                    <textarea name="notes" rows="3" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Any special instructions for your order..."></textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sticky top-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-4">
                        @foreach($products as $product)
                            @php $qty = $cart[$product->id]; $subtotal = $qty * $product->price; @endphp
                            <div class="flex justify-between items-start text-sm">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-gray-500">Qty: {{ $qty }}</div>
                                </div>
                                <div class="text-right font-semibold text-gray-900">RM {{ number_format($subtotal, 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4 mb-4">
                        <div class="flex justify-between items-center text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>RM {{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-xl font-bold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Proceed to Payment
                        </div>
                    </button>

                    <div class="text-center mt-4">
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
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryRadio = document.getElementById('delivery-radio');
    const addressForm = document.getElementById('delivery-address-form');
    const deliveryOptions = document.querySelectorAll('input[name="delivery_method"]');

    function toggleAddressForm() {
        if (deliveryRadio.checked) {
            addressForm.classList.remove('hidden');
            // Make delivery address fields required
            document.getElementById('delivery_address').required = true;
            document.getElementById('delivery_city').required = true;
            document.getElementById('delivery_postcode').required = true;
            document.getElementById('delivery_state').required = true;
            document.getElementById('delivery_phone').required = true;
        } else {
            addressForm.classList.add('hidden');
            // Remove required attribute
            document.getElementById('delivery_address').required = false;
            document.getElementById('delivery_city').required = false;
            document.getElementById('delivery_postcode').required = false;
            document.getElementById('delivery_state').required = false;
            document.getElementById('delivery_phone').required = false;
        }
    }

    deliveryOptions.forEach(option => {
        option.addEventListener('change', toggleAddressForm);
    });

    // Initial check
    toggleAddressForm();
});
</script>
@endsection

