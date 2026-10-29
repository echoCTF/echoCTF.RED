# Grafana: one place to see the whole platform

Graphs availability (EaseProbe), servers, interfaces and pf (SNMP), flows (nfdump), memcached, and an application table in MariaDB.

## 0. How to read this document

Every load-bearing claim carries one of these marks:

* **[tested]** Verified in a lab setup with real software: Grafana 11.2.0, Prometheus 2.45.3, snmp_exporter 0.26.0, node_exporter, memcached_exporter, EaseProbe, nfdump 1.7.10 with synthetic flows, and a stand-in SNMP agent.
* **[read]** From documentation or source, not run.
* **[assumed]** Standard practice or reasoning.

Not tested:

1. Anything against real OpenBSD servers. The SNMP data came from a Linux stand-in agent and a fake agent for the pf values.
2. The MariaDB data source and any panel built on it.
3. mysqld_exporter.
4. The OpenBSD port versions (Grafana 13.1.5, Prometheus 3.5.2, snmp_exporter 0.30.1). Older versions were tested, see section 7.
5. Prometheus and Grafana load with a large number of servers.

Placeholders used in examples:

| Placeholder | Meaning |
|---|---|
| `192.0.2.30` | Database and memcached host |
| `<prefix>` | EaseProbe metric prefix (see section 5) |
| `<database>` | Application database name |
| `grafana_ro` | Read-only database user for Grafana |

## 1. What feeds what

```
EaseProbe :8181 -------------------------------------+
snmp_exporter :9116 --polls--> servers ---------------+
node_exporter :9100 (textfile) <-- nf-prom.sh <-- nfcapd files
memcached_exporter :9150 --> memcached                +--> Prometheus :9090 --> Grafana :3000
                                                      |
MariaDB (application tables) -------------------------+--> Grafana directly (SQL data source)
```

| Data | Source | How it reaches Grafana | Tested |
|---|---|---|---|
| Probe up/down, SLA | EaseProbe | Prometheus scrapes `127.0.0.1:8181/metrics` | yes |
| CPU, memory, swap, disks, interface traffic, pf | snmpd on each server | snmp_exporter, then Prometheus | yes, stand-in agent |
| Per-exporter bytes, top source and destination IPs | nfcapd files | `nf-prom.sh` writes a metrics file, node_exporter serves it, Prometheus scrapes | yes |
| memcached connections and rate | memcached | memcached_exporter, then Prometheus | yes |
| Application data (for example per-user bandwidth) | MariaDB table | Grafana MySQL data source, SQL | no |
| MariaDB server metrics | MariaDB | mysqld_exporter, then Prometheus | no |

nfdump has no Grafana data source of its own, which is why the flow numbers go through a small script. [tested]

## 2. Where to run it

One monitoring host runs Prometheus, Grafana, snmp_exporter, node_exporter and memcached_exporter. The OpenBSD servers only need `snmpd` (base system) and `pflow` (base system), no extra packages. [assumed design, each piece tested separately]

* If EaseProbe's metrics port is bound to `127.0.0.1`, Prometheus must run on the same host as EaseProbe, or change `settings.http.ip` in the EaseProbe config. [read]
* The tested platform was Linux. OpenBSD has ports for grafana (13.1.5), prometheus (3.5.2) and snmp_exporter (0.30.1). Ports for node_exporter, memcached_exporter and mysqld_exporter were not checked. [read, openports.pl]

## 3. Companion files

1. `prometheus.yml`: scrape config for all jobs. [tested: promtool check, all jobs came up]
2. `snmp-targets.yml`: the server list for the SNMP job. [tested]
3. `generator.yml`: builds the OpenBSD pf module for snmp_exporter. [tested, see the SNMP document]
4. `nf-prom.sh`: turns nfcapd files into metrics. [tested]
5. `platform-overview.dashboard.json`: the dashboard, 28 panels. [tested]

## 4. Install

### 4.1 Prometheus

Use the companion `prometheus.yml`. Defaults worth knowing:

1. Scrape interval is 60 s, timeout 50 s.
2. Retention defaults to 15 days. Raise it with `--storage.tsdb.retention.time` if you need longer. [read, Prometheus default]
3. Reload after editing: send SIGHUP. The lifecycle HTTP reload is off unless enabled. [tested: the HTTP reload answered "Lifecycle API is not enabled", SIGHUP worked]

### 4.2 Grafana provisioning

Two small files make Grafana come up already configured. [tested]

`/etc/grafana/provisioning/datasources/datasources.yaml`:

```
apiVersion: 1
datasources:
  - name: Prometheus
    uid: prom
    type: prometheus
    access: proxy
    url: http://127.0.0.1:9090
    isDefault: true
    jsonData:
      timeInterval: 60s
```

`/etc/grafana/provisioning/dashboards/dashboards.yaml`:

```
apiVersion: 1
providers:
  - name: platform
    folder: Platform
    type: file
    options:
      path: /var/lib/grafana/dashboards
```

Then copy `platform-overview.dashboard.json` into `/var/lib/grafana/dashboards/`. In testing Grafana picked it up within about 10 seconds and listed it in the Platform folder. [tested]

Two details that bite:

1. `uid: prom` must match: the dashboard refers to the data source by that uid.
2. `timeInterval` must equal the real scrape interval (60 s here). The dashboard uses `$__rate_interval`, which Grafana derives from this value. If it is much smaller than the scrape interval, rate panels show gaps or nothing. [tested at 30 s on both sides, 60 s assumed by the same reasoning]

Grafana logs errors about missing `plugins` and `alerting` provisioning folders. They are harmless. [tested]

### 4.3 Flow metrics

Cron every 5 minutes, a few seconds after nfcapd rotates:

```
2-59/5 * * * * NFDUMP=/usr/local/bin/nfdump /usr/local/sbin/nf-prom.sh /var/nfcapd/exp /var/lib/node_exporter/textfile/nfdump.prom 10
```

node_exporter must read that folder. The flags tested:

```
node_exporter --web.listen-address=127.0.0.1:9100 --collector.disable-defaults --collector.textfile --collector.textfile.directory=/var/lib/node_exporter/textfile
```

[tested] The script writes the file through a temporary name and a rename, so node_exporter never reads a half-written file. [tested]

What the numbers mean, and limits: [tested]

1. The values are gauges for the newest completed nfcapd file, one rotation window (5 minutes by default), not running totals. Graph them as bars or lines per window, not as rates.
2. `nfdump_top_src_bytes` and `nfdump_top_dst_bytes` carry the IP as a label. Series count is exporters times top N, times two, and the set changes every window. Keep the top N small (10 is fine); do not raise it into the hundreds.
3. `nfdump_newest_file_age_seconds` is measured from the file name, which is the start of the window. A healthy exporter sits near one window length. The dashboard line turns red at 900 s.
4. The file age conversion uses `date -j -f` first and `date -d` as a fallback. The fallback was tested on Linux, the OpenBSD form was not. [assumed]
5. Per-client numbers from flows depend on what pflow records on the exporters (original or translated addresses). See the pflow and nfdump document.

### 4.4 memcached

```
memcached_exporter --memcached.address=192.0.2.30:11211 --web.listen-address=127.0.0.1:9150
```

[tested against a local memcached, 1.6.24] Metrics used: `memcached_current_connections`, `memcached_connections_total`, `memcached_current_bytes`, `memcached_items_evicted_total`, `memcached_up`. [tested]

The dashboard draws two threshold lines: 1000 open connections and 20 new connections per second. Adjust them to your normal load. A high new-connection rate with few open connections indicates clients that connect and disconnect instead of reusing connections. [assumed]

### 4.5 MariaDB

Grafana has a MySQL data source and its documentation states MariaDB 10.5 or newer is supported. [read, Grafana docs] Provisioning example from the documentation: [read]

```
apiVersion: 1
datasources:
  - name: MariaDB
    uid: mariadb
    type: mysql
    url: 192.0.2.30:3306
    user: grafana_ro
    jsonData:
      database: <database>
      maxOpenConns: 5
      maxIdleConns: 5
      connMaxLifetime: 14400
    secureJsonData:
      password: ${GRAFANA_MYSQL_PASSWORD}
```

Use a dedicated user with SELECT on only the tables Grafana needs. [assumed]

No SQL panel ships with the dashboard, since it depends on your schema. A time series panel needs a `time` column and a numeric column, with Grafana's time macros in the WHERE clause. [assumed] Check whether the table is written continuously or only at the end of a session before choosing a panel type.

For MariaDB server health (connections, threads, queries per second) the usual route is mysqld_exporter. The Ubuntu package is 0.15.0. [read] It was not run. A simple availability probe only checks that connections are accepted.

## 5. The dashboard

`platform-overview` has six rows and 28 panels. Two variables at the top, Server and Interface, both default to All. [tested]

1. **Availability:** probes up, probes down, a table of failing probes, SLA per probe. Built from EaseProbe's `<prefix>_*` metrics. EaseProbe builds the metric prefix from `settings.name` with the spaces removed, so `my probes` becomes `myprobes_`. Set the prefix in the panel queries to match your EaseProbe config, and change it again if you rename it there. [tested]
2. **Network:** inbound and outbound bits per second per server and interface, and errors and discards. From `ifHCInOctets` and `ifHCOutOctets`.
3. **Hosts:** CPU, memory, swap, filesystems.
4. **pf:** states against the limit, state inserts per second, source nodes against the limit, and a panel of limit hits (`pfCntStateLimit`, `pfCntSrcLimit`, `pfCntMemory`, `pfCntCongestion`).
5. **Flows:** bytes per window by exporter, top source IPs, top destination IPs, exporter silence.
6. **memcached:** open connections with a 1000 line, new connections per second with a 20 line, memory and evictions.

How it was checked: all 29 panel queries were run through Grafana's query API against a live Prometheus. Every one returned data and none errored. [tested] Key values were also checked in Prometheus: the failing-probe count matched one deliberately dead probe, and the flow panels matched the bytes sent through nfcapd.

Known gaps in what that proves:

1. The values came from a Linux stand-in agent and a fake pf agent, so real OpenBSD data may expose label differences. The first thing to check on a real server is that `hrStorageDescr` shows "Physical memory", "Swap space" and mount points, which is what the OpenBSD source produces. [read]
2. The swap panel returns nothing on a host without swap, because the ratio divides by zero. [tested]
3. The memory panel looks high on OpenBSD since cache counts as used. [read]
4. Rendered in headless Chromium at 1500 px width: all six rows and 28 panels drew with data. Narrower screens were not checked. [tested]

## 6. Troubleshooting

| Symptom | Check |
|---|---|
| Panel says "No data" | Prometheus, Status, Targets: is the job up? Then run the panel's query in Explore. |
| Server variable is empty | No `ifHCInOctets` yet. Check the `snmp` job and the targets file. |
| Rate panels empty or jagged | Data source `timeInterval` does not match the scrape interval (4.2). |
| Availability panels empty | The EaseProbe prefix does not match the queries, or Prometheus cannot reach EaseProbe's metrics port because it is bound to loopback on another host. [tested prefix behaviour] |
| Flow panels empty or stale | Is cron running `nf-prom.sh`? Check `nfdump_script_last_run_timestamp_seconds`. Then check the exporter silence panel. |
| Flow numbers always zero | The newest completed window had no traffic, or nfcapd is not receiving. See the pflow and nfdump document. |
| Legends show duplicate entries per server | Old and new series overlap after changing job names or labels. They age out of the time range. [tested] |
| Scrape fails after a Prometheus upgrade | Prometheus 3 fails scrapes with a missing or invalid Content-Type. All endpoints here sent a valid one. [tested headers] |
| Dashboard changes vanish | Provisioned dashboards are re-read from the file. Edit the JSON file, or export from the UI back to it. [assumed] |
| Login problems | Change the default `admin` password on first start. [assumed] |

## 7. Version notes

Tested: Grafana 11.2.0, Prometheus 2.45.3, snmp_exporter 0.26.0. OpenBSD ports: Grafana 13.1.5, Prometheus 3.5.2, snmp_exporter 0.30.1. [read]

1. Prometheus 3.0 changed scrape and query behaviour: range selectors are now left-open, and missing Content-Type fails a scrape. [read, changelog] Re-run the section 6 checks after upgrading.
2. snmp_exporter 0.28.0 moved `sysUpTime` out of `if_mib`. The dashboard does not use it. [read, changelog]
3. The dashboard is written as Grafana schema version 39. Newer Grafana versions migrate older schemas on load. [assumed, not tested on 13.x]

## 8. Security

1. Bind Grafana to an internal address or put it behind a trusted reverse proxy, and change the default admin password. [assumed]
2. Prometheus and the exporters have no authentication by default. Keep them on loopback or an internal network, as in the companion files, where everything binds to `127.0.0.1`. [tested configuration]
3. The monitoring host holds the SNMP community or v3 keys and the database read-only password. Protect it like any other infrastructure host.

## 9. Rollout checklist

1. Install Prometheus, Grafana, snmp_exporter, node_exporter and memcached_exporter on the monitoring host.
2. Copy in `prometheus.yml` and `snmp-targets.yml`. Put one server in the targets file first.
3. Build `snmp-openbsd.yml` with `generator.yml` (SNMP document, section 3.2) and start snmp_exporter with both files.
4. Provision the data source and dashboard (4.2), and set the EaseProbe prefix in the availability panels (section 5).
5. Install `nf-prom.sh` and its cron line (4.3) once nfcapd is receiving flows.
6. Open the dashboard and compare against what you know about that server: CPU against `top`, interface traffic against `systat ifstat` or `netstat -ib`, pf states against `pfctl -si`.
7. Add the remaining servers to `snmp-targets.yml`.
8. Add SQL panels for your application tables if needed (4.5).
