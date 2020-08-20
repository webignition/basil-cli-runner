# Browser to use in browser-based runner (--build-arg=<browser>)
ARG browser=chrome

# Base image containing app
FROM php:7.4-cli-buster as base-runner

ARG proxy_server_version=0.5

WORKDIR /app

ENV PANTHER_NO_SANDBOX=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev nano zip \
    && docker-php-ext-install pcntl zip > /dev/null \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer self-update --2

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

RUN rm /usr/local/bin/composer

RUN echo "Fetching proxy server ${proxy_server_version}"
RUN curl -L https://github.com/webignition/docker-tcp-cli-proxy/releases/download/${proxy_server_version}/server.phar --output ./server
RUN chmod +x ./server

CMD ./server

# Chrome-specific additions to base image
FROM base-runner AS chrome-runner

RUN curl https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb --output chrome.deb
RUN apt-get update \
    && apt-get install -y --no-install-recommends ./chrome.deb \
    && rm ./chrome.deb \
    && rm -rf /var/lib/apt/lists/*

RUN chmod +x vendor/symfony/panther/chromedriver-bin/update.sh
RUN cd vendor/symfony/panther/chromedriver-bin \
    && ./update.sh \
    && cd ../../../..

RUN apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Firefox-specific additions to base image
FROM base-runner AS firefox-runner
RUN echo 'deb http://deb.debian.org/debian/ unstable main contrib non-free' >> /etc/apt/sources.list
RUN echo 'Package: *' >> /etc/apt/preferences.d/99pin-unstable
RUN echo 'Pin: release a=stable' >> /etc/apt/preferences.d/99pin-unstable
RUN echo 'Pin-Priority: 900' >> /etc/apt/preferences.d/99pin-unstable
RUN echo 'Package: *' >> /etc/apt/preferences.d/99pin-unstable
RUN echo 'Pin release a=unstable' >> /etc/apt/preferences.d/99pin-unstable
RUN echo 'Pin-Priority: 10' >> /etc/apt/preferences.d/99pin-unstable

RUN apt-get update \
    && apt-get install -y --no-install-recommends -t unstable firefox libgcc-8-dev gcc-8-base libmpx2 jq \
    && rm -rf /var/lib/apt/lists/*

RUN chmod +x vendor/symfony/panther/geckodriver-bin/update.sh
RUN cd vendor/symfony/panther/geckodriver-bin \
    && ./update.sh \
    && cd ../../../..

RUN apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Conditionally run browser-specific additions
FROM ${browser}-runner AS after-condition

FROM after-condition
