FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libonig-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl \
    && rm -rf /var/lib/apt/lists/*

# Copier php.ini
COPY ./docker/php/php.ini $PHP_INI_DIR/php.ini

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Créer utilisateur non-root et dossiers Laravel
RUN useradd -G www-data,root -u 1000 -d /var/www laraveluser \
    && mkdir -p /var/www/storage /var/www/bootstrap/cache /var/www/vendor \
    && chown -R laraveluser:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache /var/www/vendor

WORKDIR /var/www
USER laraveluser

# Copier le projet
COPY --chown=laraveluser:www-data . /var/www

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]
