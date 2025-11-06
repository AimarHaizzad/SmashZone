#!/bin/bash
# Don't exit on error for non-critical commands
set -e

echo "🚀 Starting SmashZone on Railway..."
echo "📋 Environment check:"
echo "   PORT: ${PORT:-not set (will use 80)}"
echo "   DB_CONNECTION: ${DB_CONNECTION:-not set}"

# Wait for database to be ready (Railway provides DB connection automatically)
echo "⏳ Waiting for database connection..."
sleep 2

# Ensure we're using MySQL on Railway
# Railway provides MySQL connection via environment variables
# Check if MySQL connection variables are present (Railway uses MYSQLHOST or DB_HOST)
if [ -z "$DB_CONNECTION" ]; then
    if [ ! -z "$MYSQLHOST" ] || [ ! -z "$DB_HOST" ]; then
        echo "✅ MySQL detected, setting DB_CONNECTION=mysql"
        export DB_CONNECTION=mysql
    else
        echo "⚠️ WARNING: DB_CONNECTION not set and no MySQL variables detected!"
        echo "   Please set DB_CONNECTION=mysql in Railway environment variables."
        echo "   Railway should automatically provide MySQL connection variables when you add a MySQL service."
    fi
fi

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "📦 Running database migrations..."
php artisan migrate --force || echo "⚠️ Migration failed, continuing..."

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || echo "⚠️ Storage link already exists"

# Clear and cache configuration
echo "⚡ Optimizing application..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Configure Apache to listen on Railway's PORT (defaults to 80)
LISTEN_PORT=${PORT:-80}
echo "🔧 Configuring Apache to listen on port $LISTEN_PORT..."

# Update ports.conf to listen on the correct port
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

# Verify Apache configuration (don't fail if test fails, but log it)
echo "🔍 Verifying Apache configuration..."
if apache2ctl configtest 2>&1; then
    echo "✅ Apache configuration is valid"
else
    echo "⚠️ Apache config test had warnings, but continuing..."
fi

# Start Apache in foreground
echo "✅ Starting Apache server on port $LISTEN_PORT..."
echo "📝 PORT environment variable: $PORT"
echo "📝 Apache will listen on: $LISTEN_PORT"

# Start Apache
exec apache2-foreground

