#!/bin/sh
CFG=/etc/easeprobe/monitor.ovpn
PID=/var/run/ovpn-probe.pid

rm -f "$PID"
openvpn --config "$CFG" --script-security 2 --route-up /etc/easeprobe/ovpn-up.sh \
  --connect-retry-max 1 --resolv-retry 0 --server-poll-timeout 10 \
  --daemon --writepid "$PID" --log /tmp/ovpn-probe.log

# the pidfile can show up slightly after --daemon returns
i=0
while [ ! -s "$PID" ] && [ "$i" -lt 5 ]; do
  i=$((i + 1))
  sleep 1
done
[ -s "$PID" ] || exit 1
p=$(cat "$PID")

i=0
while kill -0 "$p" 2>/dev/null; do
  i=$((i + 1))
  [ "$i" -ge 20 ] && { kill "$p"; break; }
  sleep 1
done
