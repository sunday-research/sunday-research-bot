FROM php:8.2.9-fpm-alpine3.18

ENV USER=docker
ENV UID=1000

ENV MUSL_LOCPATH="/usr/share/i18n/locales/musl"
ENV LC_ALL="ru_RU.UTF-8"

WORKDIR /app

EXPOSE 9000

# Install Composer
RUN apk update \
    && apk add --no-cache \
        unzip \
        git \
    && set -o pipefail && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony CLI
RUN apk update \
    && apk add --no-cache bash \
    && set -o pipefail && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli

# Install PHP Extensions
RUN apk update \
    && apk add --no-cache \
        icu-dev \
        icu-data-full \
        postgresql-dev \
        $PHPIZE_DEPS \
        linux-headers \
        gettext-dev \
        musl-locales \
        musl-locales-lang \
    && docker-php-ext-install \
        intl \
        pdo_pgsql \
        gettext \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable \
        intl \
        redis \
    && docker-php-ext-configure pcntl --enable-pcntl

# Generate locale
RUN /usr/bin/locale

# Set specified user
RUN adduser \
    --disabled-password \
    --gecos "" \
    --uid "$UID" \
    "$USER"

USER "$USER"

# Copy php-fpm config
COPY www.conf /usr/local/etc/php-fpm.d/www.conf
