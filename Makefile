MIN_MSI=100
MIN_COVERED_MSI=100

ifeq ("${CI}", "true")
	PARALLELISM=4
else
	PARALLELISM=$(shell nproc)
endif

.PHONY: valid test coding-standard-fix coding-standard static-analysis unit-test mutation-test

valid: coding-standard-fix coding-standard static-analysis test

test: unit-test mutation-test

vendor: composer.json
	composer install $(EXTRA_FLAGS)
	@touch -c vendor

coding-standard: vendor
	vendor/bin/phpcs --parallel=$(PARALLELISM)

coding-standard-fix: vendor
	vendor/bin/phpcbf --parallel=$(PARALLELISM) || true

static-analysis: vendor
	vendor/bin/phpstan analyse $(EXTRA_FLAGS)

unit-test: vendor
	vendor/bin/phpunit --testsuite unit --stop-on-error --stop-on-failure $(EXTRA_FLAGS)

mutation-test: vendor
	vendor/bin/infection --no-progress -j=$(PARALLELISM) -s --min-msi=$(MIN_MSI) --min-covered-msi=$(MIN_COVERED_MSI) $(EXTRA_FLAGS)
