#!/bin/sh
set -e

# Ne copier .env que s’il n’existe pas
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || true
fi

# Créer les dossiers nécessaires
mkdir -p storage/framework/views storage/framework/cache storage/logs bootstrap/cache

# Copier le dossier options vers storage/app/public s'il existe
if [ -d "/var/www/options" ]; then
    echo "Copying options directory to storage/app/public..."
    cp -r /var/www/options /var/www/storage/app/public/ 2>/dev/null || true
    chown -R www-data:www-data /var/www/storage/app/public/options 2>/dev/null || true
    chmod -R 775 /var/www/storage/app/public/options 2>/dev/null || true
fi

# Fix permissions (Linux uniquement, ignore erreurs Windows)
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Installer composer
composer install --no-interaction --optimize-autoloader

# Ne pas forcer key:generate si .env existe déjà
if ! grep -q "APP_KEY=" .env; then
    php artisan key:generate --force
fi

# Lancer migrations (optionnel)
php artisan migrate --force || true

# Créer lien storage
php artisan storage:link || true

# Lancer PHP-FPM
exec php-fpm