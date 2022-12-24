## About

- This is a program to practice web crawler with Goutte and Curl

## Requirements

- Docker 

- Docker compose

- Docker image: php:8.1-apache-buster


## Install

```bash

git clone git@github.com:felipanico/php-crawler-example.git

cd ./php-crawler-example/docker/web

docker-compose build --no-cache

docker-compose up

cd ./php-crawler-example

docker-compose exec --user app web composer install

```

Open in your browser: http://127.0.0.1:10000/

## Tests

docker-compose exec --user app web ./vendor/bin/phpunit --colors tests


## Contributing

Feel free to contribute with this project
