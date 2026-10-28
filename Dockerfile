FROM php:8.3-fpm-alpine

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
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_sqlite zip bcmath opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package files and install npm deps
COPY package.json package-lock.json ./
RUN npm ci --omit=dev 2>/dev/null || npm install

# Copy the rest of the application
COPY . .

# Use production env
RUN cp .env.production .env 2>/dev/null || true

# Finish composer setup
RUN composer dump-autoload --optimize --no-interaction

# Build frontend assets
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache \
    && chmod 664 database/database.sqlite 2>/dev/null || true

# Nginx configuration - listen on port 7860 (Hugging Face requirement)
RUN printf 'server {\n\
    listen 7860;\n\
    server_name localhost;\n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
    client_max_body_size 20M;\n\
    charset utf-8;\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    location = /favicon.ico { access_log off; log_not_found off; }\n\
    location = /robots.txt  { access_log off; log_not_found off; }\n\
\n\
    error_page 404 /index.php;\n\
\n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
\n\
    location ~ /\\.(?!well-known).* {\n\
        deny all;\n\
    }\n\
}\n' > /etc/nginx/http.d/default.conf

# Supervisor configuration
RUN printf '[supervisord]\n\
nodaemon=true\n\
user=root\n\
\n\
[program:php-fpm]\n\
command=php-fpm -F\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
\n\
[program:nginx]\n\
command=nginx -g "daemon off;"\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
' > /etc/supervisord.conf

# Entrypoint script
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
# Start Supervisor (runs Nginx + PHP-FPM)\n\
exec /usr/bin/supervisord -c /etc/supervisord.conf\n\
' > /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 7860

CMD ["/entrypoint.sh"]
