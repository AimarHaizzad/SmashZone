#!/bin/bash
# Azure App Service startup script for Laravel
# Configure nginx to serve from Laravel's public directory

if [ -f /home/site/default ]; then
    cp /home/site/default /etc/nginx/sites-available/default
    service nginx reload
fi

# Ensure storage permissions
cd /home/site/wwwroot
chmod -R 755 storage bootstrap/cache 2>/dev/null || true
