#!/bin/bash
# Azure App Service startup script for Laravel
# Configure nginx to serve from Laravel's public directory

# Log to file for debugging
exec > /home/LogFiles/startup.log 2>&1

echo "=== Startup script started at $(date) ==="

# Wait for nginx to be ready
sleep 2

# Configure nginx if custom config exists
if [ -f /home/site/default ]; then
    echo "Copying nginx config..."
    cp /home/site/default /etc/nginx/sites-available/default 2>/dev/null || true
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default 2>/dev/null || true
    
    # Test nginx config
    nginx -t 2>&1 | tee -a /home/LogFiles/startup.log
    
    # Reload nginx
    echo "Reloading nginx..."
    service nginx reload 2>&1 || service nginx restart 2>&1 || true
fi

# Ensure storage permissions
cd /home/site/wwwroot 2>/dev/null
if [ -d storage ]; then
    echo "Setting storage permissions..."
    chmod -R 755 storage bootstrap/cache 2>/dev/null || true
fi

# Install PHP SQL Server extensions if not present
echo "Checking PHP SQL Server extensions..."
php -m | grep -i sqlsrv || {
    echo "SQL Server extensions not found, checking if available..."
    # Azure App Service may have these in /usr/lib/php/*
}

echo "=== Startup script completed at $(date) ==="
