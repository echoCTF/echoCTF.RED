Lists groups of headshots on the **same target** that happened close together in time. Clusters are a common side effect of flag sharing, writeup leaks and multi-accounting, but also of legit rushes (new target releases, events, streams), so use the extra columns to tell them apart.

### Cluster by
* **Day**: headshots on the same target on the same calendar day.
* **Week**: headshots on the same target in the same week (weeks start on Monday).
* **Burst**: consecutive headshots on the same target that are no more than *Burst gap* minutes apart. Unlike day and week, a burst is not cut at midnight, so it catches a group that solved between 23:50 and 00:20.

Only clusters with at least *Min headshots* headshots are listed.

### Columns
* **Span**: time between the first and last headshot of the cluster. Many headshots in a short span is more suspicious.
* **Timer avg / min**: the headshot timer (seconds between the first and last finding/treasure of the player). Shared flags tend to produce very low timers.
* **Fast**: headshots with a timer below the *Fast solve* threshold.
* **IP / VPN / Team overlap**: number of players beyond the first in the cluster that share the same last signin IP, the same last VPN remote IP, or the same team. These come from `player_last` and `team_player`, so they reflect the latest known values and not the values at the time of the headshot.
* **Share**: percentage of all the headshots of the target that fall in this cluster.
* **Target age**: days between the target release (`scheduled_at` or `created_at`) and the first headshot of the cluster. Clusters on old targets are more interesting than launch day rushes; use *Ignore first N days after release* to hide those.

### Row colours
* **Red**: at least one IP or VPN overlap, or at least half of the headshots are fast solves.
* **Yellow**: at least one fast solve or a team overlap.

### Notes
* Date filters apply before clustering, so a burst that starts before *From* is cut at that point.
* The headshots count links to the headshots activity list for the target (and day, in day mode).
