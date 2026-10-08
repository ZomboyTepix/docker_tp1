FROM php:8.2-fpm-alpine AS backend

RUN apk add --no-cache \
    git \
    unzip \
    curl \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    linux-headers

RUN docker-php-ext-install intl zip opcache pdo pdo_mysql pdo_pgsql

RUN echo "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=4000\n\
opcache.validate_timestamps=1\n\
opcache.revalidate_freq=0\n\
opcache.fast_shutdown=1" > /usr/local/etc/php/conf.d/opcache.ini

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

RUN addgroup -g 1000 symfony && \
    adduser -u 1000 -G symfony -D symfony

WORKDIR /var/www/html

COPY composer.json ./

RUN composer install \
    --no-dev \
    --no-scripts \
    --optimize-autoloader \
    --no-interaction

COPY --chown=symfony:symfony . .

RUN composer run-script post-install-cmd --no-interaction 2>/dev/null || true

RUN mkdir -p var/cache var/log && \
    chown -R symfony:symfony var/

USER symfony

EXPOSE 9000

STOPSIGNAL SIGTERM

CMD ["php-fpm"]
