# syntax=docker/dockerfile:1

# --- Stage 1: build frontend assets -----------------------------------------
# public/build is gitignored, so the image has to build it itself rather
# than assume a committed manifest exists.
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources/ resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./

RUN npm run build

# --- Stage 2: PHP application ------------------------------------------------
FROM php:8.2-fpm AS app

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
        libzip-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        git \
    && docker-php-ext-install pdo_mysql gd zip bcmath \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
