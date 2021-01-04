FROM php:7.3-cli-alpine

MAINTAINER onnov@ya.ru

ARG UID=1001
ARG GID=1001

RUN apk add --no-cache --update bash git shadow gnu-libiconv g++ autoconf make pcre2-dev\
    && pecl install pcov \
    && docker-php-ext-install iconv \
    && docker-php-ext-enable pcov \
    && apk del --no-cache g++ autoconf make pcre2-dev \
    && rm -rf /tmp/* /var/cache/apk/* \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && usermod -u $UID www-data -s /bin/bash && groupmod -g $GID www-data \
    && rm -rf /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apk/* /usr/share/php7 \
    && chmod 0777 /var/log -R

## iconv hack https://github.com/docker-library/php/issues/240
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

WORKDIR /var/www/de
USER www-data
