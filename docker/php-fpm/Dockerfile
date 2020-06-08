FROM phpdockerio/php73-fpm:latest
WORKDIR /application

ENV DEBIAN_FRONTEND noninteractive
# Install selected extensions and other stuff
RUN echo "deb http://ppa.launchpad.net/stesie/libv8/ubuntu bionic main" > /etc/apt/sources.list.d/stesie-libv8.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 1A10946ED858A0DF \
    && apt-get update \
    && apt-get -y --no-install-recommends install build-essential git php-memcached php7.3-mysql php-xdebug php7.3-gd php7.3-intl php-yaml php7.3-dev libv8-7.5 libv8-7.5-dev \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN cd /tmp && \
    git clone https://github.com/phpv8/v8js.git && \
    cd v8js && \
    phpize && \
    ./configure --with-v8js=/opt/libv8-7.5 LDFLAGS="-lstdc++" && \
    make && \
    make test && \
    make install

RUN echo "extension = v8js.so" | tee -a /etc/php/7.3/mods-available/v8js.ini
RUN phpenmod v8js