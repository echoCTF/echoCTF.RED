#!/bin/ksh
MEMD="{{db.host}}"
DBHOST="127.0.0.1"
DBUSER="{{db.user}}"
DBPASS="{{db.pass}}"
NCOPTS="-N"
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
    echo "client-connect[$$]: CN=${common_name}"
    NETWORKS=$(mysql -h ${DBHOST} -u"${DBUSER}" echoCTF -NBe "CALL VPN_LOGIN(${common_name},INET_ATON('${ifconfig_pool_remote_ip}'),INET_ATON('${untrusted_ip}'))")
    if [ "$NETWORKS" == "LOGGEDIN" ]; then
      echo "client-connect[$$]: ERROR CN=${common_name}, local=${ifconfig_pool_remote_ip}, remote=${untrusted_ip} already logged in"
      exit 1
    elif [ "${NETWORKS}"x != "x" ]; then
      echo "client-connect[$$]: CN=${common_name} networks="${NETWORKS}
      if [ -x /sbin/pfctl ]; then
        for network in ${NETWORKS};do
          /sbin/pfctl -t "${network}_clients" -T add ${ifconfig_pool_remote_ip}
        done
      fi
    else
      echo "client-connect[$$]: CN=${common_name} no networks"
    fi
    echo "client-connect[$$]: CN=${common_name}, local=${ifconfig_pool_remote_ip}, remote=${untrusted_ip}"
elif [ "$script_type" == "client-disconnect" ]; then
  NETWORKS=$(mysql -h ${DBHOST} -u"${DBUSER}" -NBe "CALL VPN_LOGOUT(${common_name},INET_ATON('${ifconfig_pool_remote_ip}'),INET_ATON('${untrusted_ip}'))" echoCTF)
  if [ -x /sbin/pfctl ]; then
    for network in ${NETWORKS};do
      /sbin/pfctl -t "${network}_clients" -T delete ${ifconfig_pool_remote_ip}
    done
  fi
  echo "client-disconnect[$$]: CN=${common_name}, local=${ifconfig_pool_remote_ip}, remote=${untrusted_ip}"
fi

exit 0
