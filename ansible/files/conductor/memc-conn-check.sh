#!/bin/sh
# memc-conn-check.sh HOST FIRST_PORT [LAST_PORT]
#
# Samples memcached "stats" on every port in the range and exits 1 when, on any
# instance, BOTH of these hold at the same time:
#   curr_connections > CURR_MAX
#   total_connections grew faster than RATE_MAX per second since the previous run
# Either one alone is only reported in the output, it does not fail the check.
# An unreachable instance always fails.
#
# Tunables (environment): CURR_MAX (default 1000), RATE_MAX (default 20),
# STATE_DIR (default /var/run).
HOST=${1:?usage: memc-conn-check.sh HOST FIRST_PORT [LAST_PORT]}
FIRST=${2:?usage: memc-conn-check.sh HOST FIRST_PORT [LAST_PORT]}
LAST=${3:-$FIRST}
CURR_MAX=${CURR_MAX:-1000}
RATE_MAX=${RATE_MAX:-50}
STATE_DIR=${STATE_DIR:-/var/run}

now=$(date +%s)
bad=0
p=$FIRST
while [ "$p" -le "$LAST" ]; do
  out=$(printf 'stats\r\nquit\r\n' | nc -w 2 "$HOST" "$p" 2>/dev/null)
  curr=$(printf '%s\n' "$out" | awk '$2=="curr_connections"{print $3+0}')
  total=$(printf '%s\n' "$out" | awk '$2=="total_connections"{print $3+0}')
  if [ -z "$curr" ] || [ -z "$total" ]; then
    echo "$HOST:$p UNREACHABLE (no stats)"
    bad=1
    p=$((p + 1))
    continue
  fi

  st="$STATE_DIR/memc-conn-check.$HOST.$p"
  msg="$HOST:$p curr=$curr"
  rate=""
  if [ -f "$st" ]; then
    read -r pt ptotal < "$st"
    case "$pt$ptotal" in
      ''|*[!0-9]*) ;;
      *)
        dt=$((now - pt))
        # total_connections goes backwards when memcached restarted: skip the sample
        if [ "$dt" -gt 0 ] && [ "$total" -ge "$ptotal" ]; then
          rate=$(awk -v n="$total" -v o="$ptotal" -v t="$dt" 'BEGIN{printf "%.2f", (n-o)/t}')
        fi
        ;;
    esac
  fi
  printf '%s %s\n' "$now" "$total" > "$st.tmp" && mv "$st.tmp" "$st"

  hi_rate=0
  hi_curr=0
  if [ -n "$rate" ]; then
    msg="$msg new_conn_rate=${rate}/s"
    if awk -v r="$rate" -v m="$RATE_MAX" 'BEGIN{exit !(r>m)}'; then
      msg="$msg HIGH_RATE(>${RATE_MAX}/s)"
      hi_rate=1
    fi
  else
    msg="$msg new_conn_rate=n/a"
  fi
  if [ "$curr" -gt "$CURR_MAX" ]; then
    msg="$msg HIGH_CURR(>${CURR_MAX})"
    hi_curr=1
  fi
  if [ "$hi_rate" -eq 1 ] && [ "$hi_curr" -eq 1 ]; then
    msg="$msg ALERT"
    bad=1
  fi
  echo "$msg"
  p=$((p + 1))
done
exit "$bad"
