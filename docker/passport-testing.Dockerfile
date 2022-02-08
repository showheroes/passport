#
# PHP Dependencies
#
FROM composer:2.1 as vendor

VOLUME ${COMPOSER_HOME:-$HOME/.composer} /tmp

ARG COMPOSER_AUTH

# Required to scan the 'seeds' directory by Composer.
WORKDIR /app
COPY ./ /app/

RUN composer update --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --prefer-dist

#
# NGINX + PHP-FPM IMAGE
#
FROM alpine:3.14
LABEL Maintainer="Evgeny Leksunin <evgeny.leksunin@showheroes.com>" \
      Description="ShowHeroes Passport Service within PHP-FPM + NGINX container."

# Install basic OS dependencies
RUN apk add imagemagick-dev nginx supervisor curl

# Install packages & PHP dependencies
RUN apk add php8-dev php8-pecl-imagick php8-pecl-xdebug php8-fpm php8-mysqli php8-json php8-openssl php8-curl \
    php8-zlib php8-fileinfo php8-xml php8-simplexml php8-zip php8-iconv php8-phar php8-intl php8-dom php8-xmlreader php8-xmlwriter php8-ctype php8-session \
    php8-mbstring php8-pdo php8-pdo_mysql php8-gd php8-tokenizer php8-bcmath php8-redis php8-pcntl php8-sockets \
    ; ln -s /usr/bin/php8 /usr/bin/php

# Configure nginx
COPY docker/configs/nginx.conf /etc/nginx/nginx.conf
# Remove default server definition
RUN rm -f /etc/nginx/conf.d/default.conf

# Configure PHP-FPM
COPY docker/configs/fpm-pool.conf /etc/php8/php-fpm.d/www.conf
COPY docker/configs/php-custom.ini /etc/php8/conf.d/custom.ini
COPY docker/configs/php-fpm.conf /etc/php8/fpm/php-fpm.conf
COPY docker/configs/php-fpm.ini /etc/php8/fpm/php-fpm.ini
# Configure default CLI (dev mode)
COPY docker/configs/php-cli-dev.ini /etc/php8/php.ini

# Configure supervisord
COPY docker/configs/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup document root
RUN mkdir -p /www/app

# Add application and optimise it
WORKDIR /www/app

COPY --from=vendor /app/ /www/app/
COPY docker/wait-for /www/app

# Expose the port nginx is reachable on
EXPOSE 80

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
