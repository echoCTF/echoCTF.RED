#!/bin/ksh
# usage: vpn_churn_check.sh <player_id> <connect|disconnect> [limit] [window_seconds]
PLAYER_ID="$1"
EVENT="$2"
LIMIT="${3:-10}"
WINDOW="${4:-60}"
MEMD="{{db.host}}"
KEY="vpn_churn:${PLAYER_ID}"

RESULT=$(printf "incr %s 1\r\n" "$KEY" | nc -w 1 "$MEMD" 11211)

if printf '%s' "$RESULT" | grep -q "NOT_FOUND"; then
  printf "add %s 0 %d 1\r\n1\r\n" "$KEY" "$WINDOW" | nc -w 1 "$MEMD" 11211 >/dev/null
  COUNT=1
else
  COUNT=$(printf '%s' "$RESULT" | head -1 | tr -d '\r')
  memtouch --servers="${MEMD}:11211" --expire="$WINDOW" "$KEY"
fi

echo "player ${PLAYER_ID} ${EVENT}, churn count ${COUNT}/${LIMIT} in ${WINDOW}s" >&2

if [ "$COUNT" -gt "$LIMIT" ]; then
  echo "player ${PLAYER_ID} exceeded VPN churn limit" >&2
  exit 1
fi
exit 0