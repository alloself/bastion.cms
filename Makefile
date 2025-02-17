.PHONY: app mysql logs clear-log

# Получение интерактивной оболочки в контейнере приложения (название сервиса «app»)
app:
	docker-compose exec app bash

# Получение интерактивной оболочки в контейнере базы данных (название сервиса «mysql»)
mysql:
	docker-compose exec mysql bash

# Просмотр логов контейнера приложения
logs:
	docker-compose logs -f app

clear-log:
	docker-compose exec app sh -c "rm -f storage/logs/laravel.log && touch storage/logs/laravel.log"

init: 
	docker-compose exec app bash && php artisan migrate:fresh --seed