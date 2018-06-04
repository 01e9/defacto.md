# Monitoring politicians promises [![Build Status](https://travis-ci.org/arteniioleg/defacto.md.svg?branch=dev)](https://travis-ci.org/arteniioleg/defacto.md)

## Getting started

### Configure credentials

* Create `.env` _(copy `.env.dist`)_
* Create `backup/postgres/pg_backup.config` _(copy `pg_backup.config.dist`)_
* Create `backup/postgres/.pgpass` _(copy `.pgpass.dist`)_
* Create `docker/docker-compose.override.yml`

    ```yaml
    version: '3.4'

    services:
        defacto_pg:
            environment:
                POSTGRES_PASSWORD: "same as in .env"
    ```

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

### Start the project

* Execute `docker/test-nginx.sh` _(check if works in browser then Ctrl+C)_
* Execute `docker/ide.sh ~/path/to/phpstorm/bin/phpstorm.sh` _(create php built-in server)_

### Configure backup sync

* Open in browser `http://YOUR_IP:8800`
* Navigate [+] > Standard folder > Choose `/mnt/mounted_folders/backup`
* Copy the Read Only Key
* Setup [Resilio Sync](https://www.resilio.com/individuals/) on another machine and sync a folder with that key
