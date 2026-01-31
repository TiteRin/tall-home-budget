##############################
# Stage 1 — Composer vendor  #
##############################
FROM dunglas/frankenphp:latest AS vendor

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libicu-dev libpq-dev libonig-dev \
    && docker-php-ext-install intl pdo_pgsql mbstring \
    && rm -rf /var/lib/apt/lists/*

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
FROM dunglas/frankenphp:latest AS runtime

RUN apt-get update && apt-get install -y \
    libicu-dev libpq-dev libonig-dev \
    && docker-php-ext-install intl pdo_pgsql mbstring opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

COPY docker/frankenphp/Caddyfile.prod /etc/frankenphp/Caddyfile
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
