FROM webdevops/php-nginx:8.1-alpine

ARG version=unspecified

ENV APP_ENV=prod
ENV HIPPY_APP_VERSION=$version

WORKDIR /app

COPY deployment/nginx.conf /opt/docker/etc/nginx/conf.d/10-buffering.conf
COPY deployment/nginx-log.conf /opt/docker/etc/nginx/vhost.common.d/10-log.conf
COPY deployment/php.conf /opt/docker/etc/php/fpm/pool.d/docker.conf
COPY . .

RUN mkdir -p /var/cache/api
RUN chmod -R 777 /var/cache
RUN mkdir -p /var/log/api
RUN mkdir -p /var/log/php
RUN chmod -R 777 /var/log
