DOCKER_COMPOSE=UID=$$(id -u) GID=$$(id -g) docker-compose

build: ## Build Docker dev container
build:
	$(DOCKER_COMPOSE) build

up: ## Launch Docker dev container
up:
	$(DOCKER_COMPOSE) up -d --force-recreate
	$(DOCKER_COMPOSE) exec -T php composer install
	sleep 10
	make build-database
	$(DOCKER_COMPOSE) exec -T php /bin/sh -c 'cd public && php -S 0.0.0.0:80'

bash: ## Open bash on Docker dev container
bash:
	$(DOCKER_COMPOSE) exec php $(if $(CMD), ${CMD}, /bin/sh)

down: ## Stop Docker dev container
down:
	$(DOCKER_COMPOSE) down

build-database: ## Drop and recreate database, and load fixtures.
build-database:
	$(DOCKER_COMPOSE) exec -T php ./bin/console doctrine:database:drop --force --env=dev --if-exists
	$(DOCKER_COMPOSE) exec -T php ./bin/console doctrine:database:create --env=dev
	$(DOCKER_COMPOSE) exec -T php ./bin/console doctrine:migrations:migrate --env=dev  --no-interaction


build-database@test: ## Drop and recreate database, and load fixtures.
build-database@test:
	$(DOCKER_COMPOSE) exec -T php ./bin/console doctrine:database:drop --force --env=test --if-exists
	$(DOCKER_COMPOSE) exec -T php ./bin/console doctrine:database:create --env=test
	$(DOCKER_COMPOSE) exec -T php ./bin/console doctrine:migrations:migrate --env=test  --no-interaction

run-tests-phpunit: ## Run PhpUnit tests. Parameter FILTER can be used to launch specific tests.
run-tests-phpunit:
	$(DOCKER_COMPOSE) exec -T php ./vendor/bin/simple-phpunit $(if $(FILTER), --filter=${FILTER})

test: ## Run all tests and check coding standards.
test:
	make build-database@test
	make run-tests-phpunit

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
