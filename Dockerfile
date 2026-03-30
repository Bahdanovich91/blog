FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN groupmod -g 1000 www-data \
    && usermod -u 1000 -g 1000 www-data \
    && mkdir -p /var/www/html/var/cache/composer \
    && mkdir -p /var/www/html/var/templates_c \
    && chmod -R 777 /var/www/html/var \
    && chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

USER www-data
