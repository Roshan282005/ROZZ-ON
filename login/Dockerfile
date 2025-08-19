# Use official PHP + Apache image
FROM php:8.2-apache

# Enable Apache mod_rewrite for PHP apps
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy app files to container
COPY . /var/www/html/

# Expose Railway's dynamic $PORT
# Railway sets the env var $PORT automatically
EXPOSE 8080

# Ensure Apache runs on $PORT instead of default 80
CMD ["sh", "-c", "apache2-foreground -DFOREGROUND -k start -e info -E /dev/stderr -d /etc/apache2 -f /etc/apache2/apache2.conf -c 'Listen ${PORT:-8080}'"]
