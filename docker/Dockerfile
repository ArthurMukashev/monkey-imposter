FROM php:8.4-fpm

# Установка системных зависимостей и расширений
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    libcurl4-openssl-dev \
    gnupg \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        gd \
        intl \
        mbstring \
        exif \
        zip \
        opcache \
        curl \
        xml \
        bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Node.js (опционально, для фронтенда)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Создание рабочей директории
WORKDIR /var/www

# Копирование файлов проекта (зависимостей)
COPY ../composer.json ../composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-dev --prefer-dist

# Копирование остального кода
COPY ../ ./

# Генерация автозагрузки и оптимизация
RUN composer dump-autoload --optimize \
    && composer run-script post-autoload-dump \
    && php artisan optimize:clear

# Настройка прав
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Копирование конфига php.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000

CMD ["php-fpm"]
