#!/bin/sh
# entrypoint.sh

# Copier .env
cp .env.example .env || true

# Créer tous les dossiers nécessaires
mkdir -p storage/framework/views storage/framework/cache storage/logs bootstrap/cache

# Permissions correctes
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Installer composer
composer install --no-interaction --optimize-autoloader

# Générer key et liens de stockage
php artisan key:generate
php artisan storage:link

# Lancer PHP-FPM
php-fpm
