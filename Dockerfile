##############################
# Dépendances PHP (composer) #
##############################
FROM dunglas/frankenphp AS vendor

# Dépendances système pour intl
RUN apk add --no-cache \
    icu-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts


##############################
# Build Vite                 #
##############################
FROM node:25-alpine AS frontend

WORKDIR /app

# Cache NPM
COPY package.json ./
COPY package-lock.json ./

RUN npm install
RUN npm ci

# Code frontend uniquement
COPY resources ./resources
COPY vite.config.* ./

# Build assets
RUN npm run build

##############################
# Run-time PHP               #
##############################
FROM php:8.4-fpm-alpine AS runtime

RUN apk add --no-cache \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    zip \
    unzip \
    && docker-php-ext-install \
        intl \
        pdo_pgsql \
        mbstring \
        opcache \
    && rm -rf /var/cache/apk/*

RUN rm -rf bootstrap/cache/*
WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN chown -R www-data:www-data \
    storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
