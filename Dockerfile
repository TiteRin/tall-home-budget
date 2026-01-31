##############################
# Stage 1 — Composer vendor  #
##############################
FROM dunglas/frankenphp:php8.4-alpine AS vendor

RUN apk add --no-cache \
    git unzip zip curl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader


##############################
# Stage 2 — Frontend build   #
##############################
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY public ./public
COPY vite.config.* ./
COPY tailwind.config.* ./
COPY postcss.config.* ./

RUN npm run build


##############################
# Stage 3 — Runtime FrankenPHP
##############################
FROM dunglas/frankenphp:php8.4-alpine AS runtime

# Extensions Laravel nécessaires
RUN apk add --no-cache \
    icu-dev libpq-dev oniguruma-dev \
    && docker-php-ext-install \
        intl pdo_pgsql mbstring opcache

WORKDIR /var/www/html

# Code Laravel
COPY . .

# Dépendances PHP + assets buildés
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Config Caddy
COPY docker/frankenphp/Caddyfile.prod /etc/frankenphp/Caddyfile

# Permissions Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80 443

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
