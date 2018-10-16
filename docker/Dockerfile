FROM php:7.2-fpm

RUN apt-get update \
    && apt-get install -y wget unzip \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libmcrypt-dev libpng-dev libicu-dev libpq-dev \
    && docker-php-ext-install -j$(nproc) zip mbstring json gd iconv pcntl intl pdo pdo_pgsql \
    && pecl install redis xdebug \
        && docker-php-ext-enable redis xdebug \
        && echo 'xdebug.remote_autostart=1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && (wget https://raw.githubusercontent.com/composer/getcomposer.org/1b137f8bf6db3e79a38a5bc45324414a6b1f9df2/web/installer -O - -q \
        | php -- --install-dir=/usr/local/bin --filename=composer) \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
