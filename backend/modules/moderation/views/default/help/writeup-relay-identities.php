Finds **writeup relays without using IPs**. It pairs a zero point player (the *fake*) that activated writeups for a target with any player that scored on that target within *Claims within* days after the activation (treasures and findings). A pair is listed only when the two accounts have at least one **identity link**. Timing and behaviour signals only add to the score.

### Identity links
Names are compared with everything except letters removed, case and accent insensitive, and need at least 5 letters.

| Signal | Score | Meaning |
|---|---|---|
| `username` | 2 | Same name in the usernames, eg `johndoe2` and `john_doe` |
| `email` | 3 | Same email local part, `+tag` and dots ignored |
| `fullname` | 3 | Same full name |
| `social:X` | 3 | Same non-empty discord, twitter, github, htb, youtube or twitch |
| `referral` | 3 | One player referred the other (either direction) |

### Boosters
Score 1 each, never enough to list a pair on their own.

| Signal | Meaning |
|---|---|
| `fake_never_vpn` | The fake never connected to the VPN |
| `new_on_target` | The beneficiary's first ever claim on the target is after the activation |
| `fast_6h` | The first claim came within 6 hours of the activation |

### Row colours
* **Red**: score 6 or more, for example two identity links.
* **Yellow**: score 4 or 5, for example one identity link plus a booster.

Identity links are shown in bold in the *Signals* column.
