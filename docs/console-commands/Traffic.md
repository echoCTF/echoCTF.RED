# Traffic command
Collect and show the traffic between players and targets. See [Player Target Traffic](../Player-Target-Traffic.md).

## collect
Listen for `pflow` exports and save the packets and bytes for each player and target.

Usage: `./backend/yii traffic/collect [listen] [flush] [verbose]`

* `listen`: address and port to listen on (default: `127.0.0.1:9995`)
* `flush`: seconds between saves to the database (default: `30`)
* `verbose`: set to `1` to print counters on every save

The command runs until it is stopped. On `SIGTERM` or `SIGINT` it saves what it has and exits.

With `verbose` it prints a line like this:

```
2026-10-27 10:15:02 datagrams=120 invalid=0 records=2400 stored=35 unmatched=4
```

* `datagrams`: packets received from pflow
* `invalid`: packets that were not NetFlow v5
* `records`: flow records read
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
