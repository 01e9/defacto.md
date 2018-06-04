#!/usr/bin/env bash

CMD=${@}
[ -n "${CMD}" ] \
    || { echo "Command is required"; exit 1; }

SCRIPT_DIR=$(dirname $(readlink -f "$0"))
PROJECT_DIR=$(dirname ${SCRIPT_DIR})

docker run -it --rm -u ${UID} \
    -v ${PROJECT_DIR}:${PROJECT_DIR} \
    --network defacto_default \
    defacto_php \
    bash -c "cd ${PROJECT_DIR} && ${CMD}"
