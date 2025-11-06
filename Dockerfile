# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
RUN chown -R www-data:www-data /var/www/html

# Create storage directories and set proper permissions
RUN mkdir -p /var/www/html/storage/logs \
    /var/www/html/storage/framework/cache \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/bootstrap/cache

# Set permissions (775 allows www-data to write)
RUN chmod -R 775 /var/www/html/storage
RUN chmod -R 775 /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev --no-interaction

# Install Node dependencies (including dev dependencies needed for build)
# Build assets, then optionally clean up node_modules to reduce image size
RUN npm install
RUN npm run build
RUN rm -rf node_modules

# Configure Apache
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Set Apache to listen on port provided by Railway
ENV APACHE_RUN_PORT=${PORT:-80}
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2.pid

# Expose port (Railway provides PORT env variable, default to 80)
# Railway typically uses ports like 8080, but our app will listen on whatever PORT is provided
EXPOSE 80

# Copy startup script
COPY railway-start.sh /usr/local/bin/railway-start.sh
RUN chmod +x /usr/local/bin/railway-start.sh

# Start Apache
CMD ["/usr/local/bin/railway-start.sh"]

