<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - SmashZone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://js.stripe.com/v3/"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .hero-gradient { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #06b6d4 100%); }
        .card-shadow { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .floating { animation: floating 3s ease-in-out infinite; }
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .bounce-in { animation: bounceIn 1s ease-out; }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-gradient-to-br from-green-50 via-blue-50 to-purple-50"></div>
    
    <!-- Navigation -->
    <nav class="relative z-10 bg-white/80 backdrop-blur-sm shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">SmashZone</span>
                </a>
                <a href="{{ route('bookings.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition-colors">
                    ← Back to Bookings
                </a>
            </div>
        </div>
    </nav>

    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center bounce-in">
                <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Complete Your Payment</h2>
                <p class="text-gray-600">Secure payment for your court booking</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Booking Details -->
                @php 
                    $bookings = $payment->bookings;
                    if ($bookings->isEmpty() && $payment->booking) {
                        $bookings = collect([$payment->booking]);
                    }
                @endphp
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 card-shadow">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">
                            Booking Summary {{ $bookings->count() ? '(' . $bookings->count() . ' slot' . ($bookings->count() > 1 ? 's' : '') . ')' : '' }}
                        </h3>
                    </div>

                    <div class="space-y-4">
                        @forelse($bookings as $bookingItem)
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div>
                                    <p class="text-sm text-gray-600">Court</p>
                                    <p class="font-semibold text-gray-900">{{ $bookingItem->court->name ?? 'Court ' . $bookingItem->court_id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($bookingItem->date)->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Time</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingItem->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingItem->end_time)->format('g:i A') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No booking details available.</p>
                        @endforelse

                        <div class="flex justify-between items-center p-4 bg-gradient-to-r from-green-50 to-blue-50 rounded-xl border border-green-200">
                            <div>
                                <p class="text-sm text-gray-600">Total Amount</p>
                                <p class="text-2xl font-bold text-green-600">RM {{ number_format($payment->amount, 2) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 card-shadow">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Payment Method</h3>
                    </div>

                    <!-- Security Notice -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="text-sm font-semibold text-blue-800">Secure Payment</span>
                        </div>
                        <p class="text-sm text-blue-700 mt-1">Your payment is secured by Stripe with bank-level encryption</p>
                    </div>

                    <!-- Payment Methods -->
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl hover:border-blue-500 transition-colors cursor-pointer payment-method" data-method="card">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">Credit Card</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 border-2 border-gray-200 rounded-xl hover:border-green-500 transition-colors cursor-pointer payment-method" data-method="fpx">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-gray-800">FPX Banking</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pay Button -->
                    <button id="pay-button" 
                            class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Pay RM {{ number_format($payment->amount, 2) }}
                        </div>
                    </button>

                    <!-- Loading State -->
                    <div id="loading" class="hidden text-center py-4">
                        <div class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-600">Processing payment...</span>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div id="error-message" class="hidden mt-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold text-red-800">Payment Error</span>
                        </div>
                        <p id="error-text" class="text-sm text-red-700 mt-1"></p>
                    </div>
                </div>
            </div>

            <!-- Payment Security Info -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Bank-Level Security</h4>
                            <p class="text-sm text-gray-600">256-bit SSL encryption</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">PCI Compliant</h4>
                            <p class="text-sm text-gray-600">Industry standard security</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Instant Confirmation</h4>
                            <p class="text-sm text-gray-600">Get confirmation immediately</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Stripe
        const stripe = Stripe('{{ config("services.stripe.key") }}');
        
        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Remove active class from all methods
                document.querySelectorAll('.payment-method').forEach(m => {
                    m.classList.remove('border-blue-500', 'border-green-500');
                    m.classList.add('border-gray-200');
                });
                
                // Add active class to selected method
                this.classList.remove('border-gray-200');
                if (this.dataset.method === 'card') {
                    this.classList.add('border-blue-500');
                } else {
                    this.classList.add('border-green-500');
                }
            });
        });

        // Handle payment
        document.getElementById('pay-button').addEventListener('click', async function() {
            const button = this;
            const loading = document.getElementById('loading');
            const errorMessage = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');

            console.log('Pay button clicked!'); // Debug log

            // Show loading state
            button.disabled = true;
            loading.classList.remove('hidden');
            errorMessage.classList.add('hidden');

            try {
                console.log('Creating payment session...'); // Debug log
                
                // Create payment session
                const response = await fetch('{{ route("payments.process", $payment) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                console.log('Response status:', response.status); // Debug log

                const data = await response.json();
                console.log('Response data:', data); // Debug log

                if (data.error) {
                    throw new Error(data.error);
                }

                console.log('Redirecting to Stripe...'); // Debug log

                // Redirect to Stripe Checkout
                const result = await stripe.redirectToCheckout({
                    sessionId: data.session_id
                });

                if (result.error) {
                    throw new Error(result.error.message);
                }

            } catch (error) {
                console.error('Payment error:', error); // Debug log
                
                // Show error message
                errorText.textContent = error.message || 'An error occurred while processing your payment. Please try again.';
                errorMessage.classList.remove('hidden');
                
                // Reset button state
                button.disabled = false;
                loading.classList.add('hidden');
            }
        });
    </script>
</body>
</html> 