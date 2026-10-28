FROM php:8.3-cli-alpine AS base

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite-dev \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip bcmath opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files and build frontend
COPY package.json package-lock.json ./
RUN npm ci --omit=dev || npm install

# Copy the rest of the application
COPY . .

# Use production env
RUN cp .env.production .env 2>/dev/null || true

# Finish composer setup (post-autoload-dump, etc.)
RUN composer dump-autoload --optimize --no-interaction

# Build frontend assets
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 database/database.sqlite 2>/dev/null || true

# Create entrypoint script
RUN printf '#!/bin/sh\n\
set -e\n\
\n\
# Ensure SQLite database exists\n\
if [ ! -f /var/www/html/database/database.sqlite ]; then\n\
    touch /var/www/html/database/database.sqlite\n\
    chmod 664 /var/www/html/database/database.sqlite\n\
fi\n\
\n\
# Run migrations\n\
php artisan migrate --force 2>/dev/null || true\n\
\n\
# Cache configuration\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Create storage link\n\
php artisan storage:link 2>/dev/null || true\n\
\n\
# Start the application\n\
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}\n\
' > /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 8080

CMD ["/entrypoint.sh"]
