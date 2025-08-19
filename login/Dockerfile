FROM php:8.2-fpm

# Install Nginx and required PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip unzip git \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Create required directories
RUN mkdir -p /run/php

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Remove default Nginx config and add our own
RUN rm /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Environment for Railway
ENV PORT=8080
EXPOSE 8080

# Start PHP-FPM and Nginx
CMD php-fpm -D && nginx -g "daemon off;"
