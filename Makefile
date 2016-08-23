CONTAINER_NAME=file-web-view


build:
	composer install

docker-start: docker-build
	-@docker rm -f $(CONTAINER_NAME)
	mkdir -p tpl_bin htdocs
	chmod a+w -R tpl_bin htdocs
	docker run --name $(CONTAINER_NAME) -d \
		-v $(shell pwd):/var/www/file-view \
		-v $(shell pwd)/docker/nginx:/etc/nginx/sites-enabled \
		-p 8071:80 \
		webreactor/nginx-php:v0.0.1

docker-build:
	docker run --rm \
		-v $(shell pwd):/var/www \
		webreactor/nginx-php:v0.0.1 make build
