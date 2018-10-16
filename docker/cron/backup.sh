#!/usr/bin/env bash

set -e

SCRIPT_DIR=$(dirname $(readlink -f "$0"))
TMP_DIR='/tmp/defacto-backup'
ARCHIVE_NAME=backup-$(date '+%d.%m.%Y-%H_%M_%S').tar.gz

slack_message() {
    if [ -n "${SLACK_WEBHOOK}" ]; then
        docker run --rm busybox wget --post-data="payload={\"text\": \"${1}\"}" "${SLACK_WEBHOOK}"
    fi
}

trap 'slack_message "DeFacto backup error on line ${LINENO}"' ERR

slack_message "DeFacto backup started"

rm -rf ${TMP_DIR} && mkdir -p ${TMP_DIR}

docker stop defacto_php defacto_pg

docker run --rm \
    -v defacto_postgres:/mnt/source/postgres:ro \
    -v ${SCRIPT_DIR}/../../uploads:/mnt/source/uploads:ro \
    -v defacto_backup:/mnt/dest \
    busybox \
    sh -c "cd /mnt/source && tar -czf /tmp/${ARCHIVE_NAME} * && mv /tmp/${ARCHIVE_NAME} /mnt/dest/"

docker start defacto_pg defacto_php

rm -rf ${TMP_DIR}

docker run --rm \
    -v defacto_backup:/archives \
    busybox \
    sh -c "find /archives -maxdepth 1 -mtime +30 -type f -delete"

slack_message "DeFacto backup finished"
