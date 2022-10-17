#!/bin/bash

for ar in $(ls /ETS/autorun*.sh 2>/dev/null); do
  chmod 700 ${ar}
  nohup bash ${ar} >/dev/null &
done
trap ctrl_c INT

function ctrl_c() {
        exit
}


function echoctf() {
  ipaddr=$(ip addr|grep eth0 -A2|grep inet|head -1|awk '{print $2}'|awk -F/ '{print $1}')
  grep "${HOSTNAME}" /etc/hosts >/dev/null
  if [ $? -eq 1 ]; then
    echo "Fixing /etc/hosts"
    echo "${ipaddr} ${HOSTNAME}" >> /etc/hosts
  fi
  echo "You can access the interfaces on:"
  echo "* [frontend] http://${ipaddr}:8080/"
  echo "* [backend] http://${ipaddr}:8081/"
  echo "* [memcached] tcp://${ipaddr}:11211"
  echo "* [mysql] tcp://${ipaddr}:3306"
  echo "Backend username/password: echoctf/echoctf"
}

echoctf
install -d -o memcache /var/run/memcached
service memcached start
service mariadb start
service apache2 start
## socat tcp-l:2023,reuseaddr,fork exec:/bin/login,pty,setsid,setpgid,stderr,ctty
$@
