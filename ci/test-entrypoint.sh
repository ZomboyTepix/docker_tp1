#!/bin/sh
set -eu

report_dir="${TEST_REPORT_DIR:-/reports}"

# On retire l'ancien rapport pour ne pas le confondre avec le nouveau.
mkdir -p "$report_dir"
rm -f "$report_dir/junit.xml"

# exec transmet les signaux à PHP et conserve le résultat de PHPUnit.
exec php -d memory_limit=128M /usr/local/bin/phpunit.phar \
    --configuration /app/phpunit.xml \
    --testdox \
    --log-junit "$report_dir/junit.xml" \
    "$@"
