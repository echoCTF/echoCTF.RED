#!/bin/ksh
MEMD="{{db.host}}"
DBHOST="127.0.0.1"
DBUSER="{{db.user}}"
DBPASS="{{db.pass}}"
NCOPTS="-N"
WEBHOOK="{{discord_webhook}}"

# Discord notification for a churn rate-limit hit, one per player per window
# Backgrounded so the connect/disconnect path never waits on the network
notify_churn() {
  curl -s -m 5 -o /dev/null -X POST -H 'Content-Type: application/json' \
    -d "{\"username\":\"VPN churn\",\"content\":\"$(hostname): CN=${common_name} hit the VPN churn limit on ${1} (remote=${untrusted_ip})\"}" \
    "$WEBHOOK" &
}

# Discord notification for a player rejected by the per IP connection limit, one per player per window
notify_iplimit() {
  curl -s -m 5 -o /dev/null -X POST -H 'Content-Type: application/json' \
    -d "{\"username\":\"VPN IP limit\",\"content\":\"$(hostname): CN=${common_name} hit the VPN connections per IP limit (remote=${untrusted_ip})\"}" \
    "$WEBHOOK" &
    /usr/local/bin/backend player/ws-notify "${common_name}" "swal:warning" "VPN connections per IP limit" "You tried to connect to the VPN from an IP that is already connected. You need to either get a subscription that allows multiple IP's or contact our support to be considered for a whitelist!" &
}

# Per player bandwidth slots
BW_SLOTS=100
BW_MEMC_SOCK="/var/run/memcached/memcached.sock"

bw_slot_acquire() {
  [ -x /sbin/pfctl ] || return 0
  _i=1
  while [ $_i -le $BW_SLOTS ]; do
    if [ -z "$(/sbin/pfctl -t bw_slot_${_i} -T show 2>/dev/null)" ]; then
      /sbin/pfctl -q -t "bw_slot_${_i}" -T replace "${ifconfig_pool_remote_ip}"
      _len=$(printf '%s' "${_i}" | wc -c | tr -d ' ')
      printf 'set bw_slot:%s 0 0 %s\r\n%s\r\n' "${ifconfig_pool_remote_ip}" "${_len}" "${_i}" | nc ${NCOPTS} -U "$BW_MEMC_SOCK" >/dev/null
      echo "bw_slot[$$]: CN=${common_name}, local=${ifconfig_pool_remote_ip} slot ${_i}"
      return 0
    fi
    _i=$((_i+1))
  done
  echo "bw_slot[$$]: WARNING CN=${common_name} no free bandwidth slot"
}

bw_slot_release() {
  [ -x /sbin/pfctl ] || return 0
  _slot=$(printf 'get bw_slot:%s\r\n' "${ifconfig_pool_remote_ip}" | nc ${NCOPTS} -U "$BW_MEMC_SOCK" | egrep -v "(VALUE|END)" | tr -d '\r')
  [ -n "$_slot" ] || return 0
  /sbin/pfctl -q -t "bw_slot_${_slot}" -T delete "${ifconfig_pool_remote_ip}"
  printf 'delete bw_slot:%s\r\n' "${ifconfig_pool_remote_ip}" | nc ${NCOPTS} -U "$BW_MEMC_SOCK" >/dev/null
}

# Stop users from connecting when the event is not active
#EVENT_ACTIVE=$(echo "get sysconfig:event_active"|nc -N ${MEMD} 11211 |egrep -v "(VALUE|END)")
#if [ "$EVENT_ACTIVE" == "0" ] || [ "$EVENT_ACTIVE" == "" ]; then
#  exit 2
#fi

# Capture all output that may escape us into /tmp/logging of the chroot
exec 1>>/tmp/openvpn-updown.log
exec 2>&1

echo "------------"
date

if [ "$script_type" == "client-connect" ]; then
    /usr/local/bin/vpn_churn_check -s /var/run/memcached/memcached.sock "$common_name" connect
    CHURN_RC=$?
    if [ $CHURN_RC -ne 0 ]; then
      echo "client-connect[$$]: ERROR CN=${common_name} hit the rate-limit"
      if [ $CHURN_RC -eq 2 ]; then
        notify_churn connect
      fi
      exit 1
    fi
    echo "client-connect[$$]: CN=${common_name}"
    NETWORKS=$(mysql --connect-timeout=10 -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "CALL VPN_LOGIN(${common_name},INET_ATON('${ifconfig_pool_remote_ip}'),INET_ATON('${untrusted_ip}'))")
    if [ "$NETWORKS" == "LOGGEDIN" ]; then
      echo "client-connect[$$]: ERROR CN=${common_name}, local=${ifconfig_pool_remote_ip}, remote=${untrusted_ip} already logged in"
      exit 1
    elif [ "$NETWORKS" == "IPLIMIT" ] || [ "$NETWORKS" == "IPLIMIT_REPEAT" ]; then
      echo "client-connect[$$]: ERROR CN=${common_name}, local=${ifconfig_pool_remote_ip}, remote=${untrusted_ip} hit the IP connection limit"
      if [ "$NETWORKS" == "IPLIMIT" ]; then
        notify_iplimit
      fi
      exit 1
    elif [ "${NETWORKS}"x != "x" ]; then
      echo "client-connect[$$]: CN=${common_name} networks="${NETWORKS}
      if [ -x /sbin/pfctl ]; then
        for network in ${NETWORKS};do
          /sbin/pfctl -q -t "${network}_clients" -T add ${ifconfig_pool_remote_ip}
        done
      fi
    else
      echo "client-connect[$$]: CN=${common_name} no networks"
    fi
    bw_slot_acquire
    echo "client-connect[$$]: CN=${common_name}, local=${ifconfig_pool_remote_ip}, remote=${untrusted_ip}"
elif [ "$script_type" == "client-disconnect" ]; then
  /usr/local/bin/vpn_churn_check -s /var/run/memcached/memcached.sock "$common_name" disconnect
  CHURN_RC=$?
  if [ $CHURN_RC -ne 0 ]; then
    echo "client-disconnect[$$]: ERROR CN=${common_name} hit the rate-limit"
    if [ $CHURN_RC -eq 2 ]; then
      notify_churn disconnect
    fi
  fi

  mysql --connect-timeout=10 -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe \
    "CALL VPN_BANDWIDTH_LOG(${common_name}, INET_ATON('${ifconfig_pool_remote_ip}'), ${bytes_received:-0}, ${bytes_sent:-0}, ${time_duration:-0})"
  echo "client-disconnect[$$]: CN=${common_name}, bandwidth logged: rx=${bytes_received:-0} tx=${bytes_sent:-0} duration=${time_duration:-0}s"

  NETWORKS=$(mysql --connect-timeout=10 -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" -NBe "CALL VPN_LOGOUT(${common_name},INET_ATON('${ifconfig_pool_remote_ip}'),INET_ATON('${untrusted_ip}'))" echoCTF)
  if [ -x /sbin/pfctl ]; then
    for network in ${NETWORKS};do
      /sbin/pfctl -q -t "${network}_clients" -T delete ${ifconfig_pool_remote_ip}
    done
  fi
  bw_slot_release
  echo "client-disconnect[$$]: CN=${common_name}, local=${ifconfig_pool_remote_ip}, remote=${untrusted_ip}"
fi

exit 0
