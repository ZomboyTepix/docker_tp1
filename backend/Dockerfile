FROM php:8.2-fpm-alpine AS backend

# Conserver les bibliothèques d'exécution et retirer les outils de compilation.
RUN apk add --no-cache git unzip curl icu-libs libzip \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev \
    && docker-php-ext-install intl zip opcache pdo_mysql \
    && apk del .build-deps

RUN printf '%s\n' \
    'opcache.enable=1' \
    'opcache.memory_consumption=128' \
    'opcache.interned_strings_buffer=8' \
    'opcache.max_accelerated_files=4000' \
    'opcache.validate_timestamps=1' \
    'opcache.revalidate_freq=0' \
    > /usr/local/etc/php/conf.d/opcache.ini

RUN curl -fsSL https://getcomposer.org/installer -o /tmp/composer-setup.php \
    && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm /tmp/composer-setup.php

RUN addgroup -g 1000 symfony \
    && adduser -u 1000 -G symfony -D symfony

WORKDIR /var/www/html

COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction

COPY --chown=symfony:symfony . .

# Seules des valeurs d'exemple entrent dans l'image. Compose les remplace au run.
RUN cp .env.example .env \
    && composer dump-autoload --no-dev --optimize --no-interaction \
    && composer run-script post-install-cmd --no-interaction \
    && mkdir -p var/cache var/log \
    && chown -R symfony:symfony var/ \
    && touch /usr/local/etc/php/conf.d/zz-runtime.ini \
        /usr/local/etc/php-fpm.d/zz-runtime.conf \
    && chown symfony:symfony /usr/local/etc/php/conf.d/zz-runtime.ini \
        /usr/local/etc/php-fpm.d/zz-runtime.conf \
    && chmod +x docker-entrypoint.sh

ENV PHP_FPM_MAX_CHILDREN=2
ENV PHP_MEMORY_LIMIT=128M

USER symfony

EXPOSE 9000
STOPSIGNAL SIGTERM

ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
CMD ["php-fpm", "-F"]
