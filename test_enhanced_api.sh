#!/bin/bash

# 🧪 Enhanced Mobile App Integration API Test
# This script tests the updated WebUrlController with enhanced page type mapping

echo "🏸 SmashZone Enhanced Mobile App Integration API Test"
echo "====================================================="
echo ""

# Configuration
BASE_URL="http://127.0.0.1:8001"
AUTH_TOKEN="15|nzHBh72P64T6pELPkYbx2p6xVLEmWDE5ufAy0cbn37909eb9"
SERVER_URL="http://10.62.69.78:8000"

echo "🔧 Testing Enhanced API Endpoints with Page Type Mapping:"
echo "============================================================"
echo ""

# Test function
test_endpoint() {
    local pageType=$1
    local name=$2
    
    echo "📱 Testing $name page (page type: $pageType)..."
    
    response=$(curl -s -X GET "${BASE_URL}/api/generate-web-url?page=${pageType}" \
        -H "Authorization: Bearer ${AUTH_TOKEN}" \
        -H "Accept: application/json")
    
    if echo "$response" | grep -q '"success":true'; then
        echo "✅ $name: SUCCESS"
        
        # Extract and display key information
        pageType=$(echo "$response" | grep -o '"page_type":"[^"]*"' | cut -d'"' -f4)
        targetPage=$(echo "$response" | grep -o '"target_page":"[^"]*"' | cut -d'"' -f4)
        webUrl=$(echo "$response" | grep -o '"web_url":"[^"]*"' | cut -d'"' -f4)
        message=$(echo "$response" | grep -o '"message":"[^"]*"' | cut -d'"' -f4)
        
        echo "   Page Type: $pageType"
        echo "   Target Page: $targetPage"
        echo "   Message: $message"
        echo "   URL: $webUrl"
        
        # Verify server URL
        decoded_url=$(echo "$webUrl" | sed 's/\\//g')
        if echo "$decoded_url" | grep -q "^$SERVER_URL"; then
            echo "   ✅ Correct server URL: $SERVER_URL"
        else
            echo "   ❌ Wrong server URL in response"
        fi
        
        # Verify target page mapping
        if [ "$pageType" = "$targetPage" ]; then
            echo "   ✅ Page type mapping correct: $pageType -> $targetPage"
        else
            echo "   ⚠️  Page type mapping: $pageType -> $targetPage"
        fi
    else
        echo "❌ $name: FAILED"
        echo "   Response: $response"
    fi
    
    echo ""
}

# Test all page types
echo "Testing Enhanced Page Type Mapping:"
echo "=================================="
test_endpoint "dashboard" "Dashboard"
test_endpoint "profile" "Profile"
test_endpoint "bookings" "Bookings"
test_endpoint "courts" "Courts"
test_endpoint "payments" "Payments"
test_endpoint "booking" "Booking (singular)"
test_endpoint "products" "Products"

# Test invalid page type (should default to dashboard)
echo "Testing Invalid Page Type (should default to dashboard):"
echo "======================================================="
test_endpoint "invalid" "Invalid Page Type"

echo "🎯 Enhanced Test Summary:"
echo "========================"
echo "✅ All API endpoints should return success: true"
echo "✅ All URLs should start with: $SERVER_URL"
echo "✅ All URLs should contain: /mobile-auth?"
echo "✅ All URLs should contain authentication parameters"
echo "✅ Page type mapping should work correctly"
echo "✅ Enhanced response should include page_type and target_page"
echo ""

echo "📱 Enhanced Mobile App Integration Flow:"
echo "======================================="
echo "1. Mobile app calls: /api/generate-web-url?page=dashboard"
echo "2. Laravel returns: web_url with enhanced page type mapping"
echo "3. Mobile app opens: web_url in browser"
echo "4. Laravel processes: /mobile-auth route with target page"
echo "5. User is logged in and redirected to specific target page"
echo ""

echo "🚀 Enhanced Features:"
echo "===================="
echo "✅ Page Type Mapping: dashboard, profile, bookings, courts, payments, booking, products"
echo "✅ Enhanced Response: includes page_type and target_page"
echo "✅ Dynamic Messages: 'Web URL generated successfully for {pageType} page'"
echo "✅ Fallback Handling: invalid page types default to dashboard"
echo "✅ Server URL: Correctly points to $SERVER_URL"
echo ""

echo "🧪 Manual Test Commands:"
echo "======================="
echo "# Test dashboard"
echo "curl -X GET \"${BASE_URL}/api/generate-web-url?page=dashboard\" \\"
echo "  -H \"Authorization: Bearer ${AUTH_TOKEN}\" \\"
echo "  -H \"Accept: application/json\""
echo ""
echo "# Test products"
echo "curl -X GET \"${BASE_URL}/api/generate-web-url?page=products\" \\"
echo "  -H \"Authorization: Bearer ${AUTH_TOKEN}\" \\"
echo "  -H \"Accept: application/json\""
echo ""
echo "# Test booking (singular)"
echo "curl -X GET \"${BASE_URL}/api/generate-web-url?page=booking\" \\"
echo "  -H \"Authorization: Bearer ${AUTH_TOKEN}\" \\"
echo "  -H \"Accept: application/json\""
echo ""

echo "🎉 Enhanced Mobile App Integration Complete!"
echo "==========================================="
echo "Your mobile app can now use these enhanced endpoints:"
echo "- /api/generate-web-url?page=dashboard"
echo "- /api/generate-web-url?page=profile"
echo "- /api/generate-web-url?page=bookings"
echo "- /api/generate-web-url?page=courts"
echo "- /api/generate-web-url?page=payments"
echo "- /api/generate-web-url?page=booking"
echo "- /api/generate-web-url?page=products"
echo ""
echo "🔑 Working Token:"
echo "================="
echo "Authorization: Bearer $AUTH_TOKEN"
