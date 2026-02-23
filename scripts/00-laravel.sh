#!/usr/bin/env bash
set -e

cd /var/www/html

# Render injects env vars directly into the container; no .env file is needed.
# config:cache will capture all runtime env vars (APP_KEY, DB_*, etc.).
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage (ignore error if already linked)
php artisan storage:link 2>/dev/null || true

# Fix permissions after potential mount changes
chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
