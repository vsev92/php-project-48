install:
	composer install
gendiff:
	./bin/gendiff
test-coverage-clover:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover ./tests/reports/clover/report.xml --coverage-filter ./src
testCov:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-html ./tests/reports --coverage-filter ./src
validate:
	composer validate
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin