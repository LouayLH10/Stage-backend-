#!/bin/sh
set -e

composer install --no-interaction --prefer-dist --optimize-autoloader

# Attendre que MySQL soit disponible
until nc -z mysql 3306; do
    echo "En attente de MySQL..."
    sleep 1
done

php artisan key:generate --force
php artisan migrate --force
php artisan optimize:clear

exec php artisan serve --host=0.0.0.0 --port=8000