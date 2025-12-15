#!/bin/bash
# Exit on error
set -e

# Ensure we're in the correct directory
cd /var/www/html || exit 1

echo "🚀 Starting SmashZone on Render..."

# Fix storage permissions (critical for Laravel to write logs and cache)
echo "🔧 Setting up storage permissions..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
touch storage/logs/laravel.log
chown www-data:www-data storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Clear config cache
echo "🔄 Clearing configuration cache..."
php artisan config:clear || true

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force || {
    echo "⚠️ Migration failed! Check your database connection."
}

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️ Storage link already exists"

# Cache configuration for better performance
echo "⚡ Optimizing application..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Configure Apache to listen on Render's PORT (Render provides PORT env variable)
LISTEN_PORT=${PORT:-10000}
echo "🔧 Configuring Apache to listen on port $LISTEN_PORT..."

# Update ports.conf
if grep -q "Listen 80" /etc/apache2/ports.conf; then
    sed -i "s/Listen 80/Listen $LISTEN_PORT/g" /etc/apache2/ports.conf
elif ! grep -q "Listen $LISTEN_PORT" /etc/apache2/ports.conf; then
    echo "Listen $LISTEN_PORT" >> /etc/apache2/ports.conf
fi

# Update virtual host configuration
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:$LISTEN_PORT>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Start Apache in foreground
echo "✅ Starting Apache server on port $LISTEN_PORT..."
exec apache2-foreground

