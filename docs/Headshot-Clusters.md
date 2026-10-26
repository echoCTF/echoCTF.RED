# Headshot Clusters

A backend moderation report that lists groups of headshots on the same target that happened close together in time. Useful for spotting flag sharing, leaked writeups and multi-accounting.

Find it under **Moderation / Headshot Clusters** (`/moderation/default/headshot-clusters`).

## Cluster modes

- `Day`: headshots on the same target on the same calendar day
- `Week`: headshots on the same target in the same week (Monday to Sunday)
- `Burst`: consecutive headshots on the same target no more than `Burst gap` minutes apart. Not cut at midnight.

## Filters

- `Min headshots`: smallest cluster size to list (default `3`)
- `Burst gap (minutes)`: max time between headshots of a burst (default `60`, burst mode only)
- `Fast solve below (seconds)`: headshot timer under which a solve counts as fast (default `300`)
- `From` / `To`: date range (defaults to the last 30 days). Applied before clustering.
- `Target`: target name
- `Includes player`: only clusters that include a matching username
- `Ignore first N days after release`: hide launch rushes (based on `scheduled_at` or `created_at` of the target)

## Columns

- `Span`: time between the first and last headshot of the cluster
- `Timer avg / min`: average and fastest headshot timer in the cluster
- `Fast`: headshots with a timer below the fast solve threshold
- `IP / VPN / Team overlap`: players beyond the first that share the same last signin IP, last VPN remote IP or approved team
- `Share`: cluster headshots as a percentage of all the target headshots (`target_state.total_headshots`)
- `Target age`: days between the target release and the first headshot of the cluster
- `Players`: in solve order, linked to their player page

## Row colours

- **Red**: any IP or VPN overlap, or at least half of the headshots (and at least 2) are fast solves
- **Yellow**: any fast solve or team overlap

Colours are a triage hint, not proof.

## Notes

- IP and VPN overlap use the latest values from `player_last`, not the values at the time of the headshot. Shared IPs can also be legit (NAT, campus networks).
- `Share` is based on the all-time headshot count of the target, not the selected date range.
- Requires MariaDB 10.2+ / MySQL 8+ for burst mode (window functions).
