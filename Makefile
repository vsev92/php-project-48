install:
	composer install
gendiff:
	./bin/gendiff
test-coverage-clover:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover ./build/logs/clover.xml 
test-coverage-html:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-html ./build/reports --coverage-filter ./src
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
