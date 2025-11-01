#!/bin/bash
# Azure App Service startup script for Laravel
cd /home/site/wwwroot

# Create symlink if it doesn't exist
if [ ! -L public_html ]; then
    ln -sfn public public_html
fi

# Ensure storage permissions
chmod -R 755 storage bootstrap/cache 2>/dev/null || true

# Start PHP built-in server if needed (not usually needed on Azure App Service)
# php -S 0.0.0.0:8000 -t public
