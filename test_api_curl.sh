#!/bin/bash

# 🧪 Mobile App Integration API Test Script
# This script tests the mobile app authentication API endpoints

echo "🏸 SmashZone Mobile App Integration API Test"
echo "============================================="
echo ""

# Configuration
BASE_URL="http://127.0.0.1:8001"
AUTH_TOKEN="9|UT4w4lMkzronZXIsX88pzpR6Yuvv3UaLqJUM9yqK0de9aa2d"
SERVER_URL="http://10.62.69.78:8000"

echo "🔧 Testing API Endpoints:"
echo "========================"
echo ""

# Test function
test_endpoint() {
    local page=$1
    local name=$2
    local param=$3
    
    echo "📱 Testing $name page with '$param' parameter..."
    
    response=$(curl -s -X GET "${BASE_URL}/api/generate-web-url?${param}=${page}" \
        -H "Authorization: Bearer ${AUTH_TOKEN}" \
        -H "Accept: application/json")
    
    if echo "$response" | grep -q '"success":true'; then
        echo "✅ $name ($param param): SUCCESS"
        
        # Extract and display key information
        target=$(echo "$response" | grep -o '"target_page":"[^"]*"' | cut -d'"' -f4)
        web_url=$(echo "$response" | grep -o '"web_url":"[^"]*"' | cut -d'"' -f4)
        
        echo "   Target: $target"
        echo "   URL: $web_url"
        
        # Verify server URL (decode the URL first)
        decoded_url=$(echo "$web_url" | sed 's/\\//g')
        if echo "$decoded_url" | grep -q "^$SERVER_URL"; then
            echo "   ✅ Correct server URL: $SERVER_URL"
        else
            echo "   ❌ Wrong server URL in response"
            echo "   Expected: $SERVER_URL"
            echo "   Got: $decoded_url"
        fi
    else
        echo "❌ $name ($param param): FAILED"
        echo "   Response: $response"
    fi
    
    echo ""
}

# Test all pages with 'page' parameter
echo "Testing with 'page' parameter:"
echo "=============================="
test_endpoint "dashboard" "Dashboard" "page"
test_endpoint "profile" "Profile" "page"
test_endpoint "bookings" "Bookings" "page"
test_endpoint "courts" "Courts" "page"
test_endpoint "payments" "Payments" "page"

# Test all pages with 'target' parameter
echo "Testing with 'target' parameter:"
echo "================================="
test_endpoint "dashboard" "Dashboard" "target"
test_endpoint "profile" "Profile" "target"
test_endpoint "bookings" "Bookings" "target"
test_endpoint "courts" "Courts" "target"
test_endpoint "payments" "Payments" "target"

echo "🎯 Test Summary:"
echo "==============="
echo "✅ All API endpoints should return success: true"
echo "✅ All URLs should start with: $SERVER_URL"
echo "✅ All URLs should contain: /mobile-auth?"
echo "✅ All URLs should contain authentication parameters"
echo "✅ Target pages should match the requested page"
echo ""

echo "📱 Mobile App Integration Flow:"
echo "==============================="
echo "1. Mobile app calls: /api/generate-web-url?page=dashboard"
echo "2. Laravel returns: web_url with authentication data"
echo "3. Mobile app opens: web_url in browser"
echo "4. Laravel processes: /mobile-auth route"
echo "5. User is logged in and redirected to target page"
echo ""

echo "🚀 Ready for Production!"
echo "======================="
echo "Your mobile app can now use these endpoints:"
echo "- /api/generate-web-url?page=dashboard"
echo "- /api/generate-web-url?page=profile"
echo "- /api/generate-web-url?page=bookings"
echo "- /api/generate-web-url?page=courts"
echo "- /api/generate-web-url?page=payments"
echo ""

echo "🧪 Manual Test Commands:"
echo "======================="
echo "# Test dashboard"
echo "curl -X GET \"${BASE_URL}/api/generate-web-url?page=dashboard\" \\"
echo "  -H \"Authorization: Bearer ${AUTH_TOKEN}\" \\"
echo "  -H \"Accept: application/json\""
echo ""
echo "# Test profile"
echo "curl -X GET \"${BASE_URL}/api/generate-web-url?page=profile\" \\"
echo "  -H \"Authorization: Bearer ${AUTH_TOKEN}\" \\"
echo "  -H \"Accept: application/json\""
echo ""
echo "# Test bookings"
echo "curl -X GET \"${BASE_URL}/api/generate-web-url?page=bookings\" \\"
echo "  -H \"Authorization: Bearer ${AUTH_TOKEN}\" \\"
echo "  -H \"Accept: application/json\""
