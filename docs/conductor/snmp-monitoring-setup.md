# Server and network metrics with SNMP

Scope: CPU, memory, swap, filesystems, interface traffic and pf health per OpenBSD server, collected with snmp_exporter and Prometheus.

## 0. How to read this document

Every load-bearing claim carries one of these marks:

* **[tested]** Verified in a lab setup. The agent in testing was net-snmp on Linux, not OpenBSD's snmpd. Polling, exporter modules and v3 authentication were tested against it.
* **[read]** Taken from OpenBSD's own source tree (snmpd, snmpd_metrics, the MIB files, the rc script) or a man page. Not run on OpenBSD.
* **[assumed]** Standard practice or reasoning, not verified.

Not tested, check on one real server first:

1. What OpenBSD's snmpd actually returns. The source was read, a live snmpd was not queried.
2. SNMPv3 interoperability between snmp_exporter and OpenBSD's snmpd. Only the exporter against net-snmp was tested. The algorithm mapping was read in OpenBSD's parser.
3. Poll time for a large number of servers.

Placeholders used in examples:

| Placeholder | Meaning |
|---|---|
| `192.0.2.10` | A monitored server's internal address |
| `<monitor-host>` | The monitoring host's address |
| `<internal-if>` | Internal network interface on the server |
| `CHANGEME-*` | Community string and SNMPv3 keys, set your own |

## 1. What SNMP gives you on OpenBSD

snmpd starts every program in `/usr/libexec/snmpd` as a backend on its own, which includes the metrics subagent `snmpd_metrics`. Nothing extra has to be enabled. [read, snmpd.c]

`snmpd_metrics` exports HOST-RESOURCES-MIB, IF-MIB, OPENBSD-PF-MIB, OPENBSD-SENSORS-MIB, OPENBSD-CARP-MIB, OPENBSD-MEM-MIB, IP-MIB, IP-FORWARD-MIB, UCD-DISKIO-MIB and BRIDGE-MIB, described as partial. [read, snmpd_metrics.8]

What that means in practice: [read, snmpd_metrics source]

1. **CPU:** `hrProcessorLoad`, one row per processor. It is the percent of time not idle during the last minute, not an instant value.
2. **Memory:** `hrStorageTable` rows "Physical memory" and "Real memory". Used is computed as total minus free pages, so buffer cache counts as used. Expect the memory line to sit high on a healthy OpenBSD box.
3. **Swap:** `hrStorageTable` row "Swap space".
4. **Filesystems:** one `hrStorageTable` row per mounted filesystem, named by mount point, for example `/` and `/var`.
5. **Interfaces:** `ifTable` and `ifXTable`, including the 64-bit counters `ifHCInOctets` and `ifHCOutOctets`. Use the 64-bit ones. Errors and discards are there too.
6. **pf:** `pfStateCount`, state searches, inserts and removals, the match and drop counters (`pfCntStateLimit`, `pfCntSrcLimit`, `pfCntMemory`, `pfCntCongestion` and others), source tracking counts and the configured limits.
7. **Memory pools per interface:** `memIfLiveLocks` from OPENBSD-MEM-MIB.
8. **Processes:** `hrSWRunTable` and per-process CPU and memory.

What it does not give you:

1. **No load average.** The UCD load table is not in the source. [read]
2. **No per-IP or per-client data.** Use flow export (see the pflow and nfdump document).
3. **No application metrics** for services such as memcached or MariaDB. Those need their own exporters (see the Grafana document).

## 2. Setup on each OpenBSD server

### 2.1 /etc/snmpd.conf

Option A, SNMPv2c with a read-only community, bound to the internal address:

```
listen on 192.0.2.10 snmpv2c
read-only community CHANGEME-community
```

Option B, SNMPv3 with authentication and encryption:

```
listen on 192.0.2.10 snmpv3
seclevel enc
user "mon" authkey "CHANGEME-authpass" auth hmac-sha256 enckey "CHANGEME-privpass" enc aes
```

Rules that matter, all [read, snmpd.conf(5)]:

1. **If there is no `listen on` line, snmpd listens on every address.** Always write one.
2. **A `listen on` line with no protocol flag defaults to `snmpv3`.** For a community string you must write `snmpv2c`, as in option A.
3. The default `seclevel` for SNMPv3 is `enc`, so v3 needs both an auth key and an encryption key.
4. `read-only community` has no default value, so you must set one.
5. Use the server's internal management address, not a public or client-facing one.

Option A is simpler and sends the community in clear text. On a closed internal network with pf limiting who can reach UDP 161 that is a defensible choice. Option B costs one more key to manage. Prefer B if the monitoring host reaches the servers over a shared network.

In the `user` line the keywords `authkey`, `auth`, `enckey` and `enc` can come in any order. The man page synopsis and its example differ only in that order. [read, parse.y grammar]

The algorithm names map as follows. `hmac-sha256` is `usmHMAC192SHA256AuthProtocol` and `aes` is `cfb128-aes-128`. [read, parse.y] Those are the same algorithms as `SHA256` and `AES` in the exporter's v3 auth, which is why the exporter side was tested against net-snmp with those settings. Interop with OpenBSD's own snmpd is still [not verified].

### 2.2 Start

```
rcctl enable snmpd
rcctl start snmpd
```

[assumed, standard rcctl usage. The rc script exists in the source tree and is a plain daemon script, with a config test that runs `snmpd -n`.] [read]

### 2.3 Config test

```
snmpd -n
```

The rc script uses this as its config test. [read]

### 2.4 pf on each server

Allow the monitoring host only:

```
pass in on <internal-if> proto udp from <monitor-host> to (<internal-if>) port 161
```

[assumed, standard pf syntax, not run]. Without a rule like this and with no `listen on` restriction, anyone who can reach the server can query it.

### 2.5 Check it works from the monitoring host

```
snmpwalk -v2c -c CHANGEME-community 192.0.2.10 1.3.6.1.2.1.25.3.3.1.2
snmpwalk -v3 -l authPriv -u mon -a SHA-256 -A CHANGEME-authpass -x AES -X CHANGEME-privpass 192.0.2.10 1.3.6.1.2.1.25.3.3.1.2
```

* The syntax is net-snmp's and was [tested] against the stand-in agent, including SHA-256 with AES.
* The OID is the CPU load column. A number per processor means CPU is working.
* If it times out, check in this order: `listen on` address, the protocol flag (section 2.1 rule 2), the pf rule, the community or user.

## 3. The collector side

Run these on the monitoring host. It can be Linux or OpenBSD. OpenBSD has ports for snmp_exporter (0.30.1), Prometheus (3.5.2) and Grafana (13.1.5). [read, openports.pl] Testing used older versions, see section 6.

### 3.1 snmp_exporter

snmp_exporter polls the servers and turns SNMP into Prometheus metrics. The stock `snmp.yml` that ships with it has modules for the standard MIBs. [tested with 0.26.0]

| What | Module | Result |
|---|---|---|
| Interfaces, 64-bit counters | `if_mib` | `ifHCInOctets`, `ifHCOutOctets`, errors, discards, status [tested] |
| Memory, swap, filesystems | `hrStorage` | `hrStorageUsed`, `hrStorageSize`, `hrStorageAllocationUnits` [tested] |
| CPU | `hrDevice` | `hrProcessorLoad` per processor [tested] |
| pf and memory pools | `openbsd_pf` (you generate it) | 36 metrics, see 3.2 [generated and tested against a fake agent] |

### 3.2 The OpenBSD module

The stock file has nothing for pf. Generate a module from OpenBSD's own MIB files with the exporter's generator. [tested]

1. Copy the MIB files from an OpenBSD server, `/usr/share/snmp/mibs/` [read in snmpd(8)], to the monitoring host. The same files are in the OpenBSD source tree under `share/snmp`.
2. Install the generator. It needs Go and the net-snmp development headers: `apt install libsnmp-dev` then `go install github.com/prometheus/snmp_exporter/generator@v0.26.0`. [tested on Linux]
3. Use the companion `generator.yml`. Fill in the `CHANGEME` values to match section 2.1.
4. Generate:

```
MIBDIRS=/path/to/mibs ./generator generate -m /path/to/mibs -g generator.yml -o snmp-openbsd.yml
```

[tested] The run printed 36 metrics for the module.

5. Start one exporter with both files. The exporter accepts `--config.file` twice. [tested]

```
snmp_exporter --config.file=snmp.yml --config.file=snmp-openbsd.yml
```

6. Ask for several modules in one request: `/snmp?module=if_mib&module=hrStorage&module=hrDevice&module=openbsd_pf&target=192.0.2.10&auth=site_v2`. [tested, 4 modules in one scrape]

The generator file defines two auths, `site_v2` (community) and `site_v3` (SHA-256 and AES). Use names that do not clash with the stock file's `public_v2`, or the merge may refuse them. [the clash itself was not tested, it was avoided]

### 3.3 One thing not to walk

Do not add `pfTables` or the per-table rows to the module. pf publishes statistics per table, so a ruleset with many tables (for example thousands of per-client tables) turns every scrape into a very large walk on every server. [assumed, from the MIB layout] The companion generator file leaves them out on purpose.

### 3.4 What the pf metrics tell you

These are the numbers that match the limits in a typical pf.conf: [read in the MIB names, meaning from pf]

1. `pfStateCount` against `pfLimitStates`: how close the state table is to the cap.
2. `rate(pfCntStateLimit)` and `rate(pfCntSrcLimit)`: sustained values above zero mean your `max` and `max-src-*` caps are rejecting traffic.
3. `rate(pfStateInserts)`: new connections per second through pf, a churn indicator.
4. `pfSrcTrackCount` against `pfLimitSourceNodes`: source-tracking nodes against the cap.
5. `rate(pfCntMemory)`: pf failing to allocate memory.
6. `memIfLiveLocks`: per-interface memory pool live locks.

### 3.5 Prometheus job

The companion `prometheus.yml` has one `snmp` job with all four modules and the servers in a separate targets file, so adding a server is one entry in a file. [tested: promtool check passed, the job came up, and all four modules returned data]

```
- job_name: snmp
  metrics_path: /snmp
  params:
    module: [if_mib, hrStorage, hrDevice, openbsd_pf]
    auth: [site_v2]
  file_sd_configs:
    - files: ["/etc/prometheus/targets/snmp.yml"]
  relabel_configs:
    - source_labels: [__address__]
      target_label: __param_target
    - source_labels: [__param_target]
      target_label: instance
    - target_label: __address__
      replacement: 127.0.0.1:9116
```

The scrape interval is 60 s with a 50 s timeout in the file. Whether 4 modules per server finish inside that on real OpenBSD hardware is not measured. If scrapes time out, split `hrDevice` into its own job with a longer interval. [assumed]

## 4. Verifying the data

1. Exporter: `curl 'http://127.0.0.1:9116/snmp?module=openbsd_pf&target=192.0.2.10&auth=site_v2' | grep ^pf` returns the pf metrics. [tested form]
2. Prometheus: Status, Targets shows the `snmp` job up for every server.
3. Queries that should return one row per server:
   * `ifHCInOctets` for interface counters.
   * `hrProcessorLoad` for CPU.
   * `pfStateCount` for pf.
   [tested against the stand-in agent]
4. Swap: on a host with no swap, `used / size` is a division by zero and the panel shows no data. That is expected, not a fault. [tested]

## 5. Troubleshooting

| Symptom | Check |
|---|---|
| Exporter returns an error or empty page | Run the `snmpwalk` in 2.5 from the monitoring host. If that fails the problem is the agent or the network, not the exporter. |
| Timeout from the monitoring host | `listen on` address, protocol flag (a bare `listen on` is v3 only), pf rule on the server. [read for the first two] |
| Works with `snmpwalk`, no pf metrics | The `openbsd_pf` module is missing from the exporter config, or the MIB path was wrong when generating. The generator prints how many metrics it built. [tested] |
| v3 fails with an authentication error | Keys and algorithms must match between `snmpd.conf` and the v3 auth in the exporter. The exporter side with SHA-256 and AES worked against net-snmp. [tested] OpenBSD interop [not verified] |
| Memory always looks nearly full | Used is total minus free pages, so cache counts as used. [read] |
| CPU shows 0 or steady for a minute | It is the average over the last minute. [read] |
| No swap panel | The host has no swap. [tested] |
| One server missing from the dashboard | Is it in the targets file? Prometheus reloads file_sd files by itself. [assumed] |
| Scrapes time out for all servers | Too many modules per scrape for the timeout. Split `hrDevice` out, or raise `scrape_timeout`. [assumed] |
| Queries from unknown hosts | `listen on` was missing, so snmpd is on every address. Add it and the pf rule. [read] |

## 6. Version notes

Testing used snmp_exporter 0.26.0 and Prometheus 2.45.3. The OpenBSD ports are newer: snmp_exporter 0.30.1 and Prometheus 3.5.2. From the upstream changelogs: [read]

1. snmp_exporter 0.28.0 moved `sysUpTime` out of `if_mib` into a new `system` module. This setup does not use it.
2. snmp_exporter 0.27.0 moved the UCD OIDs into their own module. Not used here.
3. snmp_exporter 0.23.0 introduced the split auth and module format that these files use. 0.24.0 added several modules per scrape.
4. Prometheus 3.0 fails a scrape whose Content-Type is missing or invalid. All the endpoints used here sent `text/plain; version=0.0.4`. [tested the headers, not tested under Prometheus 3]

Re-run the checks in section 4 after upgrading.

## 7. Rollout checklist

1. Pick one server. Configure `snmpd.conf` (2.1), start it (2.2), run the config test (2.3).
2. Run the `snmpwalk` from the monitoring host (2.5) and look at the CPU, memory, interface and pf values.
3. Generate `snmp-openbsd.yml` (3.2) and check the pf metric count.
4. Add that one server to the targets file and confirm it in Prometheus.
5. Roll out `snmpd.conf` and the pf rule to the other servers with your configuration management, then add them to the targets file.
6. After a day, check scrape duration in Prometheus for the `snmp` job and adjust the interval or split modules if needed.
