#!/bin/sh
# Railway startup script - replaces PORT placeholder in nginx config

# Replace PORT in nginx config with Railway's $PORT environment variable
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/sites-available/default

# Set permissions
chmod -R 777 /app/storage /app/bootstrap/cache

# Generate app key if not exists
php artisan key:generate --force --no-interaction || true

# Run migrations
php artisan migrate --force || true

# Link storage
php artisan storage:link || true

# Clear config cache
php artisan config:clear || true

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
