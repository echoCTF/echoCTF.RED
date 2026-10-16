#!/bin/sh
# usage: ovpn-probe.sh [seconds to pause between passes, default 60]
# env:   GAP seconds between servers, default 4
CFG=/etc/easeprobe/monitor.ovpn
LIST=/etc/easeprobe/vpn-servers
D=/var/run/ovpn-probe
mkdir -p "$D"
echo "${1:-60}" > "$D/pause"
while :; do
while read -r name ip; do
  PID=/var/run/ovpn-probe.$name.pid
  M=/var/run/ovpn-probe.$name.ok
  LOG=/tmp/ovpn-probe.$name.log
  rm -f "$PID" "$M"
  openvpn --config "$CFG" --remote "$ip" 1194 udp --setenv MARKER "$M" \
    --script-security 2 --route-up /etc/easeprobe/ovpn-up.sh \
    --connect-retry-max 1 --resolv-retry 0 --server-poll-timeout 10 \
    --daemon --writepid "$PID" --log "$LOG" < /dev/null

  i=0
  while [ ! -s "$PID" ] && [ "$i" -lt 5 ]; do
    i=$((i + 1))
    sleep 1
  done
  killed=0
  if [ -s "$PID" ]; then
    p=$(cat "$PID")
    i=0
    while kill -0 "$p" 2>/dev/null; do
      i=$((i + 1))
      if [ "$i" -ge 20 ]; then
        killed=1
        kill "$p"
        k=0
        while kill -0 "$p" 2>/dev/null && [ "$k" -lt 5 ]; do
          k=$((k + 1))
          sleep 1
        done
        kill -0 "$p" 2>/dev/null && kill -9 "$p"
        break
      fi
      sleep 1
    done
  fi
  detail=
  if [ -f "$M" ]; then
    r=ok
  elif grep -q "AUTH_FAILED" "$LOG" 2>/dev/null; then
    r=auth-failed
  elif grep -q "Server poll timeout" "$LOG" 2>/dev/null; then
    r=timeout
    detail="no reply from server"
  elif [ "$killed" -eq 1 ]; then
    r=timeout
    detail="handshake not finished in time"
  else
    r=failed
    detail=$(grep -v -e SIGTERM -e "process exiting" -e "exit notification" "$LOG" 2>/dev/null | tail -n 1 | sed 's/^[0-9-]* [0-9:]* //' | cut -c1-80)
  fi
  now=$(date +%s)
  fails=0 lastok=0
  [ -r "$D/$name" ] && read -r _r _t fails lastok _d < "$D/$name"
  if [ "$r" = ok ]; then
    fails=0 lastok=$now
  else
    fails=$((fails + 1))
  fi
  printf '%s %s %s %s %s\n' "$r" "$now" "$fails" "$lastok" "$detail" > "$D/$name.new" && mv "$D/$name.new" "$D/$name"
  echo "$(date '+%Y-%m-%d %H:%M:%S') $name $r" >> /var/log/ovpn-probe.log
  sleep "${GAP:-4}"
done < "$LIST"
sleep "${1:-60}"
done
