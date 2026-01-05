FROM php:8.4-fpm

RUN apt update
RUN apt install git zip unzip -y

RUN docker-php-ext-install pdo_mysql
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Installation de composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ALLOW_SUPERUSER=1