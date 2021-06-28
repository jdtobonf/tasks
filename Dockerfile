FROM php:8.0-fpm

LABEL maintainer="jdtobonf@gmail.com"

# PHP extensions

RUN apt-get update && apt-get install -y libmcrypt-dev libpng-dev git zip libmagickwand-dev --no-install-recommends \
    &&  docker-php-ext-install pdo pdo_mysql gd \
    && pecl install imagick mcrypt \
    && docker-php-ext-enable imagick mcrypt \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/html
