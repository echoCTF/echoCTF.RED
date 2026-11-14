#!/bin/sh
# One check for the disk space of all SNMP polled servers, through the local snmp_exporter.
# Exit 0 and one line when fine. Exit 1 naming the servers and mount points when not.
# env: MAXPCT  percent used above which a filesystem fails (default 90)
#      LIST    file_sd target list (default /etc/prometheus/targets/snmp.yml)
#      EXPORTER snmp_exporter address (default 127.0.0.1:9116)
#      AUTH    snmp_exporter auth name (default echo_v2)
#      FETCH   command that prints a URL to stdout (default OpenBSD ftp)
MAXPCT=${MAXPCT:-90}
LIST=${LIST:-/etc/prometheus/targets/snmp.yml}
EXPORTER=${EXPORTER:-127.0.0.1:9116}
AUTH=${AUTH:-echo_v2}
FETCH=${FETCH:-ftp -MVo -}

[ -r "$LIST" ] || { echo "cannot read $LIST"; exit 1; }

total=0 bad=0 list=
for ip in $(sed -n 's/.*targets:[^"]*"\([^"]*\)".*/\1/p' "$LIST"); do
  total=$((total + 1))
  out=$($FETCH "http://$EXPORTER/snmp?module=hrStorage&auth=$AUTH&target=$ip" 2>/dev/null)
  if ! printf '%s\n' "$out" | grep -q '^hrStorageSize{'; then
    bad=$((bad + 1))
    list="$list${list:+, }$ip unreachable"
    continue
  fi
  # filesystems are the rows named by their mount point
  full=$(printf '%s\n' "$out" | awk -v max="$MAXPCT" '
    /^hrStorage(Size|Used)\{/ {
      if (!match($0, /hrStorageDescr="\/[^"]*"/)) next
      d = substr($0, RSTART + 16, RLENGTH - 17)
      if (/^hrStorageSize/) size[d] = $NF; else used[d] = $NF
    }
    END {
      for (d in size) if (size[d] > 0 && 100 * used[d] / size[d] > max)
        printf "%s%s %d%%", (n++ ? " " : ""), d, 100 * used[d] / size[d]
    }')
  [ -n "$full" ] || continue
  bad=$((bad + 1))
  list="$list${list:+, }$ip $full"
done

[ "$total" -gt 0 ] || { echo "no targets in $LIST"; exit 1; }
[ "$bad" -eq 0 ] && { echo "all $total servers below ${MAXPCT}% disk"; exit 0; }
echo "$bad of $total servers failing: $list"
exit 1
