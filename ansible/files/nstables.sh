#!/bin/sh
#
# Query an FQDN to extract TXT record with list of IP's to be allowed access to the system
# IPs must be space separated
#
# mytxt.example.com IN TXT "1.1.1.1 1.2.3.4 1.2.0.0/24 example.ddns.net"
#
# example: nstables mytxt.example.com mytable
#

_DOMAIN=${1:-"zone.example.com"}
_TABLE=${2:-"administrators"}
echo "Settings: domain=>${_DOMAIN} table=>${_TABLE}"
echo "Fetching TXT record"
_append=$(host -t txt ${_DOMAIN}|grep -v TXT |awk -F'"' '{print $2}')
if [ "${_append}" != "" ]; then
  if [ $(uname) = "Linux" ]; then
    _tmp="${_TABLE}_tmp"
    ipset create -exist ${_TABLE} hash:net || exit 1
    ipset destroy ${_tmp} 2>/dev/null
    ipset create ${_tmp} hash:net || exit 1
    for _item in ${_append};do
        case "${_item}" in
          *[!0-9./]*)
            echo "Invalid IP address or input: ${_item}"
            _item=$(host ${_item}|grep 'has address'| awk '{print $NF}')
            ;;
        esac
        for _ip in ${_item}; do
          ipset add -exist ${_tmp} ${_ip}
        done
    done
    _new=$(ipset save ${_tmp} | awk '/^add/{print $3}' | sort)
    _cur=$(ipset save ${_TABLE} | awk '/^add/{print $3}' | sort)
    if [ -z "${_new}" ]; then
      echo "Nothing valid found, keeping current ${_TABLE}"
    elif [ "${_new}" = "${_cur}" ]; then
      echo "No change"
    else
      ipset swap ${_tmp} ${_TABLE} && netfilter-persistent save
    fi
    ipset destroy ${_tmp}
  else
    pfctl -t ${_TABLE} -T replace ${_append}
  fi
fi
