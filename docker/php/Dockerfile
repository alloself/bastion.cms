# docker/php/Dockerfile

# Этап 1: Сборка зависимостей PHP
FROM php:8.3-fpm-alpine AS builder

# Установите системные зависимости
RUN apk update && apk add --no-cache \
    build-base \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    bash \
    postgresql-dev \
    nodejs \
    npm

# Установите расширения PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql

# Установите Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установите рабочую директорию
WORKDIR /var/www

# Копируйте файлы composer и package.json
COPY composer.lock composer.json package*.json ./

# Установите зависимости PHP
RUN composer install --no-dev --no-scripts --no-autoloader

# Установите зависимости npm и соберите фронтенд
# RUN npm install --legacy-peer-deps && npm run build

# Копируйте остальные файлы проекта
COPY . .

# Установите права на директории
RUN chown -R www-data:www-data storage bootstrap/cache

# Соберите автозагрузчик и оптимизируйте Laravel
RUN composer dump-autoload --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Этап 2: Финальный образ
FROM php:8.3-fpm-alpine

# Установите системные зависимости
RUN apk update && apk add --no-cache \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    postgresql-dev

# Установите расширения PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_pgsql

# Установите Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установите рабочую директорию
WORKDIR /var/www

# Копируйте только необходимые файлы из сборочного образа
COPY --from=builder /var/www /var/www

# Установите права
RUN chown -R www-data:www-data /var/www

# Очистите кэш apk и временные файлы
RUN apk del build-base nodejs npm && \
    rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

# Экспонируйте порт
EXPOSE 9000

# Запустите PHP-FPM от пользователя www-data
USER www-data

CMD ["php-fpm"]
