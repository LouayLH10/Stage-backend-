FROM php:8.2-fpm

# Dépendances système
RUN apt-get update && apt-get install -y \
    git curl zip unzip nano libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www
