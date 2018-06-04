# Monitoring politicians promises [![Build Status](https://travis-ci.org/arteniioleg/defacto.md.svg?branch=dev)](https://travis-ci.org/arteniioleg/defacto.md)

## Getting started

* Create `backup/postgres/pg_backup.config` _(copy `pg_backup.config.dist`)_
* Create `backup/postgres/.pgpass` _(copy `.pgpass.dist`)_
* Create `.env` _(copy `.env.dist`)_
* Create `docker/docker-compose.override.yml`
* In `docker/` execute `docker-compose up -d`
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
* Execute `docker/test-nginx.sh`