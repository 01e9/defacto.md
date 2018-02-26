#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR=$(dirname ${SCRIPT_DIR})

docker run -it --rm -u ${UID} \
    -v ${PROJECT_DIR}:${PROJECT_DIR} \
    node:9 \
    bash -c "cd ${PROJECT_DIR} && $@"
