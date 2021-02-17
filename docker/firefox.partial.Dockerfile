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
