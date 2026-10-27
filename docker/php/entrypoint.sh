#!/bin/sh
set -e

if [ ! -f /app/vendor/autoload.php ]; then
    composer install --no-interaction --no-progress --working-dir=/app
fi

exec docker-php-entrypoint "$@"
