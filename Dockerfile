FROM php:8.1-apache

MAINTAINER Jasper Roel <jasperroel@gmail.com>

## Setup apache
RUN a2enmod rewrite headers
RUN a2dissite 000-default

# Install PostgreSQL PDO
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Setup the site
WORKDIR /var/www/html
COPY . .

# Setup error logging
COPY docker/php.ini $PHP_INI_DIR/conf.d/error-log.ini

EXPOSE 80