# Monitoring politicians promises [![Build Status](https://travis-ci.org/arteniioleg/defacto.md.svg?branch=dev)](https://travis-ci.org/arteniioleg/defacto.md)

## Getting started

1. Create `backup/postgres/pg_backup.config` _(copy `pg_backup.config.dist`)_
2. Create `backup/postgres/.pgpass` _(copy `.pgpass.dist`)_
3. Create `.env` _(copy `.env.dist`)_
4. In `docker/` execute `docker-compose up -d`
5. Execute

    ```bash
    docker exec defacto_pg psql DBNAME USERNAME -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";'
    ```
6. Execute `docker/php.sh composer install`
7. Execute `docker/php.sh bin/console doctrine:schema:create`