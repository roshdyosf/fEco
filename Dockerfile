FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm install

COPY . .
RUN npm run build

FROM php:8.4-fpm-bookworm AS app

ENV APP_ENV=production \
    PORT=10000

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        git \
        gettext-base \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        mbstring \
        pcntl \
        pdo_mysql \
        pdo_pgsql \
        zip \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
COPY . .
RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader

COPY --from=frontend /app/public/build ./public/build
COPY docker/nginx/default.conf /etc/nginx/templates/default.conf.template
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
