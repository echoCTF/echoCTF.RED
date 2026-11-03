Lists **headshots solved much faster than usual**. Someone who already has the flags (flag sharing, writeup relays, a second account) finishes a target in seconds or minutes, while real solves usually take hours.

The headshot timer is the time between the player's first and last claim (findings and treasures) on the target.

### How a solve is judged
* **Faster**: how many times faster than the **median** timer of the target. The median is used instead of the average so that players who leave a target for weeks don't make everyone else look fast. Only targets with at least *Min target solves* headshots are judged, since a median of a few solves is noise.
* **Usual pace**: the median of the player's own ratios over all their judged solves (1 is a typical player, 0.5 is twice as fast). Players at or below *Skip if usual ratio at most* are fast everywhere and are not listed, except players with fewer judged solves than *Always list players with fewer solves than*, since a new account has no usual pace yet.

A solve is listed when its ratio is below *Ratio below* (0.1 means 10 times faster).

### Row colours
* **Red**: fast, less traffic than *Little traffic below* (or none), and no writeups activated for the target. The strongest case.
* **Yellow**: fast without writeups, but with traffic. Often a skilled player, check the player.
* **No colour**: the player activated writeups for the target, so being fast is expected.

### Zeroing a solve
The **Zero** button on each row zeroes the headshot the same way as the headshot zero action: the timer goes to 0, the player's finding, treasure and headshot points for the target are zeroed on the stream, the player and team scores are recalculated and the target writeups are activated for the player. It also adds an entry to the abuser table (reason `fast_solve_zeroed`, no points, marked resolved) recording who zeroed it, the old timer, the target median and the points removed. The solve then drops out of this report and out of the medians.

### Notes
* Headshots with a zero timer are skipped completely. A zeroed timer means the solve was already erased as cheated, so it is neither listed nor counted in the medians or in *Min target solves*.
* Medians use every other headshot of the target, *Solved from* only limits which solves are listed.
* Traffic comes from `player_target_traffic` and only exists for solves made while traffic accounting was running. Older solves show *none*, so judge them by the timer alone.
* Findings come from scanning, so a player who scans first starts the clock early. Pasting the treasures afterwards still makes the timer far shorter than a real exploitation.
* Requires MariaDB, which has the `MEDIAN()` window function.
