lint:
	composer lint
	composer phpcs-check

lint-autofix:
	composer phpcs-fix

analyze:
	composer psalm
	composer phpstan

test:
	composer test

test-coverage:
	composer test-coverage