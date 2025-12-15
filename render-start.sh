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

# Function to set or update .env variable
set_env_var() {
    local key=$1
    local value=$2
    if [ -f .env ] && grep -q "^${key}=" .env 2>/dev/null; then
        # Update existing variable (use | as delimiter to avoid issues with / in values)
        sed -i "s|^${key}=.*|${key}=${value}|" .env 2>/dev/null || true
    else
        # Add new variable
        echo "${key}=${value}" >> .env
    fi
}

# Sync environment variables to .env file
echo "📝 Setting up .env file from environment variables..."
[ ! -z "$APP_NAME" ] && set_env_var "APP_NAME" "$APP_NAME"
[ ! -z "$APP_ENV" ] && set_env_var "APP_ENV" "$APP_ENV"
[ ! -z "$APP_DEBUG" ] && set_env_var "APP_DEBUG" "$APP_DEBUG"
[ ! -z "$APP_URL" ] && set_env_var "APP_URL" "$APP_URL"

# Handle database connection
# Render provides PostgreSQL by default, but we support MySQL too
# Force remove any existing DB_CONNECTION first to avoid conflicts
if [ -f .env ]; then
    sed -i '/^DB_CONNECTION=/d' .env 2>/dev/null || true
fi

if [ ! -z "$DATABASE_URL" ]; then
    # Render PostgreSQL (DATABASE_URL format: postgresql://user:password@host:port/database)
    echo "✅ Detected Render PostgreSQL (DATABASE_URL)"
    echo "   DATABASE_URL detected, forcing DB_CONNECTION=pgsql"
    set_env_var "DB_CONNECTION" "pgsql"
    # Parse DATABASE_URL (Render format: postgresql://user:password@host:port/database)
    # Remove protocol
    DB_URL=$(echo $DATABASE_URL | sed 's|postgresql://||' | sed 's|postgres://||')
    # Extract user
    DB_USER=$(echo $DB_URL | cut -d':' -f1)
    # Extract password (between first : and @)
    DB_PASS=$(echo $DB_URL | cut -d':' -f2 | cut -d'@' -f1)
    # Extract host:port (between @ and /)
    DB_HOST_PORT=$(echo $DB_URL | cut -d'@' -f2 | cut -d'/' -f1)
    # Extract host and port
    if echo "$DB_HOST_PORT" | grep -q ':'; then
        # Port is specified
        DB_HOST=$(echo $DB_HOST_PORT | cut -d':' -f1)
        DB_PORT=$(echo $DB_HOST_PORT | cut -d':' -f2)
        # Validate port is numeric
        if ! echo "$DB_PORT" | grep -qE '^[0-9]+$'; then
            DB_PORT="5432"
        fi
    else
        # No port specified, use default
        DB_HOST="$DB_HOST_PORT"
        DB_PORT="5432"
    fi
    # Extract database name (after /, before ?)
    DB_NAME=$(echo $DB_URL | cut -d'/' -f2 | cut -d'?' -f1)
    
    [ ! -z "$DB_HOST" ] && set_env_var "DB_HOST" "$DB_HOST"
    [ ! -z "$DB_PORT" ] && set_env_var "DB_PORT" "$DB_PORT"
    [ ! -z "$DB_NAME" ] && set_env_var "DB_DATABASE" "$DB_NAME"
    [ ! -z "$DB_USER" ] && set_env_var "DB_USERNAME" "$DB_USER"
    [ ! -z "$DB_PASS" ] && set_env_var "DB_PASSWORD" "$DB_PASS"
elif [ ! -z "$POSTGRES_HOST" ] || [ ! -z "$DB_HOST" ]; then
    # Render PostgreSQL (individual variables)
    echo "✅ Detected Render PostgreSQL (individual variables)"
    set_env_var "DB_CONNECTION" "pgsql"
    [ ! -z "$POSTGRES_HOST" ] && set_env_var "DB_HOST" "$POSTGRES_HOST"
    [ ! -z "$POSTGRES_PORT" ] && set_env_var "DB_PORT" "$POSTGRES_PORT"
    [ ! -z "$POSTGRES_DATABASE" ] && set_env_var "DB_DATABASE" "$POSTGRES_DATABASE"
    [ ! -z "$POSTGRES_USER" ] && set_env_var "DB_USERNAME" "$POSTGRES_USER"
    [ ! -z "$POSTGRES_PASSWORD" ] && set_env_var "DB_PASSWORD" "$POSTGRES_PASSWORD"
    # Fallback to standard DB_* variables
    [ ! -z "$DB_HOST" ] && set_env_var "DB_HOST" "$DB_HOST"
    [ ! -z "$DB_PORT" ] && set_env_var "DB_PORT" "$DB_PORT"
    [ ! -z "$DB_DATABASE" ] && set_env_var "DB_DATABASE" "$DB_DATABASE"
    [ ! -z "$DB_USERNAME" ] && set_env_var "DB_USERNAME" "$DB_USERNAME"
    [ ! -z "$DB_PASSWORD" ] && set_env_var "DB_PASSWORD" "$DB_PASSWORD"
elif [ ! -z "$MYSQL_HOST" ] || [ ! -z "$DB_HOST" ]; then
    # MySQL (Render or external)
    echo "✅ Detected MySQL"
    set_env_var "DB_CONNECTION" "mysql"
    [ ! -z "$MYSQL_HOST" ] && set_env_var "DB_HOST" "$MYSQL_HOST"
    [ ! -z "$MYSQL_PORT" ] && set_env_var "DB_PORT" "$MYSQL_PORT"
    [ ! -z "$MYSQL_DATABASE" ] && set_env_var "DB_DATABASE" "$MYSQL_DATABASE"
    [ ! -z "$MYSQL_USER" ] && set_env_var "DB_USERNAME" "$MYSQL_USER"
    [ ! -z "$MYSQL_PASSWORD" ] && set_env_var "DB_PASSWORD" "$MYSQL_PASSWORD"
    # Fallback to standard DB_* variables
    [ ! -z "$DB_HOST" ] && set_env_var "DB_HOST" "$DB_HOST"
    [ ! -z "$DB_PORT" ] && set_env_var "DB_PORT" "${DB_PORT:-3306}"
    [ ! -z "$DB_DATABASE" ] && set_env_var "DB_DATABASE" "$DB_DATABASE"
    [ ! -z "$DB_USERNAME" ] && set_env_var "DB_USERNAME" "$DB_USERNAME"
    [ ! -z "$DB_PASSWORD" ] && set_env_var "DB_PASSWORD" "$DB_PASSWORD"
else
    echo "⚠️ WARNING: No database connection variables found!"
    echo "   Please add a PostgreSQL or MySQL database in Render."
    set_env_var "DB_CONNECTION" "${DB_CONNECTION:-mysql}"
fi

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
    # Export the generated key
    export APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2- | tr -d '\r\n')
else
    set_env_var "APP_KEY" "$APP_KEY"
fi

# Display database configuration (without password)
echo "📋 Database configuration:"
echo "   DB_CONNECTION: $(grep "^DB_CONNECTION=" .env | cut -d '=' -f2 || echo 'not set')"
echo "   DB_HOST: $(grep "^DB_HOST=" .env | cut -d '=' -f2 || echo 'not set')"
echo "   DB_DATABASE: $(grep "^DB_DATABASE=" .env | cut -d '=' -f2 || echo 'not set')"

# Clear config cache (IMPORTANT: must clear before migrations to pick up new DB_CONNECTION)
echo "🔄 Clearing configuration cache..."
php artisan config:clear || true
php artisan cache:clear || true

# Verify DB_CONNECTION is set correctly
echo "🔍 Verifying database connection type..."
DB_CONN_TYPE=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2 || echo 'not set')
echo "   Current DB_CONNECTION in .env: $DB_CONN_TYPE"
if [ "$DB_CONN_TYPE" != "pgsql" ] && [ ! -z "$DATABASE_URL" ]; then
    echo "⚠️ WARNING: DB_CONNECTION is not 'pgsql' but DATABASE_URL is set!"
    echo "   Forcing DB_CONNECTION=pgsql..."
    set_env_var "DB_CONNECTION" "pgsql"
    php artisan config:clear || true
fi

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

