FROM php:7.4.26-cli-alpine3.14

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

    # For bin/composer
    && ln -s /usr/local/bin/php /usr/local/bin/php7.4 \

    # Purge
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/*

WORKDIR /app
