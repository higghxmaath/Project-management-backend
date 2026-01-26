FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    curl

RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

COPY . /var/www
WORKDIR /var/www

RUN chown -R www-data:www-data /var/www

COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 10000

CMD service nginx start && php-fpm
