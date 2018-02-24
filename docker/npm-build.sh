#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

docker run -it --rm -u ${UID} \
    -v ${SCRIPT_DIR}/..:/project \
    node:9 \
    bash -c 'cd /project && npm install && npm run build'
