# Dockerfile
FROM php:8.1

WORKDIR /app

RUN apt update
RUN apt-get install git bash unzip p7zip -y

RUN curl -sS https://getcomposer.org/installer | php && \
    export COMPOSER_MEMORY_LIMIT=-1 && \
    ls && \
    php composer.phar self-update --2 && \
    mv composer.phar /usr/local/bin/composer

# Then init as: composer create-project symfony/skeleton .