install:
	composer install
test:
	composer exec --verbose phpunit tests
test-coverage-clover:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover ./build/logs/clover.xml --coverage-filter ./src
test-coverage-html:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-html ./build/reports --coverage-filter ./src
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
