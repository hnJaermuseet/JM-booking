FROM php:5.5-apache

MAINTAINER Hallvard Nygård <hallvard.nygard@gmail.com>

RUN apt-get update && apt-get install -y libcurl4-gnutls-dev

RUN docker-php-ext-install curl mysql
RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN apt-get install -y git

RUN a2enmod rewrite
RUN a2enmod ssl

COPY dev.localhost.conf /etc/apache2/conf-enabled/dev.localhost.conf
COPY phpdev.ini /usr/local/etc/php/conf.d/phpdev.ini

COPY ssl /etc/ssl

CMD apache2-foreground
