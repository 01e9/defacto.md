#!/bin/bash

[ -n "$(which x11docker)" ] \
    || { echo "x11docker is required"; exit 1; }

CMD=${@}
[ -n "${CMD}" ] \
    || { echo "Command is required"; exit 1; }

SCRIPT_DIR=$(dirname $(readlink -f "$0"))
PROJECT_DIR=$(dirname ${SCRIPT_DIR})
IMAGE='defacto_php'
NETWORK='defacto_default'
HOST='defacto.local'

cd ${SCRIPT_DIR}

docker build -t ${IMAGE} .

docker-compose up -d

x11docker \
    --hostdisplay \
    --homedir ${HOME} \
    --clipboard \
    --stdout --stderr \
    --cap-default \
    --workdir ${PROJECT_DIR} \
    --runasroot "echo 'Installing GUI libraries...' && apt-get update && apt-get install -y \
        libgtk2.0-0 libcanberra-gtk-module libxext-dev libxrender-dev \
        libxtst-dev libxslt-dev dmz-cursor-theme \
        git wget htop zip unzip nano iputils-ping" \
    -- "--cap-add=SYS_PTRACE --publish=80:8080 --network ${NETWORK} --hostname ${HOST}" \
    ${IMAGE} ${CMD}
