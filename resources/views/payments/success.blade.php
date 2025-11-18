<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - SmashZone</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #10b981;
            animation: confetti-fall 3s linear infinite;
        }
        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-gradient-to-br from-green-50 via-blue-50 to-purple-50"></div>
    
    <!-- Confetti Animation -->
    <div id="confetti-container"></div>
    
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
                    View My Bookings
                </a>
            </div>
        </div>
    </nav>

    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Success Header -->
            <div class="text-center bounce-in">
                <div class="w-24 h-24 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 floating">
                    <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
                <p class="text-xl text-gray-600">Your court booking has been confirmed</p>
            </div>

            <!-- Success Card -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 card-shadow">
                <!-- Success Message -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h2>
                    <p class="text-gray-600">Thank you for your payment. Your booking is now active.</p>
                </div>

                <!-- Booking Details -->
                <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-6 mb-6 border border-green-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                        </svg>
                        Booking Details
                    </h3>
                    
                    @foreach($payment->bookings as $bookingItem)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Court</p>
                                    <p class="font-semibold text-gray-900">{{ $bookingItem->court->name ?? 'Court ' . $bookingItem->court_id }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($bookingItem->date)->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-3 bg-white rounded-xl md:col-span-2">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Time</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingItem->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingItem->end_time)->format('g:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Amount Paid</p>
                            <p class="font-semibold text-gray-900">RM {{ number_format($payment->amount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Payment Information
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Payment ID:</span>
                            <span class="font-semibold text-gray-900">#{{ $payment->id }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Payment Method:</span>
                            <span class="font-semibold text-gray-900">{{ $payment->payment_method }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Payment Date:</span>
                            <span class="font-semibold text-gray-900">{{ $payment->payment_date ? $payment->payment_date->format('M j, Y g:i A') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Status:</span>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $payment->status_badge_class }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-blue-50 rounded-2xl p-6 mb-6 border border-blue-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        What's Next?
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
                                <span class="text-xs font-bold text-blue-600">1</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Arrive on time</p>
                                <p class="text-sm text-gray-600">Please arrive 10 minutes before your booking time</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
                                <span class="text-xs font-bold text-blue-600">2</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Bring your own equipment</p>
                                <p class="text-sm text-gray-600">Rackets and shuttlecocks are not provided</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mt-0.5">
                                <span class="text-xs font-bold text-blue-600">3</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">Check in at reception</p>
                                <p class="text-sm text-gray-600">Show your booking confirmation to staff</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('bookings.index') }}" 
                       class="flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6" />
                        </svg>
                        View My Bookings
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:border-gray-400 hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Need Help?</h3>
                    <p class="text-gray-600 mb-4">If you have any questions about your booking, please contact us</p>
                    <div class="flex items-center justify-center gap-6">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm text-gray-600">+60 12-345 6789</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-600">support@smashzone.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Create confetti animation
        function createConfetti() {
            const colors = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'];
            const container = document.getElementById('confetti-container');
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                container.appendChild(confetti);
                
                // Remove confetti after animation
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
        }
        
        // Start confetti animation
        createConfetti();
        setInterval(createConfetti, 3000);
    </script>
</body>
</html> 