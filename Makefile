install:
	composer install

validate:
	composer validate

lint:
	composer run-script phpcs -- --standard=phpcs.xml src

test:
	composer run-script phpunit tests

test-coverage:
	composer run-script phpunit tests -- --coverage-clover build/logs/clover.xml

docker-up:
	docker-compose down && docker-compose up -d --build
