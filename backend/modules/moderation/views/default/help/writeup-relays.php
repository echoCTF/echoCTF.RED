Finds **writeup relays by shared IP**. A zero point player (the *fake*) activates the writeups for a target, and another player (the *beneficiary*) who shares an IP with the fake scores on the same target **after** the activation, without activating the writeups themselves. Treasures and findings both count.

One row per fake, beneficiary, target, activation and set of matched IPs.

### Matched IPs
* **signup:IP**: both players signed up from this IP.
* **signin:IP**: both players last signed in from this IP.
* **vpn:IP**: the beneficiary last connected to the VPN from the IP the fake last signed in from.

### Columns
* **Claims**: treasures and findings the beneficiary claimed on the target after the activation.
* **Claim points**: the points those claims gave.
* **First claim**: when the beneficiary first scored on the target after the activation.

### Row colours
* **Red**: the players share a signup IP, the strongest of the three matches.

### Notes
* IPs come from `player_last`, so they are the latest known values and not the values at the time of the activation. Shared networks (universities, offices, carrier NAT) produce false positives, check the players before acting.
* *Activated from* limits the writeup activations, which also makes the report faster on large databases.
