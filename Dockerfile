FROM php:8.2-apache

RUN set -ex;\
    apt-get update; \
	apt-get install -y --no-install-recommends \
        libpq-dev \
		libfreetype6-dev \
		libjpeg-dev \
		libmagickwand-dev \
		libpng-dev \
		libwebp-dev \
		libzip-dev \
        rsync \
        sudo \
		ssl-cert \
        ssh-tools \
		postgresql-client \
		less \
        micro

RUN docker-php-ext-install bcmath exif gd pgsql zip