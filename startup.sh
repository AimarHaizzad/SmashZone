#!/bin/bash
# Azure App Service startup script for Laravel
# Lightweight script - only essential operations

echo "=== Startup $(date) ==="

# Copy nginx config if it exists (quick operation)
if [ -f /home/site/default ]; then
    cp /home/site/default /etc/nginx/sites-available/default 2>/dev/null
    ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default 2>/dev/null
    # Don't reload nginx here - it's handled by Azure
fi

# Quick storage permissions check
if [ -d /home/site/wwwroot/storage ]; then
    chmod -R 755 /home/site/wwwroot/storage /home/site/wwwroot/bootstrap/cache 2>/dev/null || true
fi

echo "=== Startup completed $(date) ==="
exit 0
