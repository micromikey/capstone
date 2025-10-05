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

# Create .env from example if it doesn't exist (Railway uses env variables)
if [ ! -f /app/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /app/.env.example /app/.env
fi

# Generate app key - use --show and manually update if key:generate fails
echo "Generating app key..."
if ! php artisan key:generate --force --no-interaction 2>&1; then
    echo "Standard key:generate failed, generating key manually..."
    KEY=$(php artisan key:generate --show)
    sed -i "s/APP_KEY=.*/APP_KEY=${KEY}/" /app/.env
    echo "APP_KEY set manually: ${KEY}"
fi

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
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
