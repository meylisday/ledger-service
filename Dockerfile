FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev zip curl \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www

COPY . .

RUN composer install