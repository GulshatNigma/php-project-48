lint:
		composer exec phpcs -- --standard=PSR12 src bin

test:
		composer exec phpunit tests

install:
		composer install

validate:
		composer validate
