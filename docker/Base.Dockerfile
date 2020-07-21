FROM php:7.4-cli-buster

WORKDIR /app

COPY bin /app/bin
COPY src /app/src
COPY composer.json /app
COPY composer.lock /app
COPY phpunit.run.xml /app

RUN apt-get update \
    && apt-get install -y libzip-dev nano zip \
    && docker-php-ext-install zip > /dev/null

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer check-platform-reqs --ansi --no-dev
RUN composer install --ansi --prefer-dist --no-dev