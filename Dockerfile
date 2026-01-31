##############################
# Stage 1 — Composer vendor  #
##############################
FROM dunglas/frankenphp:php8.4-alpine AS vendor

# System deps + PHP extensions required by Composer packages
RUN apk add --no-cache \
    git unzip zip curl \
    icu-dev libpq-dev oniguruma-dev libzip-dev \
    && docker-php-ext-install \
        intl pdo_pgsql mbstring zip

# Composer binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

# Install vendor deps
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

# Runtime PHP extensions (same as vendor stage)
RUN apk add --no-cache \
    icu-dev libpq-dev oniguruma-dev libzip-dev \
    && docker-php-ext-install \
        intl pdo_pgsql mbstring opcache zip

WORKDIR /var/www/html

# Copy Laravel source
COPY . .

# Inject vendor + assets
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

# Caddy config
COPY docker/frankenphp/Caddyfile.prod /etc/frankenphp/Caddyfile

# Laravel permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80 443

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
