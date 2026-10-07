#!/bin/sh
CFG=/etc/easeprobe/monitor.ovpn
LIST=/etc/easeprobe/vpn-servers

while read -r name ip; do
  PID=/var/run/ovpn-probe.$name.pid
  rm -f "$PID"
  openvpn --config "$CFG" --remote "$ip" 1194 udp --setenv MARKER /var/run/ovpn-probe.$name.ok \
    --script-security 2 --route-up /etc/easeprobe/ovpn-up.sh \
    --connect-retry-max 1 --resolv-retry 0 --server-poll-timeout 10 \
    --daemon --writepid "$PID" --log /tmp/ovpn-probe.$name.log < /dev/null

  i=0
  while [ ! -s "$PID" ] && [ "$i" -lt 5 ]; do
    i=$((i + 1))
    sleep 1
  done
  [ -s "$PID" ] || continue
  p=$(cat "$PID")

  i=0
  while kill -0 "$p" 2>/dev/null; do
    i=$((i + 1))
    [ "$i" -ge 20 ] && { kill "$p"; break; }
    sleep 1
  done
done < "$LIST"
