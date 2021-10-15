phpmetrics:
	./vendor/bin/phpmetrics --report-html=var/myreport ./src

lint:
	composer lint
	composer phpcs-check

lint-autofix:
	composer phpcs-fix

analyze:
	composer phpstan
	composer psalm

test:
	./vendor/bin/phpunit

test-coverage:
	./vendor/bin/phpunit --coverage-clover var/clover.xml --coverage-html var/coverage

test-unit-coverage:
	./vendor/bin/phpunit --testsuite=unit --coverage-clover var/clover.xml --coverage-html var/coverage

test-unit:
	./vendor/bin/phpunit --testsuite=unit

test-integration:
	./vendor/bin/phpunit --testsuite=integration

composer-install:
	composer install

composer-dump:
	composer dump-autoload

composer-update:
	composer update

composer-outdated:
	composer outdated