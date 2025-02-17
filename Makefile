.PHONY: app mysql logs

# Получение интерактивной оболочки в контейнере приложения (название сервиса «app»)
app:
	docker-compose exec app bash

# Получение интерактивной оболочки в контейнере базы данных (название сервиса «mysql»)
mysql:
	docker-compose exec mysql bash

# Просмотр логов контейнера приложения
logs:
	docker-compose logs -f app
