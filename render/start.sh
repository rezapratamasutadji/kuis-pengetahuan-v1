#!/usr/bin/env sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

php artisan optimize:clear

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php artisan migrate --force
fi

if [ "${RUN_DB_SEED:-false}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan storage:link || true

sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf

exec apache2-foreground
