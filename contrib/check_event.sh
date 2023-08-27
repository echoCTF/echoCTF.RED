#!/bin/ksh
DATABASE=${1:-echoCTF}
EVENT=${2:-update_ranks}
integer TIMEOUT=${3:-40}
integer FAILED_EVENT=$(mysql -NBe "select count(*) from information_schema.EVENTS where EVENT_NAME='${EVENT}' AND EVENT_SCHEMA='${DATABASE}' and LAST_EXECUTED < NOW() - INTERVAL ${TIMEOUT} SECOND")
if [ $FAILED_EVENT -eq 1 ]; then
  echo "$(date): ${DATABASE}.${EVENT} had more than ${TIMEOUT} seconds delayed execution"
  mysql -NBe "ALTER EVENT ${DATABASE}.${EVENT} ENABLE"
fi