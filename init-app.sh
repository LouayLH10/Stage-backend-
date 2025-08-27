#!/bin/bash
set -e

echo "Installation des dépendances Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "Attente de MySQL..."
while ! nc -z mysql 3306; do
  sleep 1
done

echo "MySQL est disponible!"

if [ ! -f .env ]; then
    echo "Création du fichier .env..."
    cp .env.example .env
fi

echo "Génération de la clé d'application..."
php artisan key:generate --force

echo "Exécution des migrations..."
php artisan migrate --force

echo "Nettoyage du cache..."
php artisan optimize:clear

echo "Initialisation terminée!"