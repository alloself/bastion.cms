# Используем официальный образ Node.js
FROM node:23

# Устанавливаем рабочую директорию
WORKDIR /app

# Копируем package.json и package-lock.json
COPY package*.json ./

# Устанавливаем зависимости
RUN npm install

# Копируем остальные файлы проекта
COPY . .

# Открываем порт, на котором будет работать Vite
EXPOSE 5173

CMD ["sh", "-c", "npm ci || npm install && npm run dev -- --host"]
