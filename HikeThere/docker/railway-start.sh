#!/bin/bash
# Railway startup script - replaces PORT placeholder in nginx config

set -e  # Exit on error

echo "Starting Railway deployment..."
echo "PORT variable: ${PORT:-8080}"

# Check if PHP is available
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP not found in PATH"
    echo "Checking PHP installation..."
    ls -la /usr/local/bin/ || true
    ls -la /usr/bin/php* || true
    exit 1
fi

echo "PHP found: $(which php)"
echo "PHP version: $(php -v)"

# Replace PORT in nginx config with Railway's $PORT environment variable
echo "Configuring Nginx for port ${PORT:-8080}..."
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/sites-available/default

# Set permissions
echo "Setting permissions..."
chmod -R 777 /app/storage /app/bootstrap/cache

# Generate app key if not exists
echo "Generating app key..."
php artisan key:generate --force --no-interaction || true

# Run migrations
echo "Running migrations..."
php artisan migrate --force || true

# Link storage
echo "Linking storage..."
php artisan storage:link || true

# Clear config cache
echo "Clearing config cache..."
php artisan config:clear || true

# Start supervisord
echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
