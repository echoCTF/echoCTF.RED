#!/bin/sh
OK=/var/run/ovpn-probe.$1.ok
MAX=${MAX:-900}
[ -f "$OK" ] || { echo "no marker"; exit 1; }
t=$(cat "$OK")
case "$t" in ''|*[!0-9]*) echo "bad marker"; exit 1 ;; esac
age=$(( $(date +%s) - t ))
[ "$age" -ge -5 ] || { echo "marker from the future, ${age}s"; exit 1; }
[ "$age" -le "$MAX" ] || { echo "stale marker, ${age}s old"; exit 1; }
echo "ok, ${age}s old"
