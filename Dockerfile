FROM php:8.1-apache

RUN a2enmod rewrite

COPY .docker/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY vendor /var/www/vendor
COPY public/index.php /var/www/html/index.php
COPY src /var/www/src
COPY app/dist /var/www/html


