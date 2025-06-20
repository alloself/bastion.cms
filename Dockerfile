FROM php:8.3-fpm

# Установка необходимых пакетов и расширений
RUN apt-get update && apt-get install -y procps \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev

# Конфигурация и установка расширения gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

# Установка дополнительных расширений для обработки больших данных
RUN docker-php-ext-install opcache bcmath

# Создание пользователя с UID 1000
RUN groupadd -g 1000 laravel && useradd -u 1000 -g laravel -m -s /bin/bash laravel

# Установка рабочей директории
WORKDIR /var/www/html

# Копирование файлов проекта
COPY . /var/www/html

COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# Настройка прав доступа для storage и bootstrap/cache
RUN chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Установка зависимостей Laravel
RUN composer install

EXPOSE 9000

CMD ["php-fpm"]
