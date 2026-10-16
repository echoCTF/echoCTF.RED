#!/bin/ksh
#
# uvm-snapshot.sh
#
# One-shot diagnostic capture. Run it manually, right now for a baseline,
# and again as soon as possible after/during the next uvm_mapent_alloc
# occurrence or stall. No background process; it runs once and exits (the
# last section samples "kernel map entries" once a second for BURST_SECS
# seconds, 30 by default, 0 disables it).
#
# Every run is saved to its own file in the state folder and nothing is ever
# overwritten or deleted, so all runs stay available. If two runs start in the
# same second the second one gets a .1, .2, ... suffix.
#
# Usage:
#   ./uvm-snapshot.sh            # prints to stdout AND saves a copy
#   STATE_DIR=/some/dir ./uvm-snapshot.sh   # use another state folder
#   BURST_SECS=0 ./uvm-snapshot.sh          # skip the 1 s burst sampler
#
# From cron (no mail, output is already saved to the state folder):
#   */10 * * * * /usr/local/sbin/uvm-snapshot.sh >/dev/null 2>&1
#
# Assumes a root .my.cnf (or socket auth) so `mysql -e` needs no password,
# and memcached on 127.0.0.1:11211. Adjust below if either differs.
#
# Format notes: every section header ("--- name ---") is followed by a
# "t=HH:MM:SS s=N" line (wall clock and seconds since the script started), so
# a slow run shows exactly which section was slow. The original section
# headers and their order are unchanged; new sections are marked NEW.

OUTDIR=${STATE_DIR:-/var/log/uvm-incidents}
MEMCACHED_HOST=127.0.0.1
MEMCACHED_PORT=11211
BURST_SECS=${BURST_SECS:-30}
MYSQL="mysql --connect-timeout=5"

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

# seconds since the script started (portable, does not rely on $SECONDS)
start=$(date +%s)
elapsed() { echo $(( $(date +%s) - start )); }

# section header + timing line
sec() {
	echo
	echo "--- $1 ---"
	echo "t=$(date +%H:%M:%S) s=$(elapsed)"
}

{
	echo "=== uvm-snapshot: $(date) ==="

	sec "dmesg: uvm_mapent_alloc / vio"
	dmesg | grep -E "uvm_mapent_alloc|vio" | tail -20

	sec "netstat -m"
	netstat -m

	sec "swap"
	swapctl -l

	sec "ifq"
	sysctl net.inet.ip.ifq.len net.inet.ip.ifq.maxlen net.inet.ip.ifq.drops
	sysctl net.inet6.ip6.ifq.len net.inet6.ip6.ifq.maxlen net.inet6.ip6.ifq.drops

	sec "vmstat -s"
	vmstat -s

	# auxww: no width limit, so the full command line (memcached -m, mysqld
	# options) is visible and the memcached grep below can match
	sec "top by CPU"
	ps auxww | sort -k3 -rn | head -20

	sec "top by RSS"
	ps auxww | sort -k6 -rn | head -20

	sec "memcached process + configured -m"
	ps auxww | grep '[m]emcached'

	sec "memcached stats"
	printf 'stats\r\nquit\r\n' | nc -w 1 "$MEMCACHED_HOST" "$MEMCACHED_PORT" 2>&1

	sec "mariadb: buffer pool size + thread status"
	$MYSQL -e "SHOW VARIABLES LIKE 'innodb_buffer_pool_size'; SHOW GLOBAL STATUS LIKE 'Thread%';" 2>&1

	# NEW: version, limits and the counters needed for rates (Questions,
	# Connections) and for connection headroom (max_connections vs
	# Max_used_connections). Kept right after the thread status so the
	# numbers are taken at nearly the same moment.
	sec "mariadb: version, limits, counters (NEW)"
	$MYSQL -e "SELECT VERSION() AS version; SHOW GLOBAL VARIABLES WHERE Variable_name IN ('max_connections','thread_handling','thread_pool_size','thread_pool_max_threads','thread_pool_idle_timeout','wait_timeout','interactive_timeout','innodb_log_file_size','innodb_flush_log_at_trx_commit'); SHOW GLOBAL STATUS WHERE Variable_name IN ('Uptime','Connections','Questions','Queries','Com_select','Com_insert','Com_update','Com_delete','Com_commit','Max_used_connections','Max_used_connections_time','Aborted_connects','Aborted_clients','Connection_errors_max_connections','Connection_errors_internal','Slow_queries','Created_tmp_disk_tables','Innodb_row_lock_waits','Innodb_row_lock_time','Innodb_log_waits','Bytes_received','Bytes_sent','Open_files','Opened_tables');" 2>&1

	sec "mariadb: processlist"
	$MYSQL -e "SHOW PROCESSLIST;" 2>&1

	# NEW: idle/active connection count and oldest age per client address
	sec "mariadb: processlist summary by client + command (NEW)"
	$MYSQL -e "SELECT SUBSTRING_INDEX(HOST,':',1) AS client, COMMAND, COUNT(*) AS n, MAX(TIME) AS max_time_s FROM information_schema.PROCESSLIST GROUP BY 1,2 ORDER BY n DESC LIMIT 60;" 2>&1

	sec "mariadb: innodb status"
	$MYSQL -e "SHOW ENGINE INNODB STATUS" 2>&1

	# only tcp/tcp6 rows (the old version also counted headers and unix
	# socket lines such as "Foreign", "0x0")
	sec "connection state totals, all ports"
	netstat -an | awk '$1 ~ /^tcp/ && NF>=6 {print $6}' | sort | uniq -c | sort -rn

	# NEW: which services hold the ESTABLISHED connections
	sec "ESTABLISHED by local port (NEW)"
	netstat -an | awk '$1 ~ /^tcp/ && $6=="ESTABLISHED" { n=split($4,a,"."); print a[n] }' | sort -n | uniq -c | sort -rn | head -20

	sec "TIME_WAIT by local port"
	netstat -an | awk '$6=="TIME_WAIT" { n=split($4,a,"."); print a[n] }' | sort -n | uniq -c | sort -rn | head -20

	sec "TIME_WAIT by remote host:port"
	netstat -an | awk '$6=="TIME_WAIT" { print $5 }' | sort | uniq -c | sort -rn | head -20

	# ---- NEW sections below. The slow ones (vmstat 1 5, the burst
	# sampler) come last so they do not delay the sections above. ----

	sec "host: kernel, cpu count, load, limits (NEW)"
	uname -srm
	sysctl kern.version hw.model hw.ncpu hw.ncpuonline hw.physmem
	sysctl kern.maxfiles kern.nfiles kern.maxproc kern.nprocs kern.nthreads
	uptime

	sec "uvm messages in /var/log/messages* (NEW)"
	for f in /var/log/messages*; do
		[ -f "$f" ] && echo "$f: $(zgrep -c 'uvm_mapent_alloc' "$f" 2>&1)"
	done
	zgrep -h 'uvm_mapent_alloc' /var/log/messages* 2>&1 | tail -5

	sec "pf state table + limits (NEW)"
	pfctl -si 2>&1 | head -14
	pfctl -sm 2>&1

	sec "vmstat -i, interrupts by source (NEW)"
	vmstat -i

	# who holds kernel memory: on OpenBSD 7.9 `vmstat -m` prints the malloc
	# buckets, the per-type usage and the full pool(9) table (Fail, Npage,
	# Hiwat), so no separate pool command is needed
	sec "vmstat -m, kernel malloc and pools (NEW)"
	vmstat -m

	# CPU idle: 5 lines, about 4 s. First line is the average since boot,
	# the next four are 1 s samples (read the "id" column).
	sec "vmstat 1 5, cpu states (NEW)"
	vmstat 1 5

	# the original uvm check: spot reads of this count every 10-20 min can
	# not see a short burst; this samples it once a second
	if [ "$BURST_SECS" -gt 0 ] 2>/dev/null; then
		sec "kernel map entries, 1 s samples for ${BURST_SECS}s (NEW)"
		i=0
		while [ "$i" -lt "$BURST_SECS" ]; do
			echo "$(date +%H:%M:%S) $(vmstat -s | grep 'kernel map entries')"
			sleep 1
			i=$((i + 1))
		done
	fi

	echo
	echo "=== done: $(date), $(elapsed)s ==="

} 2>&1 | tee "$out"

echo
echo "saved: $out"
