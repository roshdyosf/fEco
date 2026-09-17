#!/bin/sh

set -eu

cd /var/www/html

envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

php artisan migrate --force
php artisan config:cache
php artisan route:cache

php-fpm -D
exec nginx -g 'daemon off;'
