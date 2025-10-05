# Production Multi-stage Dockerfile for HikeThere Laravel Application
FROM php:8.2-fpm as base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Configure PHP for production
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'realpath_cache_size=4096K'; \
    echo 'realpath_cache_ttl=600'; \
} > /usr/local/etc/php/conf.d/opcache.ini

RUN { \
    echo 'memory_limit=256M'; \
    echo 'upload_max_filesize=20M'; \
    echo 'post_max_size=25M'; \
    echo 'max_execution_time=300'; \
} > /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (include dev for Docker development)
RUN composer install --optimize-autoloader --no-scripts --no-interaction

# Copy package files
COPY package*.json ./

# Install Node dependencies - clean install to avoid optional dependency bugs
RUN npm ci || npm install

# Explicitly install Rollup Linux binary (workaround for npm optional deps bug)
RUN npm install --no-save @rollup/rollup-linux-x64-gnu

# Copy application files
COPY . .

# Run composer scripts
RUN composer dump-autoload --optimize

# Debug: Check if vite.config.js exists
RUN ls -la vite.config.js postcss.config.js tailwind.config.js || echo "Config files missing!"

# Debug: Check resources directory
RUN ls -la resources/css/ resources/js/ || echo "Resource files missing!"

# Build frontend assets (must succeed for production)
RUN npm run build

# Debug: Verify build output
RUN ls -la public/build/ || echo "Build directory not created!"
RUN cat public/build/.vite/manifest.json || echo "Manifest not created!"

# Create build directory if npm run build didn't create it
RUN mkdir -p /app/public/build

# Create necessary directories and set permissions
RUN mkdir -p /app/storage/logs \
    /app/storage/framework/cache \
    /app/storage/framework/sessions \
    /app/storage/framework/views \
    /app/bootstrap/cache \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Copy nginx configuration
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Copy PHP-FPM configuration (remove default first to avoid conflicts)
RUN rm -f /usr/local/etc/php-fpm.d/www.conf.default /usr/local/etc/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# Copy supervisor configuration
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Copy Railway startup script
COPY docker/railway-start.sh /railway-start.sh
RUN chmod +x /railway-start.sh

# Expose port (Railway will assign dynamic port)
EXPOSE ${PORT:-8080}

# Use the startup script as the default command
CMD ["/railway-start.sh"]
