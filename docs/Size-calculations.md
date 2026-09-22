# Size Calculations

Different formulas and rules of thumb to help you calculate sizing configurations for various daemons of the platform.

## Sizing for PHP-FPM
You need to ensure the following sizing rules apply or PHP-FPM will fail to start

1. `min_spare_servers ≤ max_spare_servers`
2. `max_spare_servers ≤ max_children`
3. `min_spare_servers ≤ start_servers ≤ max_spare_servers`

To get the real numbers:

* Check available RAM `vmstat -s | grep -E "pages managed|pages free"` Multiply "pages free" by 4096 for bytes available
* Checking actual per-worker PHP-FPM memory `ps aux | grep '[p]hp-fpm' | awk '{sum+=$6; n++} END {print sum/n/1024 " MB avg, " n " workers"}'`

```
peak_concurrent_players = expected_total_participants × concurrency_ratio

pm.max_children = (RAM available to PHP-FPM) / (average memory per worker process)
pm.min_spare_servers = peak_concurrent_players × active_request_fraction × safety_multiplier
pm.max_spare_servers = min_spare_servers + (max_children - min_spare_servers) × 0.3
pm.start_servers = (min_spare_servers + max_spare_servers) / 2
```

* `expected_total_participants` known ahead of time, your registration/signup numbers for the event
* `concurrency_ratio` fraction of registered participants typically online at once during peak hours
* `peak_concurrent_players` your peak concurrent number of players
* `active_request_fraction` at any single instant, roughly 10% of connected players are actually mid-request (page load, AJAX poll, flag submit), not all players
* `safety_multiplier` headroom for bursts (everyone refreshing at once, event-start login wave).
