# Player Target Traffic

Shows how much traffic each player sent to each target, and how much the target sent back. It counts packets and bytes.

* **Sent**: from the player to the target
* **Received**: from the target to the player

## Where to see it

* Backend **Activity => Player Target Traffic**. You can filter by player and target, delete the filtered records, or truncate the table.
* The **Target Traffic** tab on the player profile full view. Only admins see it, and only when the player has records.
* From the console with `traffic/show` (see [Traffic command](console-commands/Traffic.md))

## How it works

1. PF exports the connections between players and targets with `pflow`, to `127.0.0.1:9995`
2. `traffic/collect` listens on that address and adds up the packets and bytes. It only counts the connections as seen on the interfaces players connect through (the `offense` group, see `--iface` in the [Traffic command](console-commands/Traffic.md)), so each connection is counted once
3. Every 30 seconds it finds the player and the target of each connection and saves the totals in the `player_target_traffic` table

The player is found from their VPN IP (`player_last`). If the player has already disconnected, their last VPN session with that IP is used (`player_vpn_history`).

The target is found from the IPs of targets, target instances and private network targets.

The totals are for all time. They keep growing until you delete them.

## Setup

Run the migrations. They create the `player_target_traffic` table and the menu entry.

The VPN playbooks (`vpngw.yml`, `vpn.yml`, `vpnslaves.yml`) do the rest:

* Create `/etc/hostname.pflow1`
  ```
  flowsrc 127.0.0.1 flowdst 127.0.0.1:9995
  pflowproto 5
  up
  ```
* Add the `backend-traffic` program to supervisord, which runs `traffic/collect`

To do it by hand on a VPN server:

```sh
sh /etc/netstart pflow1
pfctl -f /etc/pf.conf
supervisorctl reread && supervisorctl update
```

`pflow0` is not touched, it is used by the conductor client. Every connection that has `pflow` is sent to all pflow interfaces, so both get the same data.

## Which connections are counted

Only connections from PF rules that have `pflow` in their `keep state` options.

* The rules in `vpn.service.conf` for `<targets>`, and for targets connecting back to players
* The rules that `cron/pf` creates for targets connecting back to players, on networks, instances and private networks
* The rules that `cron/pf` creates for players reaching networks, instances and private networks use the `pf_state_limits` sysconfig key. Add `pflow` to it to count them, for example:
  ```
  (pflow, max 10000, source-track rule, max-src-nodes 5, max-src-states 2000, max-src-conn 50)
  ```

## Notes

* A connection is counted when it ends. A long connection (for example a reverse shell) shows up only after it closes.
* If the database is down, `traffic/collect` keeps the totals in memory and tries again on the next save.
* Check that flows arrive with `tcpdump -n -i lo0 udp port 9995`
* The collector log is `/var/log/echoctf-traffic.log`
