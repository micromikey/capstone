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
echo "Configuring Nginx for port 9000 (Railway's assigned port)..."
# Note: Railway expects port 9000, not 8080
# sed -i "s/listen 9000;/listen ${PORT:-9000};/" /etc/nginx/sites-available/default

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

# Debug: Check if Vite build assets exist at runtime
echo "=== CHECKING VITE ASSETS AT RUNTIME ==="
if [ -f "/app/public/build/.vite/manifest.json" ]; then
    echo "✓ Vite manifest found!"
    ls -la /app/public/build/
else
    echo "✗ CRITICAL: Vite manifest NOT found at runtime!"
    echo "Build directory contents:"
    ls -la /app/public/build/ || echo "Build directory doesn't exist!"
fi
echo "========================================"

# Clear config cache
echo "Clearing config cache..."
php artisan config:clear || true

# Clear ALL caches to ensure fresh deployment
echo "Clearing view cache..."
php artisan view:clear || true

echo "Clearing route cache..."
php artisan route:clear || true

echo "Clearing application cache..."
php artisan cache:clear || true

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
echo "Nginx will listen on 0.0.0.0:9000 (Railway's assigned port)"
echo "PHP-FPM will listen on 127.0.0.1:9001 (internal)"
echo "Health check endpoint: /up"
echo "Public domain: ${RAILWAY_PUBLIC_DOMAIN}"
echo ""

# Start supervisord in background for diagnostics
/usr/bin/supervisord -c /etc/supervisor/supervisord.conf &
SUPER_PID=$!

# Wait for services to start
echo "Waiting 5 seconds for services to start..."
sleep 5

# Diagnostic checks
echo "=== DIAGNOSTIC CHECKS ==="
echo "1. Checking if nginx is listening on port 9000:"
netstat -tuln | grep ":9000" || echo "❌ ERROR: Nginx NOT listening on 9000"

echo ""
echo "2. Checking if PHP-FPM is listening on port 9001:"
netstat -tuln | grep ":9001" || echo "❌ ERROR: PHP-FPM NOT listening on 9001"

echo ""
echo "3. Testing nginx -> PHP-FPM connection:"
curl -I http://127.0.0.1:9000/ 2>&1 | head -10

echo ""
echo "4. Checking nginx error log:"
tail -20 /var/log/nginx/error.log 2>/dev/null || echo "No errors in nginx log"

echo ""
echo "5. Checking PHP-FPM status:"
ps aux | grep php-fpm | head -5

echo ""
echo "=== Starting normal operation ==="
wait $SUPER_PID
