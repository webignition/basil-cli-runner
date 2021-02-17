FROM php:7.4-cli-buster as base-runner

ARG proxy_server_version=0.5

WORKDIR /app

ENV PANTHER_NO_SANDBOX=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev nano zip \
    && docker-php-ext-install pcntl zip > /dev/null \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN echo "Checking runner platform requirements"
COPY composer.json /app
COPY composer.lock /app
RUN composer check-platform-reqs --ansi

RUN echo "Installing runner"
COPY bin /app/bin
COPY src /app/src
COPY phpunit.run.xml /app
RUN composer install --prefer-dist --no-dev
RUN composer clear-cache

RUN echo "Checking proxy server platform requirements ${proxy_server_version}"
RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.json --output composer.json
RUN curl https://raw.githubusercontent.com/webignition/docker-tcp-cli-proxy/${proxy_server_version}/composer.lock --output composer.lock
RUN composer check-platform-reqs --ansi
RUN rm composer.json
RUN rm composer.lock
RUN rm /usr/bin/composer

RUN echo "Fetching proxy server ${proxy_server_version}"
RUN curl -L https://github.com/webignition/docker-tcp-cli-proxy/releases/download/${proxy_server_version}/server.phar --output ./server
RUN chmod +x ./server
FROM base-runner AS firefox-runner
RUN echo 'deb http://deb.debian.org/debian/ unstable main contrib non-free' >> /etc/apt/sources.list \
    && echo 'Package: *' >> /etc/apt/preferences.d/99pin-unstable \
    && echo 'Pin: release a=stable' >> /etc/apt/preferences.d/99pin-unstable \
    && echo 'Pin-Priority: 900' >> /etc/apt/preferences.d/99pin-unstable \
    && echo 'Package: *' >> /etc/apt/preferences.d/99pin-unstable \
    && echo 'Pin release a=unstable' >> /etc/apt/preferences.d/99pin-unstable \
    && echo 'Pin-Priority: 10' >> /etc/apt/preferences.d/99pin-unstable

RUN apt-get update \
    && apt-get install -y --no-install-recommends -t unstable firefox libgcc-8-dev gcc-8-base libmpx2 jq \
    ; apt-get install -y --no-install-recommends -t unstable firefox

# The above installation of firefox uninstalls libzip-dev, zlib1g-dev
# Re-install to unbreak php zip extension
RUN apt-get install -y --no-install-recommends -t unstable libzip-dev zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*
RUN mkdir drivers \
    && cd drivers \
    && curl -L "https://github.com/dbrekelmans/browser-driver-installer/releases/download/0.3/bdi.phar" -o bdi.phar \
    && chmod +x bdi.phar \
    && ./bdi.phar \
    && rm bdi.phar

RUN apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

CMD ./server
