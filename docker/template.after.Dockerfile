RUN mkdir drivers \
    && cd drivers \
    && curl -L "https://github.com/dbrekelmans/browser-driver-installer/releases/download/0.3/bdi.phar" -o bdi.phar \
    && chmod +x bdi.phar \
    && ./bdi.phar \
    && rm bdi.phar

CMD ./server
