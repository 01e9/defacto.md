#!/bin/bash

[ -n "$(which x11docker)" ] \
    || { echo "x11docker is required"; exit 1; }

CMD=${@}
[ -n "${CMD}" ] \
    || { echo "Command is required"; exit 1; }

SCRIPT_DIR=$(dirname $(readlink -f "$0"))
PROJECT_DIR=$(dirname ${SCRIPT_DIR})
IMAGE=defacto_php
NETWORK=defacto_default

[ -n "$(docker images -q --filter=reference="${IMAGE}")" ] \
    || docker build -t ${IMAGE} ${SCRIPT_DIR}

[ -n "$(docker network ls -q --filter name=${NETWORK})" ] \
    || docker-compose -f ${SCRIPT_DIR}/docker-compose.yml up -d

x11docker \
    --hostdisplay \
    --homedir ${HOME} \
    --clipboard \
    --stdout --stderr \
    --cap-default \
    --no-init \
    --workdir ${PROJECT_DIR} \
    --runasroot "apt-get update && apt-get install -y \
        libgtk2.0-0 libcanberra-gtk-module libxext-dev libxrender-dev \
        libxtst-dev libxslt-dev dmz-cursor-theme \
        git wget htop zip unzip nano" \
    -- "--cap-add=SYS_PTRACE --publish=80:8080 --network ${NETWORK}" \
    ${IMAGE} ${CMD}
