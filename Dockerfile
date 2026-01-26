FROM php:8.2-fpm

# Install system deps
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Fix permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy nginx config
COPY ./docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD service nginx start && php-fpm
