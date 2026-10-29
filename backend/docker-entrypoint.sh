#!/bin/sh
set -eu

PHP_FPM_MAX_CHILDREN="${PHP_FPM_MAX_CHILDREN:-2}"
PHP_MEMORY_LIMIT="${PHP_MEMORY_LIMIT:-128M}"

if ! printf '%s\n' "$PHP_FPM_MAX_CHILDREN" | grep -Eq '^[1-9][0-9]*$'; then
    echo 'PHP_FPM_MAX_CHILDREN doit être un entier strictement positif.' >&2
    exit 1
fi

if ! printf '%s\n' "$PHP_MEMORY_LIMIT" | grep -Eq '^[1-9][0-9]*[KMG]?$'; then
    echo 'PHP_MEMORY_LIMIT doit être positif, avec une unité optionnelle K, M ou G.' >&2
    exit 1
fi

# Ces deux fichiers sont préparés au build pour être modifiables par symfony.
printf 'memory_limit=%s\n' "$PHP_MEMORY_LIMIT" \
    > /usr/local/etc/php/conf.d/zz-runtime.ini

printf '[www]\npm=ondemand\npm.max_children=%s\npm.process_idle_timeout=10s\n' \
    "$PHP_FPM_MAX_CHILDREN" > /usr/local/etc/php-fpm.d/zz-runtime.conf

# L'entrypoint officiel remplace ensuite le shell par la commande demandée.
exec docker-php-entrypoint "$@"
