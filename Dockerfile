FROM richarvey/nginx-php-fpm:3.1.6

# Point nginx document root at Laravel's public/ directory
ENV WEBROOT /var/www/html/public

# Skip the image's built-in composer run (we do it ourselves below)
ENV SKIP_COMPOSER 1

# Enable startup scripts in /var/www/html/scripts/
ENV RUN_SCRIPTS 1

# Install required PHP extensions
RUN apk add --no-cache php82-mysqli php82-pdo_mysql mysql-client

# Copy project files
COPY . /var/www/html

WORKDIR /var/www/html

# Install Composer dependencies (no dev packages in production)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Set permissions (runtime script will re-apply after any volume mounts)
RUN chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/scripts/00-laravel.sh
