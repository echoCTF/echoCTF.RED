#!/bin/sh
# nf-prom.sh [DATA_DIR] [OUT_FILE] [TOPN]
#
# Turns the newest COMPLETED nfcapd file of every exporter folder into
# Prometheus text metrics, for Grafana. Run it from cron right after each
# nfcapd rotation (every 5 minutes with the default 300 s rotation).
#
#   DATA_DIR  nfcapd -M folder, one sub folder per exporter   (default /var/nfcapd/exp)
#   OUT_FILE  metrics file, written atomically                (default /var/lib/node_exporter/textfile/nfdump.prom)
#   TOPN      top IPs per exporter to publish                 (default 10)
#
# Metrics are gauges for ONE rotation window, not running totals.
#   nfdump_window_flows{exporter}, nfdump_window_packets{exporter}, nfdump_window_bytes{exporter}
#   nfdump_top_src_bytes{exporter,ip}   nfdump_top_dst_bytes{exporter,ip}
#   nfdump_newest_file_age_seconds{exporter}   grows when an exporter goes silent
#   nfdump_exporter_count, nfdump_script_last_run_timestamp_seconds
#
# NFDUMP can point at another binary. Needs nfdump, awk, date, cut, sort.
# The file age is measured from the file name, which is the START of the window,
# so a healthy exporter shows roughly one rotation interval plus a little.
DATA=${1:-/var/nfcapd/exp}
OUT=${2:-/var/lib/node_exporter/textfile/nfdump.prom}
TOPN=${3:-10}
NFDUMP=${NFDUMP:-nfdump}

tmp="$OUT.$$"
now=$(date +%s)
n=0
{
  echo "# HELP nfdump_window_flows Flows in the newest completed nfcapd file."
  echo "# TYPE nfdump_window_flows gauge"
  echo "# HELP nfdump_window_packets Packets in the newest completed nfcapd file."
  echo "# TYPE nfdump_window_packets gauge"
  echo "# HELP nfdump_window_bytes Bytes in the newest completed nfcapd file."
  echo "# TYPE nfdump_window_bytes gauge"
  echo "# HELP nfdump_top_src_bytes Bytes sent by the top source IPs in that file."
  echo "# TYPE nfdump_top_src_bytes gauge"
  echo "# HELP nfdump_top_dst_bytes Bytes received by the top destination IPs in that file."
  echo "# TYPE nfdump_top_dst_bytes gauge"
  echo "# HELP nfdump_newest_file_age_seconds Age of the newest completed file."
  echo "# TYPE nfdump_newest_file_age_seconds gauge"
  for d in "$DATA"/*/; do
    [ -d "$d" ] || continue
    ex=$(basename "$d")
    # newest completed file: names are nfcapd.YYYYMMDDhhmmss, nfcapd.current.* is still being written
    f=$(ls -1 "$d" 2>/dev/null | grep -E '^nfcapd\.[0-9]{12,14}$' | sort | tail -1)
    [ -n "$f" ] || continue
    n=$((n + 1))
    p="$d$f"
    ts=${f#nfcapd.}
    # file time is in the collector's local time zone (nfcapd default); names are
    # YYYYMMDDhhmm (12 digits) or YYYYMMDDhhmmss (14 digits, short rotation intervals)
    y=$(echo "$ts" | cut -c1-4); mo=$(echo "$ts" | cut -c5-6); dd=$(echo "$ts" | cut -c7-8)
    hh=$(echo "$ts" | cut -c9-10); mi=$(echo "$ts" | cut -c11-12)
    ss=$(echo "$ts" | cut -c13-14); [ -n "$ss" ] || ss=00
    if fs=$(date -j -f "%Y%m%d%H%M%S" "$y$mo$dd$hh$mi$ss" +%s 2>/dev/null); then :
    else fs=$(date -d "$y-$mo-$dd $hh:$mi:$ss" +%s 2>/dev/null); fi
    [ -n "$fs" ] && echo "nfdump_newest_file_age_seconds{exporter=\"$ex\"} $((now - fs))"
    $NFDUMP -r "$p" -I 2>/dev/null | awk -v ex="$ex" '
      $1=="Flows:"   {fl=$2}
      $1=="Packets:" {pk=$2}
      $1=="Bytes:"   {by=$2}
      END { printf "nfdump_window_flows{exporter=\"%s\"} %d\nnfdump_window_packets{exporter=\"%s\"} %d\nnfdump_window_bytes{exporter=\"%s\"} %d\n", ex, fl, ex, pk, ex, by }'
    $NFDUMP -r "$p" -s srcip/bytes -n "$TOPN" -o csv -q 2>/dev/null | awk -F, -v ex="$ex" 'NF>=10 && $1!="ts" {printf "nfdump_top_src_bytes{exporter=\"%s\",ip=\"%s\"} %d\n", ex, $5, $10}'
    $NFDUMP -r "$p" -s dstip/bytes -n "$TOPN" -o csv -q 2>/dev/null | awk -F, -v ex="$ex" 'NF>=10 && $1!="ts" {printf "nfdump_top_dst_bytes{exporter=\"%s\",ip=\"%s\"} %d\n", ex, $5, $10}'
  done
  echo "# TYPE nfdump_exporter_count gauge"
  echo "nfdump_exporter_count $n"
  echo "# TYPE nfdump_script_last_run_timestamp_seconds gauge"
  echo "nfdump_script_last_run_timestamp_seconds $now"
} > "$tmp" && mv "$tmp" "$OUT"
