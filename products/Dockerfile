ARG IMAGES_REPOSITORY=
ARG TZ=Asia/Vladivostok
ARG ALPINE_VERSION=3.19


FROM ${IMAGES_REPOSITORY}alpine:${ALPINE_VERSION} AS base
# https://github.com/docker-library/php/blob/9c81d9730c629b700b88ef22c87839a9d6ab046b/8.2/alpine3.20/cli/Dockerfile
ARG TZ
ENV TZ=$TZ
RUN echo ${TZ} > /etc/timezone

RUN apk add --no-cache \
  linux-headers tzdata libpq-dev \
  php82 \
  php82-ctype \
  php82-xml \
  php82-mbstring \
  php82-sockets \
  php82-pcntl \
  php82-pgsql \
  php82-opcache \
  php82-posix \
  php82-curl \
  php82-pecl-redis \
  php82-xmlwriter \
  php82-phar \
  php82-dom \
  php82-fileinfo \
  php82-iconv \
  php82-tokenizer \
  php82-session \
  php82-openssl \
  php82-pdo \
  php82-pdo_pgsql \
  php82-pdo_sqlite \
  php82-fpm

RUN printf "[Date]\ndate.timezone = \"Asia/Vladivostok\"\n" > /etc/php82/conf.d/_timezone.ini

RUN set -eux; \
  adduser -u 82 -D -S -G www-data www-data

RUN mkdir -p /run/php && chown -R www-data:www-data /run/php


FROM ${IMAGES_REPOSITORY}nginx:alpine-slim AS web-server

ARG TZ
ENV TZ=$TZ
RUN echo ${TZ} > /etc/timezone
ARG ROOT=/var/www/html

RUN rm /etc/nginx/conf.d/default.conf
RUN set -eux; \
  adduser -u 82 -D -S -G www-data www-data


FROM base AS api-base

ARG ROOT=/var/www/html

RUN mkdir -p ${ROOT}

WORKDIR ${ROOT}

RUN mkdir -p ${ROOT}/storage/app/public
RUN mkdir -p ${ROOT}/storage/logs
RUN mkdir -p ${ROOT}/storage/framework/cache/data
RUN mkdir -p ${ROOT}/storage/framework/sessions
RUN mkdir -p ${ROOT}/storage/framework/views

COPY ./resources ${ROOT}/resources
COPY ./bootstrap ${ROOT}/bootstrap
COPY ./public ${ROOT}/public
COPY ./config ${ROOT}/config
COPY ./routes ${ROOT}/routes
COPY ./database ${ROOT}/database
COPY ./app ${ROOT}/app

COPY ./artisan ${ROOT}
COPY ./composer.json ${ROOT}
COPY ./composer.lock ${ROOT}

RUN --mount=type=bind,source=distr/composer,target=/usr/local/bin/composer \
  --mount=type=cache,target=/tmp/cache \
  composer install --no-interaction

RUN chown -R www-data:www-data ${ROOT}


FROM api-base AS api

COPY ./docker/api-entrypoint.sh /usr/local/bin/api-entrypoint

ENTRYPOINT ["api-entrypoint"]

CMD ["php-fpm82"]


FROM api-base AS seeder

COPY ./docker/seeder-entrypoint.sh /usr/local/bin/seeder-entrypoint

ENTRYPOINT ["seeder-entrypoint"]
