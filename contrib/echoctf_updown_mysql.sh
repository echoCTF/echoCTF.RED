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
exec 1>>/tmp/logging
exec 2>&1

echo "------------" >>/tmp/updown.log
date >> /tmp/updown.log

if [ "$script_type" == "client-connect" ]; then
    echo "client connect $common_name" >> /tmp/updown.log
    USER_LOGGEDIN=$(echo "get ovpn:$common_name"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)")
    echo "USER_LOGGEDIN: $USER_LOGGEDIN" >>/tmp/updown.log
    if [ "$USER_LOGGEDIN" == "" ]; then
      echo "[$$] logging in client ${common_name}" >>/tmp/updown.log
      mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -e "CALL VPN_LOGIN(${common_name},INET_ATON('${ifconfig_pool_remote_ip}'),INET_ATON('${untrusted_ip}'))"
      if [ -x /sbin/pfctl ]; then
        for network in $(mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "SELECT codename FROM network WHERE id in (SELECT network_id FROM network_player WHERE player_id='${common_name}') AND codename is not null");do
          /sbin/pfctl -t "${network}_clients" -T add ${ifconfig_pool_remote_ip}
        done
      fi
      echo "[$$] client ${common_name} logged in successfully">>/tmp/updown.log
    else
      echo "[$$] client ${common_name} already logged in">>/tmp/updown.log
      exit 1
    fi
elif [ "$script_type" == "client-disconnect" ]; then
  mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" -e "CALL VPN_LOGOUT(${common_name},INET_ATON('${ifconfig_pool_remote_ip}'),INET_ATON('${untrusted_ip}'))" echoCTF
  if [ -x /sbin/pfctl ]; then
    for network in $(mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "SELECT codename FROM network WHERE (codename IS NOT NULL AND active=1) AND (public=1 or id IN (SELECT network_id FROM network_player WHERE player_id='${common_name}'))");do
      /sbin/pfctl -t "${network}_clients" -T delete ${ifconfig_pool_remote_ip}
    done
  fi
  echo "[$$] client cn:${common_name}, local:${ifconfig_pool_remote_ip}, remote: ${untrusted_ip} disconnected successfully">>/tmp/updown.log
fi

exit 0
