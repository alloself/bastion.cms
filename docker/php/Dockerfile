FROM php:8.3-fpm

# Устанавливаем системные зависимости
RUN set -eux; \
    if [ -f /etc/apt/sources.list ]; then \
        sed -i 's|http://|https://|g' /etc/apt/sources.list; \
    fi; \
    if [ -d /etc/apt/sources.list.d ]; then \
        find /etc/apt/sources.list.d -maxdepth 1 -type f -exec sed -i 's|http://|https://|g' {} +; \
    fi; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        curl \
        build-essential \
        autoconf \
        ca-certificates \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        zip \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        jpegoptim optipng pngquant gifsicle \
        vim \
        nano \
        supervisor

# Очищаем кеш apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Устанавливаем PHP расширения
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Устанавливаем Redis расширение
RUN pecl install redis && docker-php-ext-enable redis

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаем пользователя для Laravel
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Копируем существующие права доступа приложения
COPY --chown=www:www . /var/www

# Меняем текущего пользователя на www
USER www

# Открываем порт 9000 и запускаем php-fpm сервер
EXPOSE 9000
CMD ["php-fpm"] 