FROM php:8.0.13-cli-alpine3.14

COPY --from=composer:2.1.12 /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_HOME=/composer

RUN \
    apk update \
    && apk upgrade \
    && apk add --no-cache \
        bash \
        cloc \
        jq \
        git \

    # Purge
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/*

WORKDIR /app
