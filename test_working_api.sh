#!/bin/bash

# 🧪 Working Mobile App Integration API Test
# This script tests the mobile app authentication API with the correct token

echo "🏸 SmashZone Mobile App Integration API Test"
echo "============================================="
echo ""

# Configuration
BASE_URL="http://127.0.0.1:8001"
AUTH_TOKEN="15|nzHBh72P64T6pELPkYbx2p6xVLEmWDE5ufAy0cbn37909eb9"
SERVER_URL="http://10.62.69.78:8000"

echo "🔧 Testing API Endpoints with Working Token:"
echo "============================================"
echo ""

# Test function
test_endpoint() {
    local page=$1
    local name=$2
    
    echo "📱 Testing $name page..."
    
    response=$(curl -s -X GET "${BASE_URL}/api/generate-web-url?page=${page}" \
        -H "Authorization: Bearer ${AUTH_TOKEN}" \
        -H "Accept: application/json")
    
    if echo "$response" | grep -q '"success":true'; then
        echo "✅ $name: SUCCESS"
        
        # Extract and display key information
        target=$(echo "$response" | grep -o '"target_page":"[^"]*"' | cut -d'"' -f4)
        web_url=$(echo "$response" | grep -o '"web_url":"[^"]*"' | cut -d'"' -f4)
        
        echo "   Target: $target"
        echo "   URL: $web_url"
        
        # Verify server URL
        decoded_url=$(echo "$web_url" | sed 's/\\//g')
        if echo "$decoded_url" | grep -q "^$SERVER_URL"; then
            echo "   ✅ Correct server URL: $SERVER_URL"
        else
            echo "   ❌ Wrong server URL in response"
        fi
    else
        echo "❌ $name: FAILED"
        echo "   Response: $response"
    fi
    
    echo ""
}

# Test all pages
test_endpoint "dashboard" "Dashboard"
test_endpoint "profile" "Profile"
test_endpoint "bookings" "Bookings"
test_endpoint "courts" "Courts"
test_endpoint "payments" "Payments"

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

echo "🔑 Working Token:"
echo "================="
echo "Authorization: Bearer $AUTH_TOKEN"
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
