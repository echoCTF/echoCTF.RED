Lists players that **scored on a target they barely talked to**. Finding a treasure or a finding means interacting with the target over the VPN, so a claim with no traffic behind it usually means the flag came from someone else (flag sharing, writeup relays, a second account doing the work).

Traffic comes from `player_target_traffic`, collected by `traffic/collect` from the VPN gateway. Traffic to target instances and Team Network targets is counted against the target, so players working on those are covered.

One row per player and target.

### Signals
* `no_traffic`: no traffic at all from the player to the target.
* `claimed_before_traffic`: the first claim is earlier than the first recorded packet.
* `under_threshold`: some traffic, but less than *Too little below* bytes in total.

### Row colours
* **Red**: `no_traffic` or `claimed_before_traffic`.
* **Yellow**: only `under_threshold`.

### Which claims are checked
Only claims made while traffic accounting was running: from the earliest `player_target_traffic` record, or from *Claims from* when that is later. Truncating the traffic table moves this start forward.

### False positives
* **Collector downtime**: if `traffic/collect` was not running for a while, claims in that window show as `no_traffic`. Many players lighting up on the same days usually means that.
* **The threshold**: real work on a target is normally far above 10 KB, raise or lower it as needed.
