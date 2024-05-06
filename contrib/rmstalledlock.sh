#!/bin/ksh
#
# Check for the existance of files that are older than a given set of minutes
# and delete them raising appropriate error.
# This script is to be used in conjuction with EaseProbe to assist in removing
# crontab locks.
#
#  - name: Cron lock files
#    cmd: "/usr/local/sbin/rmstalledlock.sh"
#    env:
#      - "BASEDIR=/tmp"
#      - "PATTERN=*.lock"
#      - "AGE=+1"
#
BASEDIR=${BASEDIR:-"/tmp"}
PATTERN=${PATTERN:-"*.lock"}
AGE=${AGE:-"+5"}

find ${BASEDIR} -type f -name "${PATTERN}" -mmin "${AGE}" -maxdepth 1 -exec echo "Removing lock: {}" \; -exec rm -f -- "{}" \;
