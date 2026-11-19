# syntax=docker/dockerfile:1

# ---------- 1. Dependensi PHP ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress \
        --prefer-dist --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction --no-scripts

# ---------- 2. Aset frontend (Vite + Tailwind v4) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources

# app.css memakai @source ke view pagination Laravel, jadi folder itu ikut disalin.
COPY --from=vendor \
    /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views \
    ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views

RUN mkdir -p public/build && npm run build

# ---------- 3. Runtime (php-fpm + nginx + scheduler) ----------
FROM php:8.3-fpm-alpine

ENV TZ=Asia/Jakarta

RUN apk add --no-cache nginx supervisor curl su-exec tzdata icu-libs libzip sqlite-libs \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS icu-dev libzip-dev sqlite-dev \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql pdo_sqlite opcache intl bcmath zip \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-silakes.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-silakes.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/entrypoint \
    && sed -i 's/\r$//' /usr/local/bin/entrypoint

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        storage/app/private \
        storage/app/public \
        bootstrap/cache \
    && rm -f bootstrap/cache/*.php \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["serve"]