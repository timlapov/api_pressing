FROM php:8.2-fpm-alpine

# Install necessary packages using apk
RUN apk add --no-cache \
    libpq \
    libpq-dev \
    wget \
    zlib-dev \
    libzip-dev \
    openssl-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /