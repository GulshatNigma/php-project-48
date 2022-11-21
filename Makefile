lint:
		composer exec --verbose phpcs -- --standard=PSR12 src bin tests

test:
		composer exec --verbose phpunit tests

install:
		composer install

validate:
		composer validate
