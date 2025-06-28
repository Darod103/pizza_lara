.PHONY: app restart logs up down  seed
# Вход в контейнер с Laravel (PHP)
app:
	docker exec -it laravel_app bash
# Полная пересборка и перезапуск контейнеров
restart:
	docker-compose down && docker-compose up -d --build

# Просмотр логов приложения
logs:
	docker logs -f laravel_app

# Запуск всех контейнеров
up:
	docker-compose up -d --build

# Остановка всех контейнеров
down:
	docker-compose down
