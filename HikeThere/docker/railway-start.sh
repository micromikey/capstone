#!/bin/bash
# Railway startup script - replaces PORT placeholder in nginx config

set -e  # Exit on error

echo "Starting Railway deployment..."
echo "PORT variable: ${PORT:-8080}"
echo "All environment variables:"
env | grep -E "(PORT|RAILWAY)" || echo "No PORT/RAILWAY variables found"

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

# Generate app key directly using PHP
echo "Generating app key..."
# Only generate if APP_KEY is not already set as environment variable or in .env
if [ -z "$APP_KEY" ] && ! grep -q "APP_KEY=base64:" /app/.env; then
    echo "Generating new APP_KEY..."
    # Generate a random 32-byte key and base64 encode it
    NEW_KEY="base64:$(openssl rand -base64 32)"
    # Replace APP_KEY= with the new key
    sed -i "s|^APP_KEY=.*|APP_KEY=${NEW_KEY}|" /app/.env
    echo "APP_KEY generated: ${NEW_KEY}"
    echo ""
    echo "⚠️  IMPORTANT: Add this to Railway Variables to persist across deployments:"
    echo "APP_KEY=${NEW_KEY}"
    echo ""
elif [ -n "$APP_KEY" ]; then
    echo "Using APP_KEY from environment variable"
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" /app/.env
else
    echo "APP_KEY already exists in .env, skipping generation"
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

# Cache config for better performance and to check for errors
echo "Caching configuration..."
if ! php artisan config:cache 2>&1; then
    echo "⚠️  ERROR: Config cache failed - there might be syntax errors in config files"
    echo "Continuing without config cache..."
fi

# Check if we can reach the app
echo "Testing Laravel application..."
php artisan about 2>&1 || echo "Unable to run 'artisan about', continuing anyway..."

# Start supervisord
echo "Starting supervisord..."
echo "Nginx will listen on 0.0.0.0:${PORT:-8080}"
echo "Testing if port is available..."
netstat -tuln | grep ${PORT:-8080} || echo "Port ${PORT:-8080} is available"

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
