<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏸 SmashZone - Mobile App Integration Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            color: white;
            min-height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            font-weight: bold;
        }
        .success { background: rgba(76, 175, 80, 0.3); }
        .error { background: rgba(244, 67, 54, 0.3); }
        .info { background: rgba(33, 150, 243, 0.3); }
        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .btn {
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover { background: #45a049; }
        .console {
            background: #000;
            color: #0f0;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            margin: 20px 0;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏸 SmashZone Mobile App Integration Test</h1>
        
        <div id="status" class="status info">
            🔍 Checking for mobile app authentication...
        </div>
        
        <div id="user-info" class="user-info" style="display: none;">
            <h3>👤 Authenticated User Information:</h3>
            <div id="user-details"></div>
        </div>
        
        <div id="actions" style="display: none;">
            <button class="btn" onclick="testApiCall()">🧪 Test API Call</button>
            <button class="btn" onclick="checkAuthStatus()">🔐 Check Auth Status</button>
            <button class="btn" onclick="clearAuth()">🚪 Clear Authentication</button>
        </div>
        
        <div class="console" id="console">
            <div>🏸 SmashZone Mobile App Integration Test Page</div>
            <div>📱 This page tests authentication from your mobile app</div>
            <div>🔍 Checking URL parameters and localStorage...</div>
        </div>
    </div>

    <script>
        // 🏸 SmashZone Mobile App Integration Test
        function log(message) {
            const console = document.getElementById('console');
            const timestamp = new Date().toLocaleTimeString();
            console.innerHTML += `<div>[${timestamp}] ${message}</div>`;
            console.scrollTop = console.scrollHeight;
        }
        
        function updateStatus(message, type = 'info') {
            const status = document.getElementById('status');
            status.textContent = message;
            status.className = `status ${type}`;
        }
        
        function showUserInfo(userData) {
            const userInfo = document.getElementById('user-info');
            const userDetails = document.getElementById('user-details');
            const actions = document.getElementById('actions');
            
            userDetails.innerHTML = `
                <p><strong>ID:</strong> ${userData.id}</p>
                <p><strong>Name:</strong> ${userData.name}</p>
                <p><strong>Email:</strong> ${userData.email}</p>
                <p><strong>Token:</strong> ${userData.token ? userData.token.substring(0, 20) + '...' : 'None'}</p>
            `;
            
            userInfo.style.display = 'block';
            actions.style.display = 'block';
        }
        
        function checkMobileAuthentication() {
            log('🔍 Checking for mobile app authentication...');
            
            // Check URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const isAuthenticated = urlParams.get('authenticated') === 'true';
            const userId = urlParams.get('user_id');
            const username = urlParams.get('username');
            const userEmail = urlParams.get('user_email');
            const userName = urlParams.get('user_name');
            const authToken = urlParams.get('auth_token');
            
            if (isAuthenticated && userId) {
                log('✅ Mobile app authentication detected via URL parameters');
                log(`👤 User ID: ${userId}`);
                log(`👤 Username: ${username}`);
                log(`📧 Email: ${userEmail}`);
                log(`🔑 Token: ${authToken ? authToken.substring(0, 20) + '...' : 'None'}`);
                
                // Store in localStorage for persistence
                localStorage.setItem('user_id', userId);
                localStorage.setItem('username', username);
                localStorage.setItem('user_email', userEmail);
                localStorage.setItem('user_name', userName);
                localStorage.setItem('auth_token', authToken);
                localStorage.setItem('is_authenticated', 'true');
                
                updateStatus('✅ Mobile app user is authenticated!', 'success');
                showUserInfo({
                    id: userId,
                    name: userName,
                    email: userEmail,
                    token: authToken
                });
                
                return true;
            } else {
                log('❌ No mobile app authentication found in URL parameters');
                
                // Check localStorage as fallback
                const storedAuth = localStorage.getItem('is_authenticated');
                if (storedAuth === 'true') {
                    log('🔄 Found stored authentication in localStorage');
                    const userData = {
                        id: localStorage.getItem('user_id'),
                        name: localStorage.getItem('user_name'),
                        email: localStorage.getItem('user_email'),
                        token: localStorage.getItem('auth_token')
                    };
                    
                    updateStatus('✅ User authenticated from stored data', 'success');
                    showUserInfo(userData);
                    return true;
                } else {
                    log('❌ No stored authentication found');
                    updateStatus('❌ No mobile app authentication found', 'error');
                    return false;
                }
            }
        }
        
        function testApiCall() {
            log('🧪 Testing API call with authentication token...');
            
            const token = localStorage.getItem('auth_token');
            if (!token) {
                log('❌ No authentication token found');
                return;
            }
            
            fetch('/api/auth/user', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                log('✅ API call successful: ' + JSON.stringify(data));
            })
            .catch(error => {
                log('❌ API call failed: ' + error.message);
            });
        }
        
        function checkAuthStatus() {
            log('🔐 Checking authentication status...');
            
            const userData = {
                id: localStorage.getItem('user_id'),
                name: localStorage.getItem('user_name'),
                email: localStorage.getItem('user_email'),
                token: localStorage.getItem('auth_token'),
                isAuthenticated: localStorage.getItem('is_authenticated')
            };
            
            log('📊 Current authentication status:');
            log(`   - User ID: ${userData.id}`);
            log(`   - Name: ${userData.name}`);
            log(`   - Email: ${userData.email}`);
            log(`   - Token: ${userData.token ? userData.token.substring(0, 20) + '...' : 'None'}`);
            log(`   - Authenticated: ${userData.isAuthenticated}`);
        }
        
        function clearAuth() {
            log('🚪 Clearing authentication data...');
            
            localStorage.removeItem('user_id');
            localStorage.removeItem('username');
            localStorage.removeItem('user_email');
            localStorage.removeItem('user_name');
            localStorage.removeItem('auth_token');
            localStorage.removeItem('is_authenticated');
            
            document.getElementById('user-info').style.display = 'none';
            document.getElementById('actions').style.display = 'none';
            
            updateStatus('🚪 Authentication cleared', 'info');
            log('✅ Authentication data cleared');
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            log('🚀 Page loaded, checking for mobile app authentication...');
            
            setTimeout(function() {
                if (checkMobileAuthentication()) {
                    log('🎉 Mobile app integration test successful!');
                } else {
                    log('⚠️ Mobile app integration test failed - no authentication found');
                }
            }, 1000);
        });
    </script>
</body>
</html>
