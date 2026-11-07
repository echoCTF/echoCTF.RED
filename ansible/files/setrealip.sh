#!/bin/ksh
hash=$(mktemp -t cloudflare.XXXXXXXXXX) || exit 1
trap 'rm -f "$hash"' EXIT

md5 -h "$hash" /etc/cloudflare.conf
curl -s -L https://www.cloudflare.com/ips-v4 >/etc/cloudflare.conf
md5 -qC "$hash" /etc/cloudflare.conf
if [ $? -eq 0 ]; then
  exit 0
fi
(for i in $(</etc/cloudflare.conf);do echo "set_real_ip_from $i;";done; echo "real_ip_header CF-Connecting-IP;"; echo "real_ip_recursive on;") > /etc/nginx/cloudflare.conf
rcctl reload participant nginx
