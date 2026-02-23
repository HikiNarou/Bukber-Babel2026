FROM richarvey/nginx-php-fpm:3.1.6

# Install dependencies system
RUN apk add --no-cache php82-mysqli php82-pdo_mysql mysql-client

# Copy project
COPY . /var/www/html

# Install composer dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Laravel optimization
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Permission
RUN chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
