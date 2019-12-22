FROM php:7.4.1-cli

RUN apt-get update && apt-get -y install git libzip-dev zip unzip
RUN docker-php-ext-install zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /var/www/html

CMD php -S 0.0.0.0:80 -t public public/index.php --timeout=0
