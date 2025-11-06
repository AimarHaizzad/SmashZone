#!/bin/bash
# Exit on error, but we'll handle some errors gracefully
set -e

# Ensure we're in the correct directory
cd /var/www/html || exit 1

echo "🚀 Starting SmashZone on Railway..."
echo "📋 Environment check:"
echo "   PORT: ${PORT:-not set (will use 80)}"
echo "   DB_CONNECTION: ${DB_CONNECTION:-not set}"
echo "   Working directory: $(pwd)"

# Fix storage permissions (critical for Laravel to write logs and cache)
echo "🔧 Setting up storage permissions..."
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
# Ensure log file exists and is writable
touch storage/logs/laravel.log
chown www-data:www-data storage/logs/laravel.log
chmod 664 storage/logs/laravel.log

# Wait for database to be ready (Railway provides DB connection automatically)
echo "⏳ Waiting for database connection..."
# Wait up to 30 seconds for database to be ready
for i in {1..10}; do
    if php -r "
    try {
        \$host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: '127.0.0.1';
        \$port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: 3306;
        \$socket = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
        if (\$socket) {
            fclose(\$socket);
            exit(0);
        }
        exit(1);
    } catch (Exception \$e) {
        exit(1);
    }
    " 2>/dev/null; then
        echo "✅ Database is ready!"
        break
    fi
    echo "   Attempt $i/10: Database not ready yet, waiting..."
    sleep 3
done

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

# Create or update .env file from environment variables (needed for Laravel commands)
echo "📝 Setting up .env file from environment variables..."

# Function to set or update .env variable
set_env_var() {
    local key=$1
    local value=$2
    if [ -f .env ] && grep -q "^${key}=" .env 2>/dev/null; then
        # Update existing variable (escape special characters in value for sed)
        # Use a different delimiter (|) to avoid issues with / in values
        sed -i "s|^${key}=.*|${key}=${value}|" .env 2>/dev/null || true
    else
        # Add new variable
        echo "${key}=${value}" >> .env
    fi
}

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        touch .env
    fi
fi

# Sync environment variables to .env file
[ ! -z "$APP_NAME" ] && set_env_var "APP_NAME" "$APP_NAME"
[ ! -z "$APP_ENV" ] && set_env_var "APP_ENV" "$APP_ENV"
[ ! -z "$APP_DEBUG" ] && set_env_var "APP_DEBUG" "$APP_DEBUG"
[ ! -z "$APP_URL" ] && set_env_var "APP_URL" "$APP_URL"
[ ! -z "$DB_CONNECTION" ] && set_env_var "DB_CONNECTION" "$DB_CONNECTION"

# Handle Railway MySQL variables (Railway uses MYSQLHOST, MYSQLPORT, etc.)
if [ ! -z "$MYSQLHOST" ]; then
    set_env_var "DB_HOST" "$MYSQLHOST"
elif [ ! -z "$DB_HOST" ]; then
    set_env_var "DB_HOST" "$DB_HOST"
fi

if [ ! -z "$MYSQLPORT" ]; then
    set_env_var "DB_PORT" "$MYSQLPORT"
elif [ ! -z "$DB_PORT" ]; then
    set_env_var "DB_PORT" "$DB_PORT"
fi

if [ ! -z "$MYSQLDATABASE" ]; then
    set_env_var "DB_DATABASE" "$MYSQLDATABASE"
elif [ ! -z "$DB_DATABASE" ]; then
    set_env_var "DB_DATABASE" "$DB_DATABASE"
fi

if [ ! -z "$MYSQLUSER" ]; then
    set_env_var "DB_USERNAME" "$MYSQLUSER"
elif [ ! -z "$DB_USERNAME" ]; then
    set_env_var "DB_USERNAME" "$DB_USERNAME"
fi

if [ ! -z "$MYSQLPASSWORD" ]; then
    set_env_var "DB_PASSWORD" "$MYSQLPASSWORD"
elif [ ! -z "$DB_PASSWORD" ]; then
    set_env_var "DB_PASSWORD" "$DB_PASSWORD"
fi

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
    # Export the generated key to environment
    export APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2- | tr -d '\r\n')
else
    # If APP_KEY is set in environment, update .env file
    set_env_var "APP_KEY" "$APP_KEY"
fi

# Test database connection before running migrations
echo "🔍 Testing database connection..."
echo "   Database configuration:"
echo "   - Host: ${DB_HOST:-${MYSQLHOST:-not set}}"
echo "   - Port: ${DB_PORT:-${MYSQLPORT:-3306}}"
echo "   - Database: ${DB_DATABASE:-${MYSQLDATABASE:-not set}}"
echo "   - Username: ${DB_USERNAME:-${MYSQLUSER:-not set}}"
echo "   - Password: ${DB_PASSWORD:+***set***}${DB_PASSWORD:-${MYSQLPASSWORD:+***set***}${MYSQLPASSWORD:-not set}}"

php artisan db:show --database=mysql 2>/dev/null || {
    echo "⚠️ Database connection test failed. Will attempt migrations anyway..."
    echo "   Note: If migrations fail, check that MySQL service is added and connected in Railway."
}

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

