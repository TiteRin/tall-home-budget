##############################
# Stage 1 — Composer vendor  #
##############################
FROM dunglas/frankenphp:latest-php8.4 AS vendor

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
FROM dunglas/frankenphp:latest-php8.4 AS runtime

WORKDIR /var/www/html

COPY . .

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

COPY docker/frankenphp/Caddyfile.prod /etc/frankenphp/Caddyfile

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80 443

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
