FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite-dev \
    nodejs \
    npm

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip bcmath opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install dependencies and build assets
RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build

# Nginx configuration
RUN echo 'server { \
    listen 80; \
    server_name _; \
    root /var/www/html/public; \
    index index.php index.html; \
    client_max_body_size 20M; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/http.d/default.conf

# Supervisor configuration
RUN echo '[supervisord] \
nodaemon=true \
[program:php-fpm] \
command=php-fpm -F \
autostart=true \
autorestart=true \
[program:nginx] \
command=nginx -g "daemon off;" \
autostart=true \
autorestart=true' > /etc/supervisord.conf

# Startup script to ensure database exists and is migrated
RUN echo '#!/bin/sh \
if [ ! -f /var/www/html/database/database.sqlite ]; then \
    touch /var/www/html/database/database.sqlite; \
    chown www-data:www-data /var/www/html/database/database.sqlite; \
    chmod 664 /var/www/html/database/database.sqlite; \
fi \
php artisan migrate --force \
php artisan config:cache \
php artisan route:cache \
php artisan view:cache \
exec /usr/bin/supervisord -c /etc/supervisord.conf' > /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 80

CMD ["/entrypoint.sh"]
