# syntax=docker/dockerfile:1

FROM php:8.2-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       unzip \
       libzip-dev \
       libpng-dev \
       libicu-dev \
       libonig-dev \
       libxml2-dev \
    && pecl install xdebug \
    && docker-php-ext-install pdo_mysql intl bcmath zip \
    && docker-php-ext-enable opcache xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
