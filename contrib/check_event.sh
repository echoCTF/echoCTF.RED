#!/bin/ksh
DATABASE=${1:-echoCTF}
EVENT=${2:-update_ranks}
integer FAILED_EVENT=$(mysql -NBe "select count(*) from information_schema.EVENTS where EVENT_NAME='${EVENT}' AND EVENT_SCHEMA='${DATABASE}' and LAST_EXECUTED < NOW() - INTERVAL 40 SECOND")
if [ $FAILED_EVENT -eq 1 ]; then
  echo "$(date): ${DATABASE}.${EVENT} had more than 40 seconds delayed execution"
  mysql -NBe "ALTER EVENT ${DATABASE}.${EVENT} ENABLE"
fi