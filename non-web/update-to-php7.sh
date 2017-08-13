#!/bin/bash

sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y libapache2-mod-php7.0
sudo a2dismod php5
sudo a2enmod php7.0

sudo apt-get install -y \
  php7.0-dom \
  php7.0-curl \
  php7.0-xml \
  php7.0-mbstring \
  php7.0-zip \
  php7.0-pgsql

service apache2 restart

