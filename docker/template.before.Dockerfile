FROM php:7.4-cli-buster as base-runner

ARG proxy_server_version=0.5

WORKDIR /app

ENV PANTHER_NO_SANDBOX=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev nano zip \
    && docker-php-ext-install pcntl zip > /dev/null \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock /app/
COPY bin /app/bin
COPY src /app/src
COPY phpunit.run.xml /app

RUN composer check-platform-reqs --ansi \
    && composer install --prefer-dist --no-dev \
    && composer clear-cache

RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.json --output composer.json
RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.lock --output composer.lock
RUN composer check-platform-reqs --ansi \
    && rm composer.json \
    && rm composer.lock \
    && rm /usr/bin/composer \
    && curl https://github.com/webignition/docker-tcp-cli-proxy/releases/download/${proxy_server_version}/server.phar -L --output ./server \
    && chmod +x ./server
