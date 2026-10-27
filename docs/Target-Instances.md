# Target instances

Players can spawn private instances of targets. A private instance is a copy of the target that only the player (and optionally their team) can reach.

A player can run more than one instance at the same time. Each one has to be of a different target, so you can't have two instances of the same target.

## How many instances

Every player has their own limit, stored in `player_spin.max_instances`.

New players start with the value of the `max_target_instances` sysconfig key. When it's not set, they get `1`. You can change it from the backend **Settings => Configure**, field **Max target instances** under _Profile properties_.

Changing the sysconfig key only affects players created after the change.

To change the limit of a single player, go to **Player Spins**, edit the player and set **Max instances**. You can also see it on the player profile, next to the spin counters.

## Subscriptions

A product can give extra instances with the `max_target_instances` metadata key.

```json
{"max_target_instances": "3"}
```

When the subscription starts, the product value is added to the player limit. When it ends, the same number is taken back.

So a player with a limit of `2` gets `5` while subscribed, and goes back to `2` when the subscription ends. Custom values set by an admin are kept.

The limit never goes below `1`.

## Spawning

When a player clicks **Spawn a private instance**:

* If they already have an instance of this target, it gets rebooted
* If the limit is `1` and they have an instance of another target, the old one is scheduled to shut down. Once it's gone they can spawn the new one
* If the limit is more than `1` and they are full, the spawn is refused. They have to shut one down first. The menu shows **Instance limit reached** instead of the spawn option

Instances that are shutting down still count until `cron/instances` removes them.

The `spins_per_day` limit still applies to spawns and reboots.

## When the limit goes down

This happens when a subscription ends, a player moves to a smaller plan, or you lower a player's **Max instances**.

On every run, `cron/instances` checks for players with more instances than they are allowed. It keeps their oldest ones and destroys the newest extra ones. You'll see a line like this in the output:

```
2026-10-27 10:15:02 Scheduling destroy webapp for alice, over the allowed instances limit
```

## Where players see them

* The dashboard shows one card for each instance
* The target cards and target pages show the instance IP in red, for the targets the player has an instance of
* `api/target/instances` lists all of them

## Backend

Instances are listed under **Infrastructure => Target Instances**.

Each instance is picked by player and target, so the links look like this:

```
/infrastructure/target-instance/view?player_id=5&target_id=12
```
