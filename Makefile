lint:
		composer exec --verbose phpcs -- --standard=PSR12 src bin tests

test:
		composer exec --verbose phpunit tests

install:
		composer install

validate:
		composer validate
		
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover ./build/logs/clover.xml
