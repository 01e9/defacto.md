FROM node:lts-alpine as js

WORKDIR /project

COPY package.json package-lock.json tsconfig.json webpack.config.js ./
COPY assets ./assets
COPY public ./public
RUN ls -la . && ls -la ./assets

RUN mkdir -p public/build
RUN npm install
RUN npm run build

FROM php:7.3-fpm

ARG COMPOSER_VERSION='1.9.0'
ARG TINI_VERSION='0.18.0'

WORKDIR /project

COPY . ./

COPY --from=js /project/public/build ./public/build

RUN apt-get update \
    && apt-get install -y wget unzip nano \
    && apt-get install -y \
        libzip-dev libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev libicu-dev libpq-dev \
    && docker-php-ext-install -j$(nproc) zip mbstring json gd iconv pcntl intl pdo pdo_pgsql \
    && apt-get install -y nginx \
        && ln -sf /dev/stdout /var/log/nginx/access.log \
        && ln -sf /dev/stderr /var/log/nginx/error.log \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

ADD https://github.com/composer/composer/releases/download/${COMPOSER_VERSION}/composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

ADD https://github.com/krallin/tini/releases/download/v${TINI_VERSION}/tini /tini
RUN chmod +x /tini

COPY docker-image/nginx.conf /etc/nginx/sites-enabled/default

RUN mkdir -p /sock
RUN rm /usr/local/etc/php-fpm.d/*
COPY docker-image/php.conf /usr/local/etc/php-fpm.d/

RUN composer install && bin/console cache:clear --no-warmup

RUN mkdir -p var/cache var/log && chown www-data:www-data var/*

RUN chown www-data:www-data uploads/*

RUN echo "APP_ENV=prod" > .env

VOLUME [ \
    "/project/var/cache", \
    "/project/var/log", \
    "/project/uploads/blog", \
    "/project/uploads/editor", \
    "/project/uploads/parties", \
    "/project/uploads/politicians" \
]

EXPOSE 80

STOPSIGNAL SIGTERM

ENTRYPOINT ["/tini", "--"]

CMD ["bash", "-c", "nginx && php-fpm -F"]
