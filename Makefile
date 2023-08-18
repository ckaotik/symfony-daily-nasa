shell:
	docker compose run php /bin/bash

build:
	docker compose up -d --no-deps --build php