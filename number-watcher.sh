#!/bin/bash

inotifywait --quiet --monitor --event close_write --format %w%f --recursive "/etc/pull-a-number.ini" |
while read -r FILENAME; do
    notify-send "Pull-A-Number" "$(cat $FILENAME)"
done
