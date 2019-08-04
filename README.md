# Monitoring politicians promises [![Build Status](https://travis-ci.org/01e9/defacto.md.svg?branch=dev)](https://travis-ci.org/01e9/defacto.md)

This project is shipped as a [docker](https://www.docker.com/) image [`01e9/defacto.md`](https://hub.docker.com/r/01e9/defacto.md)

## Getting started

* Create a (Postgres) database container

    ```bash
    docker run \
        --rm \
        -e "POSTGRES_USER=defacto" \
        -e "POSTGRES_PASSWORD=defacto" \
        -e "POSTGRES_DB=defacto" \
        --name defacto_pg \
        --network docker_default \
        postgres:9
    ```

* Start the website container
 
    ```bash
    docker run \
        --rm \
        -e "DATABASE_URL=pgsql://defacto:defacto@defacto_pg:5432/defacto" \
        -e "APP_SECRET={some-random-md5}" \
        -e "ALGOLIA_APP_ID={your-app-id}" \
        -e "ALGOLIA_API_KEY={your-api-key}" \
        -e "GOOGLE_MAPS_API_KEY={your-gmaps-key}" \
        -e "APP_ENV=dev" \
        --name defacto \
        --network docker_default \
        -p 80:80 \
        01e9/defacto.md
    ```

* Prepare the database

    ```bash
    docker exec defacto_pg psql defacto defacto -c 'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";'
    docker exec -u www-data defacto bin/console doctrine:schema:create
    docker exec -u www-data defacto bin/console --env=test doctrine:fixtures:load
    docker exec -u www-data defacto bin/console cache:clear --no-warmup
    ```

* Open in browser http://localhost
