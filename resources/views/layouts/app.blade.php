<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA Meta Tags -->
        <meta name="application-name" content="SmashZone">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="SmashZone">
        <meta name="description" content="Book badminton courts easily with SmashZone">
        <meta name="format-detection" content="telephone=no">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#10b981">

        <!-- PWA Manifest -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- Apple Touch Icons -->
        <link rel="apple-touch-icon" href="{{ asset('images/icon-192x192.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex flex-col">
                    @include('layouts.navigation')
                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset
            <main class="flex-1">
                @yield('content')
                    </main>
        </div>

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function(registration) {
                            console.log('ServiceWorker registration successful');
                        })
                        .catch(function(err) {
                            console.log('ServiceWorker registration failed');
                        });
                });
            }
        </script>

        <!-- 🏸 SmashZone Mobile App Authentication Integration -->
        <script>
        (function() {
            'use strict';
            
            console.log('🏸 SmashZone Laravel Web Integration loaded');
            
            // Function to check URL parameters for mobile app authentication
            function checkUrlParameters() {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get('user_id');
                const username = urlParams.get('username');
                const userEmail = urlParams.get('user_email');
                const userName = urlParams.get('user_name');
                const authToken = urlParams.get('auth_token');
                const isAuthenticated = urlParams.get('authenticated') === 'true';
                
                if (isAuthenticated && userId && authToken) {
                    console.log('🔐 Mobile app authentication detected via URL:', {
                        userId: userId,
                        username: username,
                        email: userEmail,
                        name: userName,
                        token: authToken
                    });
                    
                    // Store in localStorage for future use
                    localStorage.setItem('user_id', userId);
                    localStorage.setItem('username', username);
                    localStorage.setItem('user_email', userEmail);
                    localStorage.setItem('user_name', userName);
                    localStorage.setItem('auth_token', authToken);
                    localStorage.setItem('is_authenticated', 'true');
                    
                    // Set up Laravel Sanctum authentication
                    setupLaravelAuthentication({
                        id: userId,
                        username: username,
                        email: userEmail,
                        name: userName,
                        token: authToken
                    });
                    
                    // Clean URL parameters
                    const cleanUrl = window.location.pathname;
                    window.history.replaceState({}, document.title, cleanUrl);
                    
                    return true;
                }
                
                return false;
            }
            
            // Function to check if user is authenticated from mobile app
            function checkMobileAuthentication() {
                // First check URL parameters
                if (checkUrlParameters()) {
                    return true;
                }
                
                // Then check localStorage
                const isAuthenticated = localStorage.getItem('is_authenticated') === 'true';
                const userId = localStorage.getItem('user_id');
                const username = localStorage.getItem('username');
                const userEmail = localStorage.getItem('user_email');
                const userName = localStorage.getItem('user_name');
                const authToken = localStorage.getItem('auth_token');
                
                if (isAuthenticated && userId && authToken) {
                    console.log('🔐 Mobile app authentication detected from localStorage:', {
                        userId: userId,
                        username: username,
                        email: userEmail,
                        name: userName,
                        token: authToken
                    });
                    
                    // Set up Laravel Sanctum authentication
                    setupLaravelAuthentication({
                        id: userId,
                        username: username,
                        email: userEmail,
                        name: userName,
                        token: authToken
                    });
                    
                    return true;
                }
                
                return false;
            }
            
            // Function to set up Laravel authentication
            function setupLaravelAuthentication(userData) {
                // Set up Axios with Sanctum token
                if (typeof window.axios !== 'undefined') {
                    window.axios.defaults.headers.common['Authorization'] = `Bearer ${userData.token}`;
                    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
                }
                
                // Set up fetch with Sanctum token
                const originalFetch = window.fetch;
                window.fetch = function(url, options = {}) {
                    options.headers = options.headers || {};
                    options.headers['Authorization'] = `Bearer ${userData.token}`;
                    options.headers['X-Requested-With'] = 'XMLHttpRequest';
                    return originalFetch(url, options);
                };
                
                // Set global user data
                window.authenticatedUser = userData;
                window.isAuthenticated = true;
                
                // Dispatch custom event
                window.dispatchEvent(new CustomEvent('laravelUserAuthenticated', {
                    detail: userData
                }));
                
                console.log('✅ Laravel user authenticated:', userData);
            }
            
            // Listen for authentication event from mobile app
            window.addEventListener('userAuthenticated', function(event) {
                console.log('📱 Received authentication from mobile app:', event.detail);
                setupLaravelAuthentication(event.detail);
            });
            
            // Check for existing authentication on page load
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🔍 Checking for mobile app authentication...');
                
                setTimeout(function() {
                    if (checkMobileAuthentication()) {
                        console.log('✅ Mobile app user is authenticated');
                        
                        // Hide login form
                        const loginForm = document.querySelector('#login-form, .login-form, [data-login-form]');
                        if (loginForm) {
                            loginForm.style.display = 'none';
                        }
                        
                        // Show authenticated content
                        const authenticatedContent = document.querySelector('#authenticated-content, .authenticated-content, [data-authenticated-content]');
                        if (authenticatedContent) {
                            authenticatedContent.style.display = 'block';
                        }
                        
                        // Redirect to dashboard if on login page
                        const currentPath = window.location.pathname;
                        if (currentPath.includes('login') || currentPath.includes('auth')) {
                            window.location.href = '/dashboard';
                        }
                        
                        // Update page to show authenticated state
                        updatePageForAuthenticatedUser();
                    } else {
                        console.log('❌ No mobile app authentication found');
                    }
                }, 1000);
            });
            
            // Function to update page for authenticated user
            function updatePageForAuthenticatedUser() {
                const user = window.getAuthenticatedUser();
                
                // Update user name in header/navbar
                const userNameElements = document.querySelectorAll('[data-user-name]');
                userNameElements.forEach(element => {
                    element.textContent = user.name;
                });
                
                // Update user email
                const userEmailElements = document.querySelectorAll('[data-user-email]');
                userEmailElements.forEach(element => {
                    element.textContent = user.email;
                });
                
                // Show/hide elements based on authentication
                const authElements = document.querySelectorAll('[data-auth-required]');
                authElements.forEach(element => {
                    element.style.display = 'block';
                });
                
                const guestElements = document.querySelectorAll('[data-guest-only]');
                guestElements.forEach(element => {
                    element.style.display = 'none';
                });
            }
            
            // Function to get current authenticated user
            window.getAuthenticatedUser = function() {
                return {
                    id: localStorage.getItem('user_id'),
                    username: localStorage.getItem('username'),
                    email: localStorage.getItem('user_email'),
                    name: localStorage.getItem('user_name'),
                    token: localStorage.getItem('auth_token'),
                    isAuthenticated: localStorage.getItem('is_authenticated') === 'true'
                };
            };
            
            // Function to logout
            window.logoutMobileUser = function() {
                // Clear localStorage
                localStorage.removeItem('user_id');
                localStorage.removeItem('username');
                localStorage.removeItem('user_email');
                localStorage.removeItem('user_name');
                localStorage.removeItem('auth_token');
                localStorage.removeItem('is_authenticated');
                
                // Clear sessionStorage
                sessionStorage.removeItem('user_id');
                sessionStorage.removeItem('username');
                sessionStorage.removeItem('user_email');
                sessionStorage.removeItem('user_name');
                sessionStorage.removeItem('auth_token');
                sessionStorage.removeItem('is_authenticated');
                
                // Clear global variables
                window.authenticatedUser = null;
                window.isAuthenticated = false;
                
                // Clear Axios headers
                if (typeof window.axios !== 'undefined') {
                    delete window.axios.defaults.headers.common['Authorization'];
                }
                
                console.log('🚪 Mobile app user logged out');
                
                // Redirect to login page
                window.location.href = '/login';
            };
            
        })();
        </script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        @stack('scripts')
    </body>
</html>
