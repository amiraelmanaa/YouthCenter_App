FROM composer:2 as builder
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader

FROM php:8.2-fpm
WORKDIR /app
COPY --from=builder /app .
CMD ["symfony", "serve", "--no-tls"]