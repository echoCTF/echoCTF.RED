#!/bin/sh
#
# Check a given database for events disabled
# more than a given number of seconds (300 default).
#

## default values modify as you please
DATABASE="echoCTF"
INTERVAL=300
VERBOSE=0
ENABLE=1


usage () {
  echo -e "\t\tMySQL events checker"
  echo -e "\t\t--------------------\n"
  echo "usage: $0 [-ehv] ] [-d database] [-i interval]"
  echo "  -h            this help"
  echo "  -v            verbose output (default: disabled)"
  echo "  -e            dont enable events that are disabled (default: enable)"
  echo "  -d database   check events for the given database (using: ${DATABASE})"
  echo "  -i interval   check for events disabled longer than interval in seconds (using: ${INTERVAL})"
  echo
}


OPTSTRING=":vehd:i:"

while getopts ${OPTSTRING} opt; do
  case ${opt} in
    d)
      DATABASE="${OPTARG}"
      ;;
    i)
      INTERVAL="${OPTARG}"
      ;;
    v)
      VERBOSE=1
      ;;
    e)
      ENABLE=0
      ;;
    h)
      usage
      exit 0
      ;;
    :)
      echo "Option -${OPTARG} requires an argument."
      echo
      usage
      exit 1
      ;;
    ?)
      echo "Invalid option: -${OPTARG}."
      echo
      usage
      exit 1
      ;;
  esac
done

if [ $VERBOSE -ne 0 ]; then
  echo "Using database: ${DATABASE}"
fi

for _ev in $(mysql -NBe "SELECT CONCAT('${DATABASE}.',EVENT_NAME) from INFORMATION_SCHEMA.EVENTS WHERE EVENT_SCHEMA='${DATABASE}' AND STATUS='DISABLED' AND (UNIX_TIMESTAMP(LAST_ALTERED)+${INTERVAL})<UNIX_TIMESTAMP(now()) AND @@EVENT_SCHEDULER='ON'");do
  if [ $VERBOSE -ne 0 ]; then
    echo "EVENT ${_ev} IS DISABLED LONGER THAN ${INTERVAL} seconds";
  fi
  if [ $ENABLE -ne 0 ]; then
    if [ $VERBOSE -ne 0 ]; then
      echo "ENABLING ${_ev}"
    fi
    mysql -e "ALTER EVENT ${_ev} ENABLE"
  fi
done;
