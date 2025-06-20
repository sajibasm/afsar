FROM php:7.4-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html

# Set correct permissions for runtime and assets folders
RUN chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets

# Copy and enable custom vhost config
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Install dependencies
#RUN composer install --no-interaction

# Expose port
EXPOSE 80