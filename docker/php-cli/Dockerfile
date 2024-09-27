FROM php:8.2-cli-alpine

# Install the required packages via apk
RUN apk add --no-cache \
    libpq \
    libpq-dev \
    wget \
    zlib-dev \
    libmcrypt-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli zip

# Install Composer
RUN wget https://getcomposer.org/installer -O - -q | php -- --install-dir=/bin --filename=composer --quiet

# Set the working directory
WORKDIR /