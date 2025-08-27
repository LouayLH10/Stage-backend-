FROM php:8.2-fpm

# Dépendances système
RUN apt-get update && apt-get install -y \
    git curl zip unzip nano libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# Créer un utilisateur non-root 'www'
RUN useradd -G www-data,root -d /var/www www && \
    chown -R www:www /var/www

# Passer sur cet utilisateur
USER www
WORKDIR /var/www

# Copier php.ini
COPY ./docker/php/php.ini $PHP_INI_DIR/php.ini

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le cache Composer dans /tmp pour éviter open_basedir
ENV COMPOSER_CACHE_DIR=/tmp/composer

# Installer les dépendances Composer si composer.json existe
COPY --chown=www:www composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader || true

# Lancer storage:link automatiquement si le dossier storage existe
RUN if [ -d storage ]; then php artisan storage:link || true; fi

# Exposer le port PHP-FPM
EXPOSE 9000

# Commande par défaut
CMD ["php-fpm"]
