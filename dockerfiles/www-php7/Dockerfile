FROM php:7.0.14-apache

MAINTAINER Hallvard Nygård <hallvard.nygard@gmail.com>

RUN apt-get update && apt-get install -y \
    libcurl4-gnutls-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev

RUN docker-php-ext-install curl
RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd

RUN apt-get install -y git

RUN a2enmod rewrite
RUN a2enmod ssl

COPY dev.localhost.conf /etc/apache2/conf-enabled/dev.localhost.conf
COPY phpdev.ini /usr/local/etc/php/conf.d/phpdev.ini

COPY ssl /etc/ssl

CMD apache2-foreground
