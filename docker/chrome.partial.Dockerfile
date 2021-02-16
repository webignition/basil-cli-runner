RUN curl https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb --output chrome.deb \
    && apt-get update \
    && apt-get install -y ./chrome.deb \
    && rm ./chrome.deb \
    && rm -rf /var/lib/apt/lists/*

RUN chmod +x vendor/symfony/panther/chromedriver-bin/update.sh
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/* \
    && cd vendor/symfony/panther/chromedriver-bin \
    && ./update.sh \
    && cd ../../../..
