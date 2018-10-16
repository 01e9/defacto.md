#!/usr/bin/env bash

mkdir -p /mnt/sync/folders
mkdir -p /mnt/sync/config

if ! [ -f /etc/sync.conf ]; then
    SECRET=$(cat /run/secrets/resilio || rslsync --generate-secret)

    cp /etc/sync.conf.template /etc/sync.conf
    sed -i "s/SECRET/${SECRET}/" /etc/sync.conf
fi

echo "$*"

HOME=/mnt/sync/folders exec /usr/bin/rslsync --nodaemon $*
