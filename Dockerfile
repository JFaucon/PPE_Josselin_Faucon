FROM php:8.1-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    git libicu-dev libxml2-dev libzip-dev unixodbc-dev

RUN docker-php-ext-configure intl
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions pdo opcache intl zip dom mbstring

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN pecl install sqlsrv && docker-php-ext-enable sqlsrv
RUN pecl install pdo_sqlsrv && docker-php-ext-enable pdo_sqlsrv
RUN curl https://packages.microsoft.com/config/ubuntu/20.04/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN curl https://packages.microsoft.com/keys/microsoft.asc |  tee /etc/apt/trusted.gpg.d/microsoft.asc
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18

USER www-data
WORKDIR /var/www/html

COPY src ./src
COPY composer.json composer.lock .env ./
COPY public ./public
COPY templates ./templates
COPY config ./config

ENV APP_ENV prod
ENV APP_DEBUG 0

RUN composer install --no-interaction