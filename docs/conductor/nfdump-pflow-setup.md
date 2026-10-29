# Network usage monitoring with pflow and nfdump

Scope: usage and trend visibility per IP and per server, using OpenBSD's pflow as the exporter and nfdump as the collector. Availability monitoring is out of scope.

## 0. How to read this document

Every load-bearing claim carries one of these marks:

* **[tested]** Verified in a lab setup: nfdump 1.7.10 built from the upstream tag v1.7.10 on Linux (the same version as the OpenBSD port), fed with synthetic NetFlow v5 and IPFIX packets of known sizes. Byte totals returned by nfdump matched what was sent.
* **[read]** Taken from a man page, port file or upstream README, not run.
* **[assumed]** Standard practice or reasoning, not verified.

Not tested, check on one real exporter before relying on it:

1. Everything on the OpenBSD side: `pflow0`, `hostname.pflow0`, the `(pflow)` pf option, and what the exported records actually contain (original client addresses or translated addresses). Only synthetic packets were used.
2. The NfSen web interface (section 7).
3. Disk usage per day. No real traffic was measured.

Placeholders used in examples:

| Placeholder | Meaning |
|---|---|
| `192.0.2.10` | An exporter's internal address |
| `192.0.2.20` | The collector's internal address |
| `198.51.100.5` | A client address seen in flows |
| `198.51.100.0/24` | A client subnet |
| `<internal-if>` | Internal network interface |
| `<client-if>` | Interface carrying the traffic you want to account |

## 1. Architecture

```
Exporter (OpenBSD)                            Collector
  pf rule with (pflow)  ->  pflow0  --UDP-->  nfcapd -M  ->  files per exporter  ->  nfdump / NfSen
  (one per server)                            (one host)
```

* No dedicated network interface is needed. `pflow0` is a pseudo-device and sends ordinary UDP to the collector address. [read]
* Send the flows over an internal management network, not over the client-facing path. pflow is plain UDP with no encryption and no acknowledgement. [read for UDP, rest assumed]
* The collector can be any internal host with disk space, for example a monitoring host.

## 2. Collector setup (OpenBSD)

### 2.1 Install

```
pkg_add nfdump
```

* The port is nfdump 1.7.10 and is built with nfprofile, which NfSen needs. [read, port Makefile]
* The package creates the user and group `_nfcapd`. [read, port PLIST]
* The port's file list shows no rc.d script for nfcapd, so start it yourself (2.3). [read, port PLIST]

### 2.2 Directories

```
mkdir -p /var/nfcapd/exp /var/nfcapd/run
chown -R _nfcapd:_nfcapd /var/nfcapd
```

* The data directory must exist before nfcapd starts. [tested with `-w`: it refuses to start with "path does not exist". The same check for `-M` was not run, so create it anyway]
* The pid file directory must be writable by the user nfcapd switches to. [tested: with `-u nobody` and a pid file in a root-owned directory, nfcapd did not start, printed nothing and left no pid file. Moving the pid file into a directory owned by that user fixed it]

### 2.3 Start nfcapd

```
nfcapd -D -u _nfcapd -g _nfcapd -b 192.0.2.20 -p 9995 -t 300 -M /var/nfcapd/exp -P /var/nfcapd/run/nfcapd.pid
```

What each flag does: [read in nfcapd man page, flags marked tested were run]

1. `-D` runs in the background. [tested]
2. `-u` and `-g` drop privileges after start. [tested with nobody and nogroup]
3. `-b` binds to one address. [tested with 127.0.0.1]
4. `-p 9995` is the default port. [read]
5. `-t 300` starts a new file every 300 s, which is the default. [read] Tests used `-t 2` to get files quickly.
6. `-M dir` creates one sub folder per exporter IP automatically, with dots replaced by dashes, for example `192-0-2-10`. [tested] This is the right mode for many exporters, no per-server config.
7. `-P` writes the pid file. [tested]

Rules that bite:

1. Do not combine `-w` or `-I` with `-M` or `-n`. [tested] nfcapd starts without complaint and puts everything in the default folder, so the per-exporter folders stay empty. The man page says not to mix them. [read]
2. With `-M` the collector accepts flows from ANY sender. [tested: a junk packet from an unknown address created its own exporter folder] Restrict the sender addresses with pf on the collector (2.5).
3. The data directory stays empty until the first flow arrives. [tested] An empty directory means nothing has reached the collector.

Boot start, simplest option: put the command above in `/etc/rc.local`. [assumed, standard OpenBSD practice]

### 2.4 Alternative to -M: fixed exporter list

```
nfcapd -D -u _nfcapd -g _nfcapd -b 192.0.2.20 -p 9995 -n 'srv01,192.0.2.10,/var/nfcapd/srv01' -n 'srv02,192.0.2.11,/var/nfcapd/srv02'
```

* Each `-n ident,IP,dir` must have its directory created first.
* Packets from addresses not listed are silently dropped. [tested] That makes it a built-in allow list, at the cost of one entry per exporter to maintain.
* Several addresses can share one `-n` using a semicolon list inside single quotes. [read]

### 2.5 Collector firewall

```
table <flow_exporters> persist file "/etc/pf.flow_exporters"
pass in proto udp from <flow_exporters> to 192.0.2.20 port 9995
block in proto udp to 192.0.2.20 port 9995
```

[assumed, standard pf syntax, not run]. `/etc/pf.flow_exporters` holds the exporters' internal addresses, one per line.

### 2.6 Retention

```
nfexpire -u /var/nfcapd/exp/192-0-2-10 -s 20g -t 14d
nfexpire -e /var/nfcapd/exp/192-0-2-10
nfexpire -l /var/nfcapd/exp/192-0-2-10
```

* `-u` stores limits (once per exporter folder), `-e` expires old files, `-l` lists first and last file, size and limits. [tested on a copy of a data folder: a 4k size limit removed all rotated files and kept the current one]
* Run `-e` from cron for every exporter folder. Whether `nfcapd -e` applies the same limits on its own was not tested.
* Size the limits from real data: after one day of traffic run `du -sk /var/nfcapd/exp/*`, then multiply by the retention you want.

## 3. Exporter setup (each OpenBSD server)

### 3.1 pflow0

Immediate, no reboot: [read, ifconfig(8)]

```
ifconfig pflow0 create
ifconfig pflow0 flowsrc 192.0.2.10 flowdst 192.0.2.20:9995 pflowproto 10
```

* `flowsrc` is the server's own internal address. It is what identifies the server to the collector, and it must be unique per server.
* `flowdst` needs both address and port or nothing is exported. [read]
* `pflowproto` defaults to 5. [read, ifconfig(8)] Use 10 (IPFIX). Neither man page mentions IPv6 for version 5, so IPFIX is the safe choice. [read: not mentioned]
* The collector side of IPFIX, including an IPv6 flow, was tested with a synthetic packet. OpenBSD's own IPFIX template was not tested.

Persistent: `/etc/hostname.pflow0` holds the same arguments on one line:

```
flowsrc 192.0.2.10 flowdst 192.0.2.20:9995 pflowproto 10
```

Lines that are not a special format are passed to ifconfig. [read, hostname.if(5)] Whether the interface needs an explicit `up`, and applying it with `sh /etc/netstart pflow0`, are [assumed]. Test on one server first.

### 3.2 pf rules

Only states created by a rule marked `pflow` are exported. [read] The option goes in the state options and needs an explicit `keep state`: [read, pf.conf(5)]

```
pass in on <client-if> proto { tcp udp icmp } from any to any keep state (pflow)
```

This is a pattern, not a ruleset. Add `(pflow)` to the rules that pass the traffic you want to account. Apply with `pfctl -f /etc/pf.conf`. [assumed]

Points to check on the first server:

1. Which addresses appear in the records: the original client address, or an address after NAT. This decides whether per-client queries work. [not verified]
2. Whether one connection is exported once or twice when both an `in` and an `out` rule carry `(pflow)`. [not verified]
3. Do not put `(pflow)` on the rule that passes the flow export traffic itself, or the exporter reports its own export packets. [assumed]

Records can be delayed. pflow does not hold a record longer than 30 seconds. [read] A test flow shows up in nfdump tens of seconds later, not instantly.

## 4. End-to-end check

Run these in order. Stop at the first failure.

1. Exporter: `ifconfig pflow0` shows the flowsrc and flowdst you set. [assumed]
2. Exporter: `tcpdump -ni <internal-if> udp port 9995` shows packets leaving. [assumed]
3. Collector: `tcpdump -ni <internal-if> udp port 9995` shows packets arriving. [assumed]
4. Collector: `netstat -an -f inet | grep 9995` shows the UDP listener. [assumed]
5. Collector: a folder named after the exporter appears under `/var/nfcapd/exp`. [tested for the folder creation]
6. Collector: after the next rotation a file `nfcapd.YYYYMMDDHHMMSS` appears, besides the `nfcapd.current.*` files. [tested]
7. `nfdump -R /var/nfcapd/exp/192-0-2-10 -c 5 -N` prints flows. [tested form]

## 5. Common operations

All commands below are [tested] on nfdump 1.7.10 against synthetic flows with known totals. `D=/var/nfcapd/exp`. Short flags are used throughout. `-N` prints plain numbers, `-n` limits rows, `-R` reads a folder recursively.

### 5.1 Per IP

1. Top senders by bytes, all exporters: `nfdump -R $D -s srcip/bytes -n 20 -N`
2. Top receivers: `nfdump -R $D -s dstip/bytes -n 20 -N`
3. Total traffic of one IP in both directions: `nfdump -R $D 'ip 198.51.100.5' -N` (read the Summary line)
4. Only what one IP sent: `'src ip 198.51.100.5'`. Only what it received: `'dst ip 198.51.100.5'`
5. A whole subnet: `'src net 198.51.100.0/24'`
6. Largest single flows of one IP: `nfdump -R $D 'src ip 198.51.100.5' -O bytes -n 10 -N`
7. One row per IP with totals: `nfdump -R $D -A srcip -O bytes -n 10 -N`

Warning: `-s ip/bytes` lists both ends of every flow as separate rows. A client and the remote server both appear. Do not add those rows together. [tested]

### 5.2 Per server

1. One server: read its folder, `nfdump -R $D/192-0-2-10 -N`. [tested]
2. Same through a filter on the whole tree: `nfdump -R $D 'exporter ip 192.0.2.10' -N`. [tested]
3. Do not rely on `exporter id`. In testing, id 1 matched the first sender to arrive, so it looks like an arrival-order number, not a stable server identity. Use `exporter ip` or the folder. [tested for the behaviour, the reason is inferred]
4. All servers ranked by bytes: [tested]

```
for s in $D/*; do
  nfdump -R "$s" -N 2>/dev/null |
    awk -v n="${s##*/}" '/^Summary:/{gsub(/,/,""); print n, $4, $7}'
done | sort -k3 -rn
```

Output is `folder flows bytes`, largest first.

5. Show what a folder holds: `nfdump -R $D/192-0-2-10 -I` for totals per protocol, `nfexpire -l $D/192-0-2-10` for first and last file and size. [tested]

### 5.3 Time windows

1. File range inside one folder: `nfdump -R $D/192-0-2-10/nfcapd.202601011300:nfcapd.202601011400 -N`. [tested, files in one folder]
2. `-t` window: `nfdump -R $D -t 2026/01/01.13:00:00-2026/01/01.14:00:00 -N`. [tested] The man page marks `-t` as legacy. [read]
3. Filter form: `nfdump -R $D -N 'first seen >= 2026-01-01T13:00:00 and last seen <= 2026-01-01T14:00:00'`. [tested] The time must use the `T` separator. A space between date and time gave a syntax error. [tested]
4. A window with no data prints zero flows and a total of zero, it is not an error. [tested]

### 5.4 Breakdowns

1. By destination port: `nfdump -R $D -s dstport/bytes -n 10 -N`
2. By protocol: `nfdump -R $D -s proto/bytes -N`
3. Order options after the slash: `flows`, `packets`, `bytes`, `pps`, `bps`, `bpp`. [read]

### 5.5 For scripts and dashboards

1. CSV: `nfdump -R $D -s srcip/bytes -n 10 -o csv -q`. Columns are `ts,te,td,pr,val,fl,flP,pkt,pktP,byt,bytP,pps,bps,bpp`. [tested]
2. JSON per flow: `nfdump -R $D/192-0-2-10 -o json -q`. Each record carries `ident` (the exporter folder name) and `ip4_router` (the exporter address). [tested]

### 5.6 Subfolders by date (optional)

`nfcapd -S 1` stores files under `year/month/day`. [tested: files landed in `.../YYYY/MM/DD/` and `nfdump -R` on the top folder still found them] The file-range form in 5.3 was not tested across these subfolders.

## 6. Troubleshooting

| Symptom | Check | Notes |
|---|---|---|
| No exporter folder appears | Is nfcapd running? Is the sender allowed by collector pf? Is the port right? | Folders appear only when the first packet arrives. [tested] A packet sent to the wrong port produced nothing. [tested] |
| Everything lands in the default folder, exporter folders empty | `-w` or `-I` combined with `-n` or `-M` | [tested] Remove `-w` and `-I`. |
| nfcapd starts, no process, no output, no pid file | Pid file directory writable by the `-u` user? | [tested] This case is silent. |
| "path does not exist" on start | Create the data directory first | [tested with `-w`, it prints this error] |
| Folder exists but only `nfcapd.current.*` files | Wait one rotation interval (`-t`). With 300 s that is up to 5 minutes. | [tested] |
| Flows arrive late | pflow holds records up to 30 s | [read] |
| Unknown folders appear on the collector | Something sent traffic to port 9995 from an address that is not an exporter | [tested] Fix with the collector pf rules (2.5), then delete the folder. |
| Records from one server missing, others fine | `flowdst` has both address and port? `(pflow)` is on the rules that match the traffic? Exporter can reach the collector? | [read] for the flowdst and pflow rule requirements. |
| Same traffic counted twice | `(pflow)` on rules in both directions for one connection, or reading `-s ip/bytes` as a total | Second is [tested]. First [not verified]. |
| Per-client numbers show NAT addresses | pflow records states as pf sees them. Check where the `(pflow)` rule sits relative to NAT | [not verified] |
| No IPv6 flows | Use `pflowproto 10`. Version 5 is the default. | Default is [read]. IPv6 behaviour of v5 is [not stated in the man pages]. |
| nfdump says syntax error on a time filter | Use `2026-01-01T13:00:00`, not a space | [tested] |
| `exporter id` filter returns an unexpected server | Use `exporter ip` or the per-server folder | [tested] |
| Disk filling | `nfexpire -l` per folder, then set `-s` or `-t` with `-u` and run `-e` from cron | [tested on a copy] |
| Packets dropped under load | nfcapd has `-B` to raise the socket buffer. Not tested, no load was generated | [read] |

Where nfcapd logs: it logs through syslog when it runs as a daemon. [assumed] Check `/var/log/daemon` on OpenBSD. A start-up failure may print nothing at all (see the table), so check the process and the pid file first.

## 7. Web interface

Two candidates:

1. **NfSen** (Perl and PHP front end for nfdump).
   * Present in the OpenBSD ports at version 1.3.11p2, with runtime dependencies rrdtool, p5-Mail-Tools, p5-Socket6 and nfdump. [read, port Makefile]
   * The port README says the `_nfcapd` user is added to group `www`, and that pflow export from pf can feed it. [read]
   * Upstream 1.3.12 on GitHub states it works with nfdump 1.7.x and 1.8.x. The same README says NfSen is no longer under development. [read]
   * Open question: the port is 1.3.11, not 1.3.12. Whether 1.3.11 runs against nfdump 1.7.10 is [not verified]. The fallback is installing 1.3.12 from GitHub by hand.
   * NfSen starts its own collector processes from its own config (a list of sources and ports), so it replaces the collector setup in section 2 instead of running next to it. Do not run a second nfcapd on the same port. [assumed]
   * NfSen needs one system semaphore per netflow source, so with many sources check the OpenBSD semaphore limits. [read, NfSen README]
   * The NfSen README says nfdump must be built with `--enable-nfprofile`, which the OpenBSD port does. [read]
2. **nfsen-ng** (modern front end).
   * Needs PHP 8.4 and the OpenSwoole extension and is described as Linux only, with no maintained BSD port. [read, project page]
   * Usable only if the collector is a Linux host.

Neither was run. Test NfSen on a throwaway OpenBSD box before committing to it. If the web UI turns out to be a blocker, the command-line queries in section 5 and the CSV output cover most needs.

## 8. Rollout checklist

1. Build one exporter end to end (sections 3 and 4) and confirm which addresses appear in the records.
2. Settle the client-address question (3.2 point 1) before rolling out to the other servers.
3. Roll `hostname.pflow0` and the pf change out with your configuration management, one server at a time.
4. Lock the collector port to the exporter addresses (2.5).
5. After 24 hours, measure disk (`du -sk /var/nfcapd/exp/*`) and set retention (2.6).
6. Add the collector itself to your availability monitoring: process alive and disk free, so a dead collector does not go unnoticed.
