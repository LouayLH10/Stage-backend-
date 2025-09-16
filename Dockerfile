FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# Copier php.ini personnalisé
COPY ./docker/php/php.ini $PHP_INI_DIR/php.ini

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer utilisateur non-root laraveluser
RUN useradd -ms /bin/bash -G www-data,root -u 1000 laraveluser \
    && mkdir -p /var/www/storage /var/www/bootstrap/cache /var/www/vendor \
    && touch /var/www/.env \
    && chown -R laraveluser:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/vendor \
    && chmod 664 /var/www/.env \
    && echo "umask 0002" >> /etc/profile \
    && echo "umask 0002" >> /home/laraveluser/.profile \
    && mkdir -p /var/www/.composer && chown -R laraveluser:www-data /var/www/.composer

# Définir le cache Composer dans un dossier autorisé par open_basedir
ENV COMPOSER_HOME=/var/www/.composer

# Définir le dossier de travail
WORKDIR /var/www

# Configurer Git pour accepter le changement de propriétaire
RUN git config --global --add safe.directory /var/www

# Copier le projet complet avec la bonne propriété
COPY --chown=laraveluser:www-data . /var/www

# Créer le dossier default_icons s'il n'existe pas et s'assurer des permissions
RUN mkdir -p /var/www/storage/app/public/options/default_icons && \
    chown -R laraveluser:www-data /var/www/storage/app/public/options && \
    chmod -R 775 /var/www/storage/app/public/options

# Passer à l'utilisateur non-root
USER laraveluser

# Exposer le port PHP-FPM
EXPOSE 9000

# Commande de lancement
CMD ["php-fpm"]