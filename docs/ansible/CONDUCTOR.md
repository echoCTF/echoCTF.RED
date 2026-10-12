# Conductor Playbook (`ansible/runonce/conductor.yml`)

Provisions a standalone OpenBSD conductor for central monitoring and graphing node for the echoCTF.RED platform. It installs and wires up Prometheus, Grafana, the SNMP/node/memcached/mysqld exporters, EaseProbe, NetFlow collection (`nfcapd`/`nfdump`), Unbound, OpenVPN, and `supervisord`, plus the PF anchors and interfaces those services rely on.

Runs once per conductor host, on a freshly installed OpenBSD system. Re-running is safe the tasks are idempotent.

Companion playbook: [`conductor-client.yml`](conductor-client.md) configures the *monitored* hosts that report into this conductor.

## Required variables

Set these in `host_vars/<host>.yml` (or the inventory). Values shown are defaults where one exists.

| Variable | Example | Notes |
| --- | --- | --- |
| `myname` | `conductor.mydomain` | Hostname. |
| `ansible_host` | `1.2.3.4` | Standard Ansible; not referenced by the playbook itself. |
| `targets_interface` | `vio1` | |
| `interconnect_interface` | `vio2` | |
| `targets_if_ipv4` | `10.0.0.123` | |
| `interconnect_interface_ip` | `10.7.0.123` | |
| `targets_netmask` | `255.255.0.0` | |
| `admins_domain` | `mydomain` | Feeds the `<administrators>` PF table via `nstables`. |
| `grafana_user` / `grafan_password` | `admin` / `grafana` | Consumed via the `grafana.config.ini` template. |
| `installurl` | unset | Optional; if set, written to `/etc/installurl`. |
| `GITHUB_REPO` / `GITHUB_REPO_BRANCH` | unset / `main` | Remote mode only. |
| `APP_DIR` | `/root/sources` | Remote mode only; overwritten in local mode. |

## Usage

**Remote** (from a control machine, as `root`):

```sh
ansible-playbook runonce/conductor.yml -i 192.168.1.12, -u root -k
```

The play targets `hosts: all`, so use a per-conductor inventory or `--limit` if your inventory has more than one host.

**Local** (on the OpenBSD target, as `root`):

```sh
pkg_add -vvi ansible
ansible-playbook runonce/conductor.yml --connection=local -i 127.0.0.1,
```

In local mode the playbook assumes the repo is already checked out (it skips the `git clone`) and sets `APP_DIR` relative to `playbook_dir`.

## Verifying it worked

After a reboot:

```sh
# Hostname, interfaces
hostname
ifconfig vio2
ifconfig vio1

# Services are enabled and running
rcctl check cron ntpd unbound prometheus node_exporter snmp_exporter grafana supervisord

# Prometheus config is valid and listening on loopback
promtool check config /etc/prometheus/prometheus.yml
netstat -an -f inet | grep -E '\.(9090|9100|9116|3000) '

# Grafana is up
curl -s http://127.0.0.1:3000/api/health

# NetFlow is arriving (a conductor-client must be exporting)
ls -l /var/nfcapd/exp | tail
cat /var/lib/node_exporter/textfile/nfdump.prom

# PF anchors loaded
pfctl -sr | grep -E '3000|9995'
```

Expected loopback listeners: `9090` prometheus, `9100` node_exporter, `9116` snmp_exporter, `3000` grafana. To confirm *which* process owns a socket, use `fstat | grep internet | grep <port>`
