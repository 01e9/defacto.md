#!/usr/bin/env bash

cp /backup/.pgpass ~/
chmod 600 ~/.pgpass

unset sleep
trap 'echo TERMinated; kill $sleep; exit' TERM

while : ; do
    /backup/pg_backup.sh
    sleep 86400 & sleep=$$!; wait
done
