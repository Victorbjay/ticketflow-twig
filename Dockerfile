# syntax=docker/dockerfile:1
FROM php:8.1-cli-alpine

# Tools Composer needs for download/extract
RUN apk add --no-cache git unzip

# Composer (official)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Install PHP deps first for caching
COPY composer.json composer.lock* /app/
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Copy the rest of the code
COPY . /app

# Serve /public via PHP built-in server
ENV PORT=8000
EXPOSE 8000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t public"]
