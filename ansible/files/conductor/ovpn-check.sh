#!/bin/sh
# One check for all VPN servers, reads what ovpn-probe.sh recorded.
# Exit 0 and one line when fine. Exit 1 naming the servers and the reason when not.
# env: MINFAIL consecutive failures before a server counts as down (default 2)
#      SLACK   seconds of silence tolerated beyond the runner pause (default 120)
LIST=/etc/easeprobe/vpn-servers
D=/var/run/ovpn-probe
MINFAIL=${MINFAIL:-2}
SLACK=${SLACK:-120}
now=$(date +%s)

ago() {
  s=$((now - $1))
  if [ "$s" -ge 3600 ]; then echo "$((s / 3600))h"
  elif [ "$s" -ge 60 ]; then echo "$((s / 60))m"
  else echo "${s}s"; fi
}

[ -r "$D/pause" ] || { echo "probe runner never started: supervisorctl status vpn-probe"; exit 1; }
read -r pause < "$D/pause"

total=0 tested=0 bad=0 newest=0 list=
while read -r name ip; do
  total=$((total + 1))
  [ -r "$D/$name" ] || continue
  tested=$((tested + 1))
  read -r r t fails lastok detail < "$D/$name"
  [ "$t" -gt "$newest" ] && newest=$t
  [ "$fails" -ge "$MINFAIL" ] || continue
  bad=$((bad + 1))
  [ "$bad" -le 5 ] || continue
  if [ "$lastok" -gt 0 ]; then seen="last ok $(ago "$lastok") ago"; else seen="never ok"; fi
  list="$list${list:+, }$name $r x$fails ($seen)${detail:+ [$detail]}"
done < "$LIST"

[ "$newest" -gt 0 ] || { echo "no results from the probe runner yet: supervisorctl status vpn-probe"; exit 1; }
limit=$((pause + SLACK))
[ $((now - newest)) -le "$limit" ] || { echo "probe runner stalled, last result $(ago "$newest") ago (expected within ${limit}s): supervisorctl status vpn-probe"; exit 1; }
[ "$bad" -eq 0 ] && { echo "all $tested of $total servers ok"; exit 0; }
[ "$bad" -gt 5 ] && list="$list, +$((bad - 5)) more"
echo "$bad of $total servers failing: $list"
exit 1
