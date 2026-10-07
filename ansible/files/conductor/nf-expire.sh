#!/bin/sh
# nf-expire.sh [DATA_DIR] [SIZE] [AGE]
# Stores the retention limits in every exporter folder (nfexpire -u) and expires
# old files (nfexpire -e). Empty SIZE or AGE means no limit of that kind.
DATA=${1:-/var/nfcapd/exp}
SIZE=${2:-}
AGE=${3:-14d}
rc=0
for d in "$DATA"/*/; do
  [ -d "$d" ] || continue
  d=${d%/}
  set --
  [ -n "$SIZE" ] && set -- "$@" -s "$SIZE"
  [ -n "$AGE" ] && set -- "$@" -t "$AGE"
  [ $# -gt 0 ] || exit 0
  nfexpire -u "$d" "$@" >/dev/null 2>&1 || { echo "nfexpire -u failed for $d" >&2; rc=1; continue; }
  nfexpire -e "$d" >/dev/null 2>&1 || { echo "nfexpire -e failed for $d" >&2; rc=1; }
done
exit $rc
