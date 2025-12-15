<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmashZone - Professional Badminton Court Booking & Equipment</title>
    <meta name="description" content="Book premium badminton courts, shop professional equipment, and join the ultimate badminton community at SmashZone. Your one-stop destination for everything badminton.">
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
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="text-2xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent">SmashZone</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#home" class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Home</a>
                        <a href="#features" class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Features</a>
                        <a href="#about" class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">About</a>
                        <a href="#contact" class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Contact</a>
                    </div>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login', absolute: false) }}" class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Login</a>
                    <a href="{{ route('register', absolute: false) }}" class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg">Get Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="pt-16">
        <div class="hero-gradient min-h-screen flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="text-white space-y-8 bounce-in">
                        <div class="space-y-4">
                            <h1 class="text-5xl lg:text-6xl font-bold leading-tight">
                                Welcome to
                                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-300">SmashZone</span>
                            </h1>
                            <p class="text-xl lg:text-2xl text-blue-100 font-medium">
                                Your Ultimate Badminton Destination
                            </p>
                        </div>
                        
                        <p class="text-lg text-blue-100 leading-relaxed">
                            Book premium badminton courts, shop professional equipment, and join a community of passionate players. 
                            Experience the perfect blend of convenience and excellence in badminton.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('register', absolute: false) }}" 
                               class="bg-white text-green-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg text-center">
                                Start Playing Today
                            </a>
                            <a href="#features" 
                               class="border-2 border-white text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-green-600 transition-all transform hover:scale-105 text-center">
                                Learn More
                            </a>
                        </div>

                        <!-- Social Media Icons -->
                        <div class="flex space-x-4 pt-8">
                            <a href="#" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white/30 transition-colors">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right Illustration -->
                    <div class="relative floating">
                        <div class="bg-white/10 backdrop-blur-sm rounded-3xl p-8 card-shadow">
                            <!-- Badminton Players Illustration -->
                            <div class="relative">
                                <!-- Court Background -->
                                <div class="w-full h-64 bg-gradient-to-b from-green-400 to-green-600 rounded-2xl relative overflow-hidden">
                                    <!-- Court Lines -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-32 h-24 border-2 border-white rounded-lg relative">
                                            <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-white"></div>
                                            <div class="absolute top-1/4 left-1/2 w-0.5 h-1/2 bg-white"></div>
                                            <div class="absolute bottom-1/4 left-1/2 w-0.5 h-1/2 bg-white"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Net -->
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-1 h-16 bg-white"></div>
                                    
                                    <!-- Player 1 (Foreground) -->
                                    <div class="absolute bottom-4 left-8">
                                        <div class="w-16 h-20 bg-purple-500 rounded-full relative">
                                            <!-- Head -->
                                            <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 w-8 h-8 bg-yellow-300 rounded-full"></div>
                                            <!-- Racket -->
                                            <div class="absolute -top-4 -right-2 w-12 h-1 bg-green-500 rounded-full transform rotate-45"></div>
                                            <div class="absolute -top-6 -right-1 w-8 h-8 bg-green-400 rounded-full"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Player 2 (Background) -->
                                    <div class="absolute bottom-4 right-8">
                                        <div class="w-14 h-18 bg-purple-600 rounded-full relative opacity-80">
                                            <!-- Head -->
                                            <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 w-7 h-7 bg-yellow-300 rounded-full"></div>
                                            <!-- Racket -->
                                            <div class="absolute -top-3 -left-2 w-10 h-1 bg-green-500 rounded-full transform -rotate-45"></div>
                                            <div class="absolute -top-5 -left-1 w-6 h-6 bg-green-400 rounded-full"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Shuttlecock -->
                                    <div class="absolute top-1/3 left-1/2 transform -translate-x-1/2 animate-bounce">
                                        <div class="w-4 h-6 bg-white rounded-full relative">
                                            <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 w-6 h-2 bg-orange-400 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Decorative Elements -->
                                <div class="absolute -top-4 -right-4 w-20 h-20 bg-pink-400 rounded-full opacity-30"></div>
                                <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-blue-400 rounded-full opacity-30"></div>
                                <div class="absolute top-1/4 -left-2 w-12 h-12 bg-yellow-400 rounded-full opacity-30"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Why Choose SmashZone?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience the perfect blend of convenience, quality, and community in badminton
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-2xl p-8 card-shadow hover:transform hover:scale-105 transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Easy Court Booking</h3>
                    <p class="text-gray-600">Book your preferred court with just a few clicks. Real-time availability and instant confirmation.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-8 card-shadow hover:transform hover:scale-105 transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Premium Equipment</h3>
                    <p class="text-gray-600">Shop the latest badminton equipment from top brands. Rackets, shoes, and accessories.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-8 card-shadow hover:transform hover:scale-105 transition-all">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Community</h3>
                    <p class="text-gray-600">Join a community of passionate badminton players. Connect, compete, and grow together.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">About SmashZone</h2>
                    <p class="text-lg text-gray-600 mb-6">
                        SmashZone is your ultimate destination for everything badminton. We provide premium court booking services, 
                        professional equipment, and a vibrant community for players of all levels.
                    </p>
                    <p class="text-lg text-gray-600 mb-8">
                        Whether you're a beginner looking to start your badminton journey or a professional player seeking the best facilities, 
                        SmashZone has everything you need to excel in this amazing sport.
                    </p>
                    <a href="{{ route('register', absolute: false) }}" 
                       class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-lg inline-block">
                        Join SmashZone Today
                    </a>
                </div>
                <div class="bg-white rounded-2xl p-8 card-shadow">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600 mb-2">100+</div>
                            <div class="text-gray-600">Courts Available</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600 mb-2">500+</div>
                            <div class="text-gray-600">Happy Players</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600 mb-2">24/7</div>
                            <div class="text-gray-600">Booking Support</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-pink-600 mb-2">50+</div>
                            <div class="text-gray-600">Equipment Items</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-green-600 to-blue-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Start Your Badminton Journey?</h2>
            <p class="text-xl text-green-100 mb-8">
                Join thousands of players who trust SmashZone for their badminton needs
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" 
                   class="bg-white text-green-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg">
                    Create Free Account
                </a>
                <a href="{{ route('login', absolute: false) }}" 
                   class="border-2 border-white text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white hover:text-green-600 transition-all transform hover:scale-105">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold">SmashZone</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Your ultimate destination for badminton courts, equipment, and community.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">About</a></li>
                        <li><a href="{{ route('login', absolute: false) }}" class="text-gray-400 hover:text-white transition-colors">Login</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>Email: info@smashzone.com</li>
                        <li>Phone: +1 (555) 123-4567</li>
                        <li>Address: Badminton Street, Sports City</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 SmashZone. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('bg-white/95', 'backdrop-blur-sm');
            } else {
                nav.classList.remove('bg-white/95', 'backdrop-blur-sm');
            }
        });
    </script>
</body>
</html>