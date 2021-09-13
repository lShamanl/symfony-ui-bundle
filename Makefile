lint:
	composer lint
	composer phpcs-check

lint-autofix:
	composer phpcs-fix

analyze:
	composer phpstan
	composer psalm

test:
	composer test

test-coverage:
	composer test-coverage

composer-install:
	composer install

composer-dump:
	composer dump-autoload

composer-update:
	composer update

composer-outdated:
	composer outdated