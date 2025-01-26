# docker/node/Dockerfile

# Этап 1: Сборка фронтенда
FROM node:23-alpine AS builder

# Установите системные зависимости
RUN apk update && apk add --no-cache \
    bash \
    git \
    make \
    g++ \
    python3

# Установите рабочую директорию
WORKDIR /app

# Копируйте файлы package.json и package-lock.json
COPY package*.json ./

# Установите зависимости
RUN npm install

# Копируйте остальные файлы проекта
COPY . .

# Соберите фронтенд
# RUN npm run build

# Этап 2: Финальный образ (опционально)
FROM node:23-alpine

# Установите рабочую директорию
WORKDIR /app

# Копируйте только собранные файлы из сборочного образа
COPY --from=builder /app/dist /app/dist

# Экспонируйте порт (если необходимо)
EXPOSE 3000

# Команда по умолчанию (опционально)
CMD ["npm", "run", "serve"]
