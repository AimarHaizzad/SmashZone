#!/bin/bash

# 🚀 SmashZone Deployment Script
# This script prepares your Laravel app for production deployment

echo "🏸 SmashZone Deployment Script"
echo "================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "Please run this script from the Laravel project root directory"
    exit 1
fi

print_status "Starting deployment preparation..."

# 1. Install/Update Dependencies
print_status "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev
if [ $? -eq 0 ]; then
    print_success "PHP dependencies installed"
else
    print_error "Failed to install PHP dependencies"
    exit 1
fi

print_status "Installing Node.js dependencies..."
npm install --production
if [ $? -eq 0 ]; then
    print_success "Node.js dependencies installed"
else
    print_error "Failed to install Node.js dependencies"
    exit 1
fi

# 2. Build Production Assets
print_status "Building production assets..."
npm run build
if [ $? -eq 0 ]; then
    print_success "Production assets built successfully"
else
    print_error "Failed to build production assets"
    exit 1
fi

# 3. Generate Application Key (if not exists)
if [ -z "$(grep 'APP_KEY=' .env 2>/dev/null | cut -d '=' -f2)" ] || [ "$(grep 'APP_KEY=' .env 2>/dev/null | cut -d '=' -f2)" = "" ]; then
    print_status "Generating application key..."
    php artisan key:generate
    if [ $? -eq 0 ]; then
        print_success "Application key generated"
    else
        print_warning "Failed to generate application key - you may need to run 'php artisan key:generate' manually"
    fi
else
    print_success "Application key already exists"
fi

# 4. Run Database Migrations
print_status "Running database migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    print_success "Database migrations completed"
else
    print_warning "Database migrations failed - you may need to run 'php artisan migrate' manually"
fi

# 5. Create Storage Link
print_status "Creating storage link..."
php artisan storage:link
if [ $? -eq 0 ]; then
    print_success "Storage link created"
else
    print_warning "Storage link may already exist or failed to create"
fi

# 6. Clear and Cache Configuration
print_status "Optimizing application for production..."

print_status "Caching configuration..."
php artisan config:cache
if [ $? -eq 0 ]; then
    print_success "Configuration cached"
else
    print_warning "Configuration caching failed"
fi

print_status "Caching routes..."
php artisan route:cache
if [ $? -eq 0 ]; then
    print_success "Routes cached"
else
    print_warning "Route caching failed"
fi

print_status "Caching views..."
php artisan view:cache
if [ $? -eq 0 ]; then
    print_success "Views cached"
else
    print_warning "View caching failed"
fi

# 7. Set Proper Permissions
print_status "Setting file permissions..."
chmod -R 755 storage bootstrap/cache
if [ $? -eq 0 ]; then
    print_success "File permissions set"
else
    print_warning "Failed to set file permissions - you may need to run 'chmod -R 755 storage bootstrap/cache' manually"
fi

# 8. Create Deployment Package
print_status "Creating deployment package..."
tar -czf smashzone-deployment-$(date +%Y%m%d-%H%M%S).tar.gz \
    --exclude=node_modules \
    --exclude=.git \
    --exclude=storage/logs \
    --exclude=.env \
    --exclude=deploy.sh \
    --exclude="*.md" \
    --exclude="test_*.php" \
    --exclude="test_*.sh" \
    --exclude="cookies*.txt" \
    --exclude="complete_past_bookings.php" \
    --exclude="sample_data.php" \
    .

if [ $? -eq 0 ]; then
    print_success "Deployment package created: smashzone-deployment-$(date +%Y%m%d-%H%M%S).tar.gz"
else
    print_warning "Failed to create deployment package"
fi

# 9. Display Next Steps
echo ""
echo "🎉 Deployment preparation complete!"
echo "================================"
echo ""
echo "📋 Next steps:"
echo "1. Upload the deployment package to your server"
echo "2. Extract the files on your server"
echo "3. Copy .env.example to .env and configure with production values"
echo "4. Run 'php artisan key:generate' on the server"
echo "5. Run 'php artisan migrate --force' on the server"
echo "6. Set up your web server (Nginx/Apache) to point to the 'public' directory"
echo "7. Configure SSL certificate"
echo "8. Set up cron job for Laravel scheduler"
echo ""
echo "📚 For detailed instructions, see: DEPLOYMENT_GUIDE.md"
echo ""
echo "🔧 Quick server setup commands:"
echo "   chmod -R 755 storage bootstrap/cache"
echo "   chown -R www-data:www-data storage bootstrap/cache"
echo "   php artisan config:cache"
echo "   php artisan route:cache"
echo "   php artisan view:cache"
echo ""
print_success "Ready for deployment! 🚀"
