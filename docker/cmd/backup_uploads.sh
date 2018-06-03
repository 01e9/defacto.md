#!/usr/bin/env bash

unset sleep
trap 'echo TERMinated; kill $sleep; exit' TERM

while : ; do
    /backup.sh /uploads /backups
    sleep 86400 & sleep=$$!; wait
done
