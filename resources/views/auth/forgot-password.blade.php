<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - SmashZone</title>
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-gradient-to-br from-green-50 via-blue-50 to-purple-50"></div>
    
    <!-- Navigation -->
    <nav class="relative z-10 bg-white/80 backdrop-blur-sm shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">SmashZone</span>
                </a>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600 font-medium transition-colors">
                    Back to Sign in
                </a>
            </div>
        </div>
    </nav>

    <div class="relative z-10 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center bounce-in">
                <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
                <p class="text-gray-600">No problem! Enter your email and we'll send you a reset link</p>
            </div>

            <!-- Forgot Password Form -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 card-shadow">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-green-800 font-medium">{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                            Email Address *
                        </label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-colors text-lg"
                               placeholder="Enter your email address">
                        @error('email')
                            <div class="text-red-500 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 rounded-xl font-bold text-lg hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Send Password Reset Link
                        </div>
                    </button>
                </form>
            </div>

            <!-- Back to Login Link -->
            <div class="text-center">
                <p class="text-gray-600">
                    Remember your password? 
                    <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-semibold">
                        Sign in here
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
