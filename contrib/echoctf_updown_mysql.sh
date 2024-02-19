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
        for network in $(mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "SELECT codename FROM network WHERE (codename IS NOT NULL AND active=1) AND (public=1 or id IN (SELECT network_id FROM network_player WHERE player_id='${common_name}'))");do
          /sbin/pfctl -t "${network}_clients" -T add ${ifconfig_pool_remote_ip}
        done
        # Add to private instances of our own and our team mates
        TEAM_VISIBLE_INSTANCES=$(echo "get sysconfig:team_visible_instances"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)")
        TEAMS_QUERY="SELECT LOWER(CONCAT(t2.name,'_',player_id)) AS net FROM target_instance as t1 LEFT JOIN target as t2 on t1.target_id=t2.id WHERE player_id=${common_name} or player_id IN (SELECT player_id from team_player WHERE team_id IN (SELECT team_id FROM team_player where player_id=${common_name} and approved=1))"
        if [ "$TEAM_VISIBLE_INSTANCES" == "" ]; then
          TEAMS_QUERY="SELECT LOWER(CONCAT(t2.name,'_',player_id)) AS net FROM target_instance as t1 LEFT JOIN target as t2 on t1.target_id=t2.id WHERE player_id=${common_name} or (player_id IN (SELECT player_id from team_player WHERE team_id IN (SELECT team_id FROM team_player where player_id=${common_name} and approved=1)) AND team_allowed=1)"
        fi
        for network in $(mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "$TEAMS_QUERY");do
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
    TEAM_VISIBLE_INSTANCES=$(echo "get sysconfig:team_visible_instances"|nc ${NCOPTS} ${MEMD} 11211 |egrep -v "(VALUE|END)")
    TEAMS_QUERY="SELECT LOWER(CONCAT(t2.name,'_',player_id)) AS net FROM target_instance as t1 LEFT JOIN target as t2 on t1.target_id=t2.id WHERE player_id=${common_name} or player_id IN (SELECT player_id from team_player WHERE team_id IN (SELECT team_id FROM team_player where player_id=${common_name} and approved=1))"
    if [ "$TEAM_VISIBLE_INSTANCES" == "" ]; then
      TEAMS_QUERY="SELECT LOWER(CONCAT(t2.name,'_',player_id)) AS net FROM target_instance as t1 LEFT JOIN target as t2 on t1.target_id=t2.id WHERE player_id=${common_name} or (player_id IN (SELECT player_id from team_player WHERE team_id IN (SELECT team_id FROM team_player where player_id=${common_name} and approved=1)) AND team_allowed=1)"
    fi

    for network in $(mysql -h ${DBHOST} -u"${DBUSER}" -p"${DBPASS}" echoCTF -NBe "$TEAMS_QUERY");do
      /sbin/pfctl -t "${network}_clients" -T delete ${ifconfig_pool_remote_ip}
    done
  fi
  echo "[$$] client cn:${common_name}, local:${ifconfig_pool_remote_ip}, remote: ${untrusted_ip} disconnected successfully">>/tmp/updown.log
fi

exit 0
