lint:
	composer lint
	composer phpstan
	composer phpcs-check
	composer psalm
	composer phpstan

lint-autofix:
	composer phpcs-fix

test:
	composer test

test-coverage:
	composer test-coverage