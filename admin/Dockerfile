FROM php:8.4-cli-alpine

RUN apk add --no-cache \
    curl \
    libzip-dev \
    nodejs \
    npm \
    autoconf \
    g++ \
    make \
    openssl-dev \
    linux-headers

RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN docker-php-ext-install zip pcntl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Run composer natively in the container building process to ensure extensions match
RUN composer install --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 3000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=3000"]
