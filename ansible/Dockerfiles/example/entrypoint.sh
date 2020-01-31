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
  echo "${ipaddr}"
}

echoctf
## socat tcp-l:2023,reuseaddr,fork exec:/bin/login,pty,setsid,setpgid,stderr,ctty
$@
