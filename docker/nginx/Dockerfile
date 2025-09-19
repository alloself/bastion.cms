FROM nginx:latest

# Копируем конфигурационные файлы nginx из локального каталога "conf.d" в контейнер
COPY conf.d /etc/nginx/conf.d

# Открываем порт для HTTP
EXPOSE 80

# Запускаем nginx в режиме foreground
CMD ["nginx", "-g", "daemon off;"]
