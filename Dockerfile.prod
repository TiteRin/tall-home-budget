# ---- Étape vendor : installer dépendances PHP ----
FROM php:8.4-cli AS vendor

WORKDIR /app

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    unzip git curl libicu-dev libpq-dev \
    && docker-php-ext-install intl pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Installer composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

# 🚀 Désactivation des scripts artisan
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Copier le reste du projet
COPY . .


# ---- Étape frontend : build des assets ----
FROM node:20 AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


# ---- Étape finale : image de prod Laravel ----
FROM php:8.4-fpm-alpine AS prod

WORKDIR /var/www/html

# Installer intl et pdo_pgsql pour Postgres
RUN apk add --no-cache icu-dev libpq-dev \
    && docker-php-ext-install intl pdo_pgsql

# Copier dépendances PHP et assets compilés
COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

RUN composer dump-autoload --optimize && \
    php artisan package:discover --ansi || true

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
