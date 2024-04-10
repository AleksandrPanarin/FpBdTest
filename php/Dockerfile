# Add PHP-Apache base image
FROM php:8.3-apache

RUN a2enmod rewrite

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

WORKDIR /var/www/html
# Copy the source code in /www into the container at /var/www/html
COPY ./ /var/www/html