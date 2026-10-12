# Conductor Client Playbook (`ansible/runonce/conductor-client.yml`)

Configures OpenBSD systems to be monitored by and send flow data to the **conductor** system. Run once per host during onboarding.

## What it does

For each host in the `conductor_clients` inventory group, the playbook:

1. Writes `/etc/snmpd.conf` so `snmpd` listens on the interconnect interface (`{{ interconnect_interface_ip }}`) with a read-only SNMPv2c community, then enables and (re)starts `snmpd`.
2. Writes `/etc/hostname.pflow0` so the `pflow0` interface exports IPFIX (pflowproto 10) to `10.7.0.123:9995`, then applies it via `netstart`.
3. Appends pf rules to `/etc/service.pf.conf` allowing SNMP in and pflow out, then reloads pf.

All tasks are idempotent: services/rules are only restarted or reloaded when their configuration changes.

## Usage

### Remote (from a control machine)

Connect as `root`; Ansible prompts for the SSH password (`-k`) and uses `-u root`.

```sh
ansible-playbook runonce/conductor-client.yml -i 192.168.1.12, -u root -k
```

- `-i 192.168.1.12,` ad-hoc inventory with a single host (note the trailing comma; without it, Ansible treats the value as a file path).
- For multiple hosts, use a proper inventory file and make sure each host is in the `conductor_clients` group.

### Local (on the OpenBSD target itself)

Run as `root` on the target host.

```sh
pkg_add -vvi ansible
ansible-playbook runonce/conductor-client.yml --connection=local -i 127.0.0.1,
```

- `--connection=local` no SSH, executes on the current host.
- `-i 127.0.0.1,` the local host must still be a member of `conductor_clients` for the play to match it.

## Variables

| Variable | Required | Description |
|---|---|---|
| `interconnect_interface_ip` | yes | IP address of the interconnect interface on the target. Used for the SNMP listener, the pflow source, and as the pf rule match address. |

## Files managed

| Path | Purpose |
|---|---|
| `/etc/snmpd.conf` | SNMP listener + read-only community. Validated with `snmpd -n -f %s` before being written. Mode `0600`, `root:wheel`. |
| `/etc/hostname.pflow0` | pflow0 interface config (flowsrc/flowsdst/pflowproto). Mode `0640`, `root:wheel`. |
| `/etc/service.pf.conf` | Appended pf rules for the conductor. Mode `0600`, `root:wheel`. |

## Verification

After a successful run, on the target:

```sh
# SNMP is listening and enabled
rcctl check snmpd

# Confirm snmpd is bound to the interconnect IP on UDP port 161
netstat -an -f inet | grep '\.161 '

# pflow0 interface is up and configured
ifconfig pflow0

# pf rules are loaded
pfctl -sr | grep -E 'port (161|9995)'
```

From the conductor host (`10.7.0.123`), confirm SNMP reachability:

```sh
snmpwalk -v2c -c R5CZsMUAThIxCXSzJOW5 <interconnect_interface_ip> system
```
