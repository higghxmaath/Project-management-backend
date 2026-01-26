FROM richarvey/nginx-php-fpm:2.0.0

COPY . /var/www/html

WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 775 storage bootstrap/cache

ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS 0
ENV RUN_SCRIPTS 1

EXPOSE 80
