#!/bin/bash
# Azure App Service startup script for Laravel
# Configure nginx to serve from Laravel's public directory
# This script must complete quickly - Azure handles nginx/PHP-FPM automatically

# Log to file for debugging
exec > /home/LogFiles/startup.log 2>&1

echo "=== Startup script started at $(date) ==="

# Configure nginx if custom config exists (run in background to avoid blocking)
if [ -f /home/site/default ]; then
    echo "Copying nginx config..."
    cp /home/site/default /etc/nginx/sites-available/default 2>/dev/null &
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default 2>/dev/null &
    
    # Test nginx config (non-blocking)
    timeout 5 nginx -t 2>&1 || true
    
    # Reload nginx (non-blocking)
    timeout 5 service nginx reload 2>&1 || timeout 5 service nginx restart 2>&1 || true &
fi

# Ensure storage permissions (run in background)
(
    cd /home/site/wwwroot 2>/dev/null
    if [ -d storage ]; then
        chmod -R 755 storage bootstrap/cache 2>/dev/null || true
    fi
) &

# Wait for background jobs (but timeout quickly)
wait

echo "=== Startup script completed at $(date) ==="

# Exit immediately - don't block
exit 0
