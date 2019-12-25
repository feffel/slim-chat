ARGS?=
USER=$(shell id -u):$(shell id -g)
build:
	docker-compose build
up:
	docker-compose up -d
stop:
	docker-compose stop
bash:
	docker-compose exec chat bash
install:
	docker-compose exec -u $(USER) chat composer install
makemigration:
	docker-compose exec -u $(USER) chat vendor/bin/phinx create $(ARGS)
migrate:
	docker-compose exec chat vendor/bin/phinx migrate $(ARGS)
test:
	docker-compose exec chat vendor/bin/phpunit $(ARGS)
