#!/bin/bash

# Wait for a moment to ensure all services are ready
sleep 2

cd /var/www/html

# Ensure storage directory has correct permissions
chmod -R 777 storage bootstrap/cache

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate app key if missing or empty
if [ -z "$(grep '^APP_KEY=[^[:space:]]' .env)" ]; then
    php artisan key:generate --force
fi

# Clear config cache
php artisan config:clear

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Run original CMD
exec "$@"
