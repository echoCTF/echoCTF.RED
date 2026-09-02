#!/bin/sh
# /usr/local/sbin/build-cloudblock
set -e
d=$(mktemp -d); trap 'rm -rf "$d"' EXIT

ftp -Vo "$d/aws.json"   "https://ip-ranges.amazonaws.com/ip-ranges.json"
ftp -Vo "$d/gcp.json"   "https://www.gstatic.com/ipranges/cloud.json"
ftp -Vo "$d/oci.json"   "https://docs.oracle.com/en-us/iaas/tools/public_ip_ranges.json"
ftp -Vo "$d/do.csv"     "https://digitalocean.com/geo/google.csv"
ftp -Vo "$d/linode.csv" "https://geoip.linode.com/"
ftp -Vo "$d/vultr.csv"  "https://geofeed.constant.com/"

touch "$d/vultr.csv"

az=$(ftp -Vo - "https://www.microsoft.com/en-us/download/details.aspx?id=56519" |
     grep -oE 'https://download\.microsoft\.com/[^"]*ServiceTags_Public_[0-9]+\.json' | head -1)
[ -n "$az" ] || { echo "azure url not found" >&2; exit 1; }
ftp -Vo "$d/azure.json" "$az"

for as in 16276 24940 51167 12876 9009 49505 53667 23470 62240 60068 396356 210644; do
  bgpq4 -S RIPE,APNIC,ARIN,LACNIC,AFRINIC -F  "%n/%l\n" AS$as >> "$d/asn.txt"
  bgpq4 -S RIPE,APNIC,ARIN,LACNIC,AFRINIC -6F "%n/%l\n" AS$as >> "$d/asn.txt"
done

python3 - "$d" > "$d/out.txt" <<'EOF'
import json, sys, ipaddress, pathlib
d = pathlib.Path(sys.argv[1]); nets = []
def add(p):
    try: nets.append(ipaddress.ip_network(p.strip(), strict=False))
    except ValueError: pass
j = json.loads((d/'aws.json').read_text())
for x in j['prefixes']:      add(x['ip_prefix'])
for x in j['ipv6_prefixes']: add(x['ipv6_prefix'])
for x in json.loads((d/'gcp.json').read_text())['prefixes']:
    add(x.get('ipv4Prefix') or x['ipv6Prefix'])
for r in json.loads((d/'oci.json').read_text())['regions']:
    for c in r['cidrs']: add(c['cidr'])
for v in json.loads((d/'azure.json').read_text())['values']:
    for p in v['properties']['addressPrefixes']: add(p)
for f in ('do.csv','linode.csv','vultr.csv'):
    for ln in (d/f).read_text().splitlines():
        if ln and not ln.startswith('#'): add(ln.split(',')[0])
p = d/'asn.txt'
if p.exists():
    for ln in p.read_text().splitlines(): add(ln)
v4 = [n for n in nets if n.version == 4]
v6 = [n for n in nets if n.version == 6]
for n in ipaddress.collapse_addresses(v4): print(n)
for n in ipaddress.collapse_addresses(v6): print(n)
EOF

n=$(wc -l < "$d/out.txt")
[ "$n" -ge 5000 ] || { echo "refusing: only $n prefixes" >&2; exit 1; }
grep -qE '^(0\.0\.0\.0/0|::/0)$' "$d/out.txt" && { echo "default route in list" >&2; exit 1; }

install -m 0644 "$d/out.txt" /etc/pf/cloudblock.txt
pfctl -t cloudblock -T replace -f /etc/pf/cloudblock.txt