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

    # Install xdebug
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps \

    # Install infection
    && composer global require infection/infection:0.25.* \
    && ln -s ${COMPOSER_HOME}/vendor/bin/infection /usr/local/bin/infection \

    # Configure Composer
    && chmod -R 777 ${COMPOSER_HOME} \

    # Purge
    && rm -rf /var/cache/apk/* \
    && rm -rf /tmp/*

WORKDIR /app
