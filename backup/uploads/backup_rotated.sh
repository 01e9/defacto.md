#!/usr/bin/env bash

SOURCE_DIR=${1}
[ -n "${SOURCE_DIR}" ] \
    || { echo "Source dir is required"; exit 1; }

DESTINATION_DIR=${2}
[ -n "${DESTINATION_DIR}" ] \
    || { echo "Destination dir is required"; exit 1; }

BACKUP_NAME=$(date +\%Y-\%m-\%d)

tar -czf ${DESTINATION_DIR}/${BACKUP_NAME}.tar.gz --transform "s,^${SOURCE_DIR:1},," ${SOURCE_DIR}/*

find ${DESTINATION_DIR}/*.tar.gz -mtime +90 -exec rm {} \;
