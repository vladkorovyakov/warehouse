FROM debian:bookworm-20240110-slim

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV PHP_VERSION 8.2

COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

RUN apt -q update && apt install -qy apt-transport-https lsb-release ca-certificates wget curl
RUN curl -o /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN sh -c 'echo "deb https://ftp.mpi-inf.mpg.de/mirrors/linux/mirror/deb.sury.org/repositories/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list'
RUN apt -q update
RUN apt install -qy --no-install-recommends \
    php${PHP_VERSION}-fpm php${PHP_VERSION}-cli php${PHP_VERSION}-xml php${PHP_VERSION}-intl php${PHP_VERSION}-curl \
    php${PHP_VERSION}-bcmath php${PHP_VERSION}-mbstring php${PHP_VERSION}-readline php${PHP_VERSION}-zip \
    php${PHP_VERSION}-pgsql php${PHP_VERSION}-igbinary php${PHP_VERSION}-iconv php${PHP_VERSION}-gd \
    ${ADDITIONAL_PACKAGES}

RUN apt autoremove && apt clean
RUN rm -r /var/lib/apt/lists/*
RUN cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime

RUN groupadd -g ${USER_ID} nginx && \
    useradd -u ${USER_ID} -g ${GROUP_ID} nginx && \
    mkdir -p /home/nginx/.composer /app &&\
    chown -R nginx:nginx /home/nginx /app

COPY ./docker/app/php/ /etc/php/${PHP_VERSION}/
COPY ./docker/app/app-entrypoint.sh /app-entrypoint.sh

RUN chmod +x /app-entrypoint.sh

WORKDIR /app

COPY composer.* symfony.* ./

RUN set -eux; \
    if [ -f composer.json ]; then \
		composer install --prefer-dist --no-autoloader --no-scripts --no-progress; \
		composer clear-cache; \
    fi

COPY --chown=nginx:nginx ./ ./

RUN set -eux; \
    if [ -f composer.json ]; then \
		composer dump-autoload --classmap-authoritative; \
		chmod +x bin/console; sync; \
    fi \
    && chown -R nginx:nginx *

EXPOSE 9000

CMD ["php-fpm8.2", "-F"]