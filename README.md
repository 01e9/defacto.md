# Monitoring politicians promises [![Build Status](https://travis-ci.org/01e9/defacto.md.svg?branch=dev)](https://travis-ci.org/01e9/defacto.md)

## Getting started

### Configure credentials

* Create `.env` _(copy `.env.dist`)_
* Create `docker/docker-compose.override.yml` _(copy `docker/docker-compose.override.yml.dist`)_

### Start docker containers

* In `docker/` execute `docker-compose up -d`

### Prepare database and dependencies

* Execute

    ```bash
    docker exec defacto_pg psql DBNAME USERNAME -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";'
    ```
* Execute `docker/php.sh composer install`
* Execute `docker/php.sh bin/console doctrine:schema:create`
* Set `ALGOLIA_API_KEY` in `.env`
* Execute `docker/php.sh bin/console doctrine:fixtures:load`
* Execute `docker/js.sh npm install`
* Execute `docker/js.sh npm run build`

## Development

* Execute `docker/ide.sh ~/path/to/phpstorm/bin/phpstorm.sh` _(create php built-in server)_
