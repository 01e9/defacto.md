#!/bin/bash

[ -n "$(which x11docker)" ] \
    || { echo "x11docker is required"; exit 1; }

CMD=${@}
[ -n "${CMD}" ] \
    || { echo "Command is required"; exit 1; }

SCRIPT_DIR=$(dirname $(readlink -f "$0"))
PROJECT_DIR=$(dirname ${SCRIPT_DIR})
IMAGE='01e9/ide:php'
NETWORK='defacto_default'
HOST='defacto.local'

cd ${SCRIPT_DIR}

docker-compose up -d

x11docker \
    --hostdisplay \
    --homedir ${HOME} \
    --clipboard \
    --stdout --stderr \
    --cap-default \
    --workdir ${PROJECT_DIR} \
    --runasroot "apt-get update && apt-get install -y \
        && (curl -sL https://deb.nodesource.com/setup_10.x | bash -) && apt-get install -y nodejs" \
    -- "--cap-add=SYS_PTRACE --publish=80:8080 --network ${NETWORK} --hostname ${HOST}" \
    ${IMAGE} ${CMD}
