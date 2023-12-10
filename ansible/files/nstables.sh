#!/bin/ksh
#
# Query given NS server for a given FQDN mx record and add the IP entries into a given table
#
# example: nstables mytxt.example.com mytable
#
_DOMAIN=${1:-"zone.example.com"}
_TABLE=${2:-"administrators"}

echo "Settings: domain=>${_DOMAIN} table=>${_TABLE}"
echo "Fetching TXT record"
_append=$(host -t txt ${_DOMAIN}|grep -v TXT |awk -F'"' '{print $2}')
pfctl -t ${_TABLE} -T replace ${_DOMAIN} ${_append}
