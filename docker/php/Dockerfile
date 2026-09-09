# Blue Studio OS — PHP-FPM application image (development + production-ready)
# Runtime: PHP 8.5 (Laravel 13 requirement: >= 8.3; project targets 8.5).
FROM php:8.5-fpm

# System deps required by the Laravel extensions we enable.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libonig-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        intl \
        bcmath \
        zip \
        gd \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer (managed, current stable).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Production opcache settings; local overrides via .env/php.ini if needed.
RUN docker-php-ext-enable opcache \
    && { \
        echo "opcache.enable=1"; \
        echo "opcache.memory_consumption=128"; \
        echo "opcache.max_accelerated_files=20000"; \
        echo "opcache.validate_timestamps=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
