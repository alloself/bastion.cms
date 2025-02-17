FROM nginx:latest

# Создаем папку для SSL-сертификатов и генерируем самоподписанный сертификат
RUN mkdir -p /etc/nginx/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
      -subj "/C=RU/ST=Moscow/L=Moscow/O=MyCompany/OU=IT/CN=localhost" \
      -keyout /etc/nginx/ssl/server.key \
      -out /etc/nginx/ssl/server.crt

# Копируем конфигурационные файлы nginx из локального каталога "conf.d" в контейнер
COPY conf.d /etc/nginx/conf.d

# Открываем порты для HTTP и HTTPS
EXPOSE 80
EXPOSE 443

# Запускаем nginx в режиме foreground
CMD ["nginx", "-g", "daemon off;"]
