@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">🏸 SmashZone Mobile App Integration Test</h1>
        
        <!-- Authentication Status -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h2 class="text-lg font-semibold mb-3">Authentication Status</h2>
            <div id="auth-status" class="text-sm">
                <p>Checking authentication...</p>
            </div>
        </div>
        
        <!-- User Information -->
        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
            <h2 class="text-lg font-semibold mb-3">User Information</h2>
            <div id="user-info" class="text-sm">
                <p>No user data available</p>
            </div>
        </div>
        
        <!-- Test Buttons -->
        <div class="mb-6 p-4 bg-green-50 rounded-lg">
            <h2 class="text-lg font-semibold mb-3">Test Functions</h2>
            <div class="space-y-2">
                <button onclick="testAuthentication()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Test Authentication
                </button>
                <button onclick="simulateMobileLogin()" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Simulate Mobile Login
                </button>
                <button onclick="logoutMobileUser()" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Logout Mobile User
                </button>
            </div>
        </div>
        
        <!-- Debug Information -->
        <div class="mb-6 p-4 bg-yellow-50 rounded-lg">
            <h2 class="text-lg font-semibold mb-3">Debug Information</h2>
            <div id="debug-info" class="text-sm font-mono">
                <p>Click "Test Authentication" to see debug information</p>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="p-4 bg-purple-50 rounded-lg">
            <h2 class="text-lg font-semibold mb-3">How to Test</h2>
            <ol class="list-decimal list-inside space-y-2 text-sm">
                <li>Open browser developer tools (F12)</li>
                <li>Go to Console tab</li>
                <li>Click "Simulate Mobile Login" to simulate mobile app authentication</li>
                <li>Check console for authentication messages</li>
                <li>Click "Test Authentication" to see current status</li>
                <li>In your Android app, login and then open this page to see real integration</li>
            </ol>
        </div>
    </div>
</div>

<script>
// Test functions for mobile app integration
function testAuthentication() {
    const user = window.getAuthenticatedUser();
    const debugInfo = document.getElementById('debug-info');
    const authStatus = document.getElementById('auth-status');
    const userInfo = document.getElementById('user-info');
    
    debugInfo.innerHTML = `
        <p><strong>localStorage data:</strong></p>
        <p>user_id: ${localStorage.getItem('user_id') || 'null'}</p>
        <p>username: ${localStorage.getItem('username') || 'null'}</p>
        <p>user_email: ${localStorage.getItem('user_email') || 'null'}</p>
        <p>user_name: ${localStorage.getItem('user_name') || 'null'}</p>
        <p>auth_token: ${localStorage.getItem('auth_token') ? 'Present' : 'null'}</p>
        <p>is_authenticated: ${localStorage.getItem('is_authenticated') || 'null'}</p>
        <p><strong>Global variables:</strong></p>
        <p>window.authenticatedUser: ${window.authenticatedUser ? 'Present' : 'null'}</p>
        <p>window.isAuthenticated: ${window.isAuthenticated || 'false'}</p>
    `;
    
    if (user.isAuthenticated) {
        authStatus.innerHTML = '<p class="text-green-600">✅ User is authenticated</p>';
        userInfo.innerHTML = `
            <p><strong>ID:</strong> ${user.id}</p>
            <p><strong>Name:</strong> ${user.name}</p>
            <p><strong>Email:</strong> ${user.email}</p>
            <p><strong>Username:</strong> ${user.username}</p>
        `;
    } else {
        authStatus.innerHTML = '<p class="text-red-600">❌ User is not authenticated</p>';
        userInfo.innerHTML = '<p>No user data available</p>';
    }
}

function simulateMobileLogin() {
    // Simulate mobile app authentication
    localStorage.setItem('user_id', '1');
    localStorage.setItem('username', 'AimarHaizzad');
    localStorage.setItem('user_email', 'AimarHaizzad@gmail.com');
    localStorage.setItem('user_name', 'Owner');
    localStorage.setItem('auth_token', '1|simulated_token_123');
    localStorage.setItem('is_authenticated', 'true');
    
    // Dispatch authentication event
    window.dispatchEvent(new CustomEvent('userAuthenticated', {
        detail: {
            id: '1',
            username: 'AimarHaizzad',
            email: 'AimarHaizzad@gmail.com',
            name: 'Owner',
            token: '1|simulated_token_123'
        }
    }));
    
    console.log('📱 Simulated mobile app authentication');
    testAuthentication();
}

// Listen for authentication events
window.addEventListener('laravelUserAuthenticated', function(event) {
    console.log('🎉 Laravel user authenticated event received:', event.detail);
    testAuthentication();
});

// Check authentication on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(testAuthentication, 2000);
});
</script>
@endsection
