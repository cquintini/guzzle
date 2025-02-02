FROM composer:latest as setup

WORKDIR /guzzle

RUN set -xe \
    && composer init --name=Guzzle6Http/test --description="Simple project for testing Guzzle scripts" --author="Márk Sági-Kazár <mark.sagikazar@gmail.com>" --no-interaction \
    && composer require Guzzle6Http/guzzle


FROM php:7.3

WORKDIR /guzzle

COPY --from=setup /guzzle /guzzle
