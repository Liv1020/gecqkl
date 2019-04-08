FROM php:5.5.38-apache

# Install modules
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libicu-dev \
        libxml2-dev \
        libssl-dev \
        git \
        ssh \
        cron \
                --no-install-recommends

RUN docker-php-ext-install mcrypt zip intl mbstring pdo_mysql mysql mysqli exif xmlrpc sockets \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd

RUN apt-get purge -y g++ \
    && apt-get autoremove -y \
    && rm -r /var/lib/apt/lists/* \
    && rm -rf /tmp/*

RUN cp /usr/share/zoneinfo/Asia/Shanghai /etc/localtime

COPY php.ini $PHP_INI_DIR/

ADD crontab /etc/cron.d/crontab

RUN crontab /etc/cron.d/crontab

ADD cmd.sh /root/

CMD ["/root/cmd.sh"]
