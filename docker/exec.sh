#!/bin/bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR=$(dirname ${SCRIPT_DIR})

docker exec -it -u $UID defacto bash -c "cd ${PROJECT_DIR} && $@"
