#!/bin/ksh
#
# uvm-snapshot.sh
#
# One-shot diagnostic capture. Run it manually, right now for a baseline,
# and again as soon as possible after/during the next uvm_mapent_alloc
# occurrence or stall. No background process, no loop - runs once and exits.
#
# Every run is saved to its own file in the state folder and nothing is ever
# overwritten or deleted, so all runs stay available. If two runs start in the
# same second the second one gets a .1, .2, ... suffix.
#
# Usage:
#   ./uvm-snapshot.sh            # prints to stdout AND saves a copy
#   STATE_DIR=/some/dir ./uvm-snapshot.sh   # use another state folder
#
# From cron (no mail, output is already saved to the state folder):
#   */10 * * * * /usr/local/sbin/uvm-snapshot.sh >/dev/null 2>&1
#
# Assumes a root .my.cnf (or socket auth) so `mysql -e` needs no password,
# and memcached on 127.0.0.1:11211. Adjust below if either differs.

OUTDIR=${STATE_DIR:-/var/log/uvm-incidents}
MEMCACHED_HOST=127.0.0.1
MEMCACHED_PORT=11211

mkdir -p "$OUTDIR"
ts=$(date +%Y%m%d-%H%M%S)
out="$OUTDIR/snapshot-$ts.log"
# create the file atomically (noclobber) so concurrent or same-second runs
# never share or overwrite a file
n=0
until (set -C; : > "$out") 2>/dev/null; do
	n=$((n + 1))
	out="$OUTDIR/snapshot-$ts.$n.log"
	[ "$n" -gt 1000 ] && { echo "cannot create a snapshot file in $OUTDIR" >&2; exit 1; }
done

{
	echo "=== uvm-snapshot: $(date) ==="

	echo
	echo "--- dmesg: uvm_mapent_alloc / vio ---"
	dmesg | grep -E "uvm_mapent_alloc|vio" | tail -20

	echo
	echo "--- netstat -m ---"
	netstat -m

	echo
	echo "--- swap ---"
	swapctl -l

	echo
	echo "--- ifq ---"
	sysctl net.inet.ip.ifq.len net.inet.ip.ifq.maxlen net.inet.ip.ifq.drops
	sysctl net.inet6.ip6.ifq.len net.inet6.ip6.ifq.maxlen net.inet6.ip6.ifq.drops

	echo
	echo "--- vmstat -s ---"
	vmstat -s

	echo
	echo "--- top by CPU ---"
	ps aux | sort -k3 -rn | head -20

	echo
	echo "--- top by RSS ---"
	ps aux | sort -k6 -rn | head -20

	echo
	echo "--- memcached process + configured -m ---"
	ps aux | grep [m]emcached

	echo
	echo "--- memcached stats ---"
	printf 'stats\r\nquit\r\n' | nc -w 1 "$MEMCACHED_HOST" "$MEMCACHED_PORT" 2>&1

	echo
	echo "--- mariadb: buffer pool size + thread status ---"
	mysql -e "SHOW VARIABLES LIKE 'innodb_buffer_pool_size'; SHOW GLOBAL STATUS LIKE 'Thread%';" 2>&1

	echo
	echo "--- mariadb: processlist ---"
	mysql -e "SHOW PROCESSLIST;" 2>&1

	echo
	echo "--- mariadb: innodb status ---"
	mysql -e "SHOW ENGINE INNODB STATUS" 2>&1

	echo
	echo "--- connection state totals, all ports ---"
	netstat -an | awk 'NF>=6 {print $6}' | sort | uniq -c | sort -rn

	echo
	echo "--- TIME_WAIT by local port ---"
	netstat -an | awk '$6=="TIME_WAIT" { n=split($4,a,"."); print a[n] }' | sort -n | uniq -c | sort -rn | head -20

	echo
	echo "--- TIME_WAIT by remote host:port ---"
	netstat -an | awk '$6=="TIME_WAIT" { print $5 }' | sort | uniq -c | sort -rn | head -20

} 2>&1 | tee "$out"

echo
echo "saved: $out"
