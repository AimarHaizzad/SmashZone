#!/bin/bash
# Azure App Service startup script for Laravel
# Configure nginx to serve from Laravel's public directory

# Configure nginx if custom config exists
if [ -f /home/site/default ]; then
    cp /home/site/default /etc/nginx/sites-available/default
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
    service nginx reload || service nginx restart || true
fi

# Ensure storage permissions
cd /home/site/wwwroot 2>/dev/null || cd /home/site/wwwroot
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
