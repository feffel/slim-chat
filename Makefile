build:
	docker-compose build
up:
	docker-compose up
stop:
	docker-compose stop
bash:
	docker-compose exec chat bash
install:
	docker-compose exec chat composer install
test:
	docker-compose exec chat composer test
