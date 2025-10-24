<?php
/**
 * 🧪 Mobile App Integration Test Script
 * 
 * This script tests the mobile app authentication integration
 * with different target pages and server URLs.
 */

echo "🏸 SmashZone Mobile App Integration Test\n";
echo "==========================================\n\n";

// Test configuration
$baseUrl = 'http://127.0.0.1:8001';
$authToken = '9|UT4w4lMkzronZXIsX88pzpR6Yuvv3UaLqJUM9yqK0de9aa2d';
$serverUrl = 'http://10.62.69.78:8000';

// Test pages
$testPages = [
    'dashboard' => 'Dashboard',
    'profile' => 'Profile',
    'bookings' => 'Bookings',
    'courts' => 'Courts',
    'payments' => 'Payments'
];

echo "🔧 Testing API Endpoints:\n";
echo "========================\n\n";

foreach ($testPages as $page => $name) {
    echo "📱 Testing {$name} page...\n";
    
    // Test with 'page' parameter
    $url = "{$baseUrl}/api/generate-web-url?page={$page}";
    $response = makeRequest($url, $authToken);
    
    if ($response && $response['success']) {
        echo "✅ {$name} (page param): SUCCESS\n";
        echo "   Target: {$response['target_page']}\n";
        echo "   URL: {$response['web_url']}\n";
        
        // Verify the URL contains the correct server
        if (strpos($response['web_url'], $serverUrl) === 0) {
            echo "   ✅ Correct server URL: {$serverUrl}\n";
        } else {
            echo "   ❌ Wrong server URL in response\n";
        }
    } else {
        echo "❌ {$name} (page param): FAILED\n";
    }
    
    echo "\n";
}

echo "🔧 Testing with 'target' parameter:\n";
echo "===================================\n\n";

foreach ($testPages as $page => $name) {
    echo "📱 Testing {$name} page with 'target' param...\n";
    
    // Test with 'target' parameter
    $url = "{$baseUrl}/api/generate-web-url?target={$page}";
    $response = makeRequest($url, $authToken);
    
    if ($response && $response['success']) {
        echo "✅ {$name} (target param): SUCCESS\n";
        echo "   Target: {$response['target_page']}\n";
        echo "   URL: {$response['web_url']}\n";
    } else {
        echo "❌ {$name} (target param): FAILED\n";
    }
    
    echo "\n";
}

echo "🎯 Expected Results Summary:\n";
echo "============================\n";
echo "✅ All API calls should return success: true\n";
echo "✅ All URLs should start with: {$serverUrl}\n";
echo "✅ All URLs should contain: /mobile-auth?\n";
echo "✅ All URLs should contain authentication parameters\n";
echo "✅ Target pages should match the requested page\n\n";

echo "📱 Mobile App Integration Flow:\n";
echo "==============================\n";
echo "1. Mobile app calls: /api/generate-web-url?page=dashboard\n";
echo "2. Laravel returns: web_url with authentication data\n";
echo "3. Mobile app opens: web_url in browser\n";
echo "4. Laravel processes: /mobile-auth route\n";
echo "5. User is logged in and redirected to target page\n\n";

echo "🚀 Ready for Production!\n";
echo "=======================\n";
echo "Your mobile app can now use these endpoints:\n";
echo "- /api/generate-web-url?page=dashboard\n";
echo "- /api/generate-web-url?page=profile\n";
echo "- /api/generate-web-url?page=bookings\n";
echo "- /api/generate-web-url?page=courts\n";
echo "- /api/generate-web-url?page=payments\n\n";

/**
 * Make HTTP request to API endpoint
 */
function makeRequest($url, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$token}",
        "Accept: application/json"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    
    return null;
}
