#!/bin/bash
function do_exit()
{
  echo "$2"
  exit $1
}

nc -z localhost 375 || do_exit 1 "Port 375/tcp down"

#curl -s -f http://localhost:10888/ >/dev/null || exit 1

# for memcached
#MEMFLAG=$(echo -e "get ETSCTF\r"|nc -N localhost 11211|sha256sum|awk '{print $1}')
#[ "$MEMFLAG" == "" ] || exit 2

# for interesting files
sha512sum -c --status < /usr/local/lib/.sha512sum || exit 1

# for environment variable
ENVFLAG=$(env|grep ETSCTF_FLAG|sha256sum|awk '{print $1}')
[ "$ENVFLAG" == "ENVFLAG_HASH" ] || exit 3
