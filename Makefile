# HELP
.PHONY: help

help: ## Makefile help.
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

up: ## Start local cluster.
	@$(MAKE) -s env
	docker-compose up -d

ps: ## Start local cluster.
	@$(MAKE) -s env
	docker-compose ps --all


bash: ## Access php container command line.
	@$(MAKE) -s up
	docker-compose exec ak_invoice_php bash

env: ## Confirm that .env file exists (internal).
	@test -s .env || cp .env.dist .env