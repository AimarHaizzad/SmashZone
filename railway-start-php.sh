#!/bin/bash
set -e

echo "🚀 Starting SmashZone on Railway (PHP Built-in Server)..."

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
sleep 2

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

# Start PHP built-in server (Railway provides PORT env variable)
echo "✅ Starting PHP server on port ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

