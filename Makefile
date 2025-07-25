.PHONY: install up down test test-unit test-integration migrate fixtures

install:
	docker-compose exec php composer install

up:
	docker-compose up -d

down:
	docker-compose down

test:
	docker-compose exec php vendor/bin/phpunit

test-unit:
	docker-compose exec php vendor/bin/phpunit tests/Unit

test-integration:
	docker-compose exec php vendor/bin/phpunit tests/Integration

test-coverage:
	docker-compose exec php vendor/bin/phpunit --coverage-html coverage

migrate:
	docker-compose exec php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker-compose exec php bin/console doctrine:fixtures:load --no-interaction

cache-clear:
	docker-compose exec php bin/console cache:clear