# Traffic command
Collect and show the traffic between players and targets. See [Player Target Traffic](../Player-Target-Traffic.md).

## collect
Listen for `pflow` exports and save the packets and bytes for each player and target.

Usage: `./backend/yii traffic/collect [listen] [flush] [verbose] [--iface=offense]`

* `listen`: address and port to listen on (default: `127.0.0.1:9995`)
* `flush`: seconds between saves to the database (default: `30`)
* `verbose`: set to `1` to print counters on every save
* `--iface`: the interfaces players connect through. Accepts interface names and interface groups, comma separated, for example `--iface=offense` or `--iface=tun0,tun1` (default: `offense`)

A connection between a player and a target goes through two interfaces of the VPN server, and pf exports it once for each. The collector only counts the copy from the `--iface` interfaces, so every connection is counted once.

It finds the interfaces with `ifconfig` when it starts and every 5 minutes, so new interfaces are picked up. If none of the names exist, it stops with `No interface found for --iface=...`. Use `--iface=` (empty) to count everything, without this check.

The command runs until it is stopped. On `SIGTERM` or `SIGINT` it saves what it has and exits.

When it starts it prints the interface indexes it counts:

```
2026-10-27 10:15:00 counting flows on interface indexes: 6,9
```

With `verbose` it prints a line like this:

```
2026-10-27 10:15:02 datagrams=120 invalid=0 records=2400 skipped=1200 stored=35 unmatched=4
```

* `datagrams`: packets received from pflow
* `invalid`: packets that were not NetFlow v5
* `records`: flow records read
* `skipped`: records from other interfaces than `--iface`, not counted
* `stored`: player and target pairs saved
* `unmatched`: flows that did not match a player and a target

## show
Show the traffic of a player, for all targets or for one target.

Usage: `./backend/yii traffic/show <player> [target]`

* `player`: player `username` or `id`
* `target`: target `name` or `id` (optional)

```
target                      pkts sent    pkts recv     bytes sent     bytes recv  last seen
webapp                          20500        18200        2105344       10485760  2026-10-27 11:00:00
```
