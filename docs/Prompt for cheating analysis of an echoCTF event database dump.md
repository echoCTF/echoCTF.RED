# Prompt: cheating analysis of an echoCTF event database dump

How to use: attach the database dump (`.sql.gz`) and paste or attach this file as the first message. Fill in the inputs in section 1 that you know and delete the ones you do not. Claude will infer the rest and state what it assumed.

---

## 1. Inputs (edit before sending)

1. Dump file: `<name>.sql.gz` (mysqldump of the `echoCTF` database).
2. Connection rule of this event: `<for example "only venue IPs are allowed", or "remote play is allowed", or "none given">`.
3. Venue IPs: `<list of venue public IPs, "unknown, infer them", or "no venue, online event">`.
4. Staff and health-check account ids to exclude: `<ids, or "unknown, infer them">`.
5. VPN layout of this event: `<how teams map to VPN servers and which address range each server owns, or "unknown, work it out from the data">`.
6. Anything else the organizer wants checked: `<free text, or "none">`.

If an input is missing, do not ask. Infer it, say what you assumed in the report, and continue.

## 2. Task

Analyze the dump for cheating behavior other than what the `abuser` table already records. Also read the existing `abuser` rows and say how far they can be trusted. Put every finding into one markdown file and deliver the file to the user. The chat reply is two or three plain sentences.

## 3. How to answer (standing rules)

1. Never use em dashes or en dashes. Use "to" for ranges.
2. Lists: numbered lists for items, and sub-lists start with `*`, never `-`.
3. Tag every load-bearing claim with one of `[tested]` (I ran it and saw the result), `[read in source]` (read in a config, code or project doc), `[assumed]` (not verified).
4. Before any yes/no verdict, state the weakest unverified link in the chain.
5. A yes/no question gets a yes or no first.
6. Answer only what was asked. No unsolicited alternatives or suggestions.
7. Use plain, normal language. Say "connection" or "internet address", not "endpoint". Explain any technical term in one short line or avoid it.
8. Commands go in their own code block at column zero, never indented under list items. Prefer short flags.
9. Before building on a mechanism described by a secondary source, read the primary source (project docs or code, for example `modules/target/models/FailedClaimHandler.php`, `schemas/echoCTF-triggers.sql`) and confirm it exists as described.
10. If the user disputes a finding, re-check the data before replying. If you find a mistake of your own (a wrong query, a wrong number), say so first and plainly, then give the corrected numbers.
11. Re-run or re-read every number before it goes into the report.

## 4. Event rules and platform facts (verify against the data where a rule says so)

1. **Connection rule:** event specific, use the rule from section 1. If it says only venue IPs are allowed, any connection from an IP that is not a venue IP is a violation, whatever the reason. Do not argue about the reason (second person, second device, hotspot) when deciding that, and put the possible reasons only in the weakest-link line. If it says remote play is allowed, or gives no rule, report non-venue connections as information and do not call them violations. If no rule was given, say that you applied none.
2. **Venue IPs:** use the list from section 1. If the event is online only and has no venue, skip the venue-based checks and say so. If no list was given, infer the venue IPs as remote addresses seen from many distinct players and say they are inferred. Start with 20 or more distinct players and scale the threshold to the event size. The queries below use 20, replace it with your threshold. Report non-venue IPs that are shared by more than one player but fewer than the threshold separately, since they may be a second venue network.
3. **VPN layout:** event specific, never assume it. Use the layout from section 1 if given, and in every case work it out from the `openvpn` table and the sessions: which servers exist, which address range each one owns, and how teams map to servers (one per team, shared, or something else). State the layout you found in the report and only describe server sharing in terms of what the data shows.
4. **Certificates:** verify how certificates map to players (one per player, shared, or something else) and what identifies them (for example the CN). OpenVPN without `duplicate-cn` drops the old session when a new connection with the same CN arrives. Read the config of every row in the `openvpn` table (not only the first) for `duplicate-cn`, `keepalive`, `ping-exit`, `verb` and the log path.
5. **Wording for certificate contention:** describe it as short sessions on different public IPs replacing each other on one certificate. Never write "two live clients", because only one is connected at any instant.
6. **Instances:** targets are not always up. Read the per-player and per-team instance limits from `sysconfig` and the target settings (for example `members_per_team` and `instance_allowed`), because they differ per event. Check any speed or burst claim against those limits and against the findings per target. `target_instance_audit` may cover only part of the event, so state its date range.
7. **Findings vs claims:** findings are generated by tunnel traffic, per player, and the finding time is when it was registered in the database. Claims are web submissions and need no VPN session.
8. **"Claim flag before finding" abuser rows** (reason `tai_player_treasure`) are not necessarily player misconduct. The trigger only checks that the target has findings and the claimant has none in the database at that moment. Two explanations fit and do not exclude each other: a teammate claim (when `team_encrypted_claims_allowed` is on), and a finding that registered late (for example a network error, so it only registered after the local cache entry expired). Report both. Test the second one by checking the claimant's own findings on that target before and after the claim.
9. **Timezone:** event specific, never assume it. Events run in different parts of the world. `abuser.created_at` is a DATETIME, and most other tables are TIMESTAMP columns that the dump shows as UTC, so the two can be hours apart. Find the offset from the data: read the time zone setting from `sysconfig` and the dump header, then confirm it by matching failed-claim rows to the same players' real claims. The correct offset is the one that lines them up within seconds to minutes, and no other offset should. Report the zone and offset you found, and use it for every comparison with `abuser` or any other DATETIME column.
10. **Per-player flags:** read the formula, the key location and how static and dynamic treasures are handled from `FailedClaimHandler.php`, the treasure and target code, and `sysconfig`. Read the flag prefix and hash length from the data. They can differ per event, so check them against the current code. Validate the rebuilt flags by checking that failed-claim strings match them. If almost none match, the formula or key handling is wrong.

## 5. Setup

1. Unpack the dump with `gunzip -c <dump>.sql.gz > dump.sql`.
2. Strip trigger, routine and event blocks (everything between `DELIMITER ;;` and `DELIMITER ;`) so loading does not fire side effects. The final `DROP EVENT` statements may fail when the event scheduler is off, which is harmless.
3. Load the dump into a local MariaDB or MySQL instance. The local server can stop between commands, so check it before each query batch and restart it if the socket refuses connections.
4. Use the session scratchpad for scripts and intermediate files. Run any Python that reads the dump with `python3 -I`. Keep scripts outside the folder holding the dump.
5. Build helper tables: a per-player flag table (treasure, target, player, flag hash), and a view `pteam` with columns `player_id`, `team_id`, `tname` and the category value, mapping each player to their team.
6. Exclude staff and health-check accounts: use the ids from section 1, otherwise infer them from the data (admin accounts, accounts with no team and no claims, accounts that only run health checks).
7. The event window comes from `sysconfig` `event_start` and `event_end`. Check the unit (epoch seconds) and convert them to UTC before using them in queries.
8. Set the local server time zone to UTC (`SET GLOBAL time_zone='+00:00'`) so TIMESTAMP columns and `FROM_UNIXTIME` show UTC.

## 6. Checks to run

Report each as found, or as "checked, nothing found" with the numbers.

### 6.1 Reading the existing `abuser` data

1. Timezone offset (section 4, rule 9).
2. Failed claims: match every submitted string against every player's real flags, exact or within a few character edits (4 is a starting value). Count how many match the submitter's own flag, a teammate's, another team's, or nothing. Another team's flag would be cross-team sharing. Check whether any penalty hit a player's own valid flag that only had extra characters.
3. "Claim flag before finding" rows: per row list teammates with findings before the claim, the claimant's own findings before and after the claim, and the lag to the first later own finding. Present teammate claim and late registration as non-exclusive explanations.

### 6.2 VPN and connection checks

1. **Session reconstruction.** Sessions come from `player_bandwidth` (`ts` is the end, `duration` is seconds, so start is `ts - duration`) joined with `player_vpn_history` (connect events with remote and local address). Drop sessions with no remote IP before computing transitions, because missing values inflate the counts.
2. **Non-venue use.** Apply the connection rule from section 4, rule 1. Per player, count connects from non-venue IPs inside the event window, distinct IPs, first and last time. Report the share of players, teams and connects, the split by day, and a full table. Query:

```sql
CREATE TEMPORARY TABLE venue AS
  SELECT vpn_remote_address ra FROM player_vpn_history
  GROUP BY vpn_remote_address HAVING COUNT(DISTINCT player_id) >= 20;
CREATE TEMPORARY TABLE ev AS
  SELECT * FROM player_vpn_history
  WHERE ts BETWEEN '<event_start>' AND '<event_end>' AND player_id NOT IN (<staff ids>);
SELECT e.player_id, pt.tname, COUNT(*) nonvenue_connects,
       COUNT(DISTINCT e.vpn_remote_address) ips, MIN(e.ts) first_utc, MAX(e.ts) last_utc
FROM ev e LEFT JOIN pteam pt ON pt.player_id = e.player_id
WHERE e.vpn_remote_address NOT IN (SELECT ra FROM venue)
GROUP BY e.player_id, pt.tname ORDER BY nonvenue_connects DESC;
```

3. **Certificate contention.** Find certificates whose consecutive sessions alternate between different public IPs within seconds (gap of 60 s or less in the detector, 15 s or less when counting transitions, "reversal" means A to B to A within 10 minutes). The thresholds are starting values, adjust them to the data. Switches between two venue IPs are excluded because shared venue networks can rotate their outgoing address `[assumed]`. Detector:

```sql
WITH venue AS (
  SELECT vpn_remote_address ra FROM player_vpn_history
  GROUP BY vpn_remote_address HAVING COUNT(DISTINCT player_id) >= 20
), c AS (
  SELECT player_id, id, ts, vpn_remote_address ra,
         LAG(vpn_remote_address) OVER w pra, LAG(ts) OVER w pts
  FROM player_vpn_history WHERE player_id NOT IN (<staff ids>)
  WINDOW w AS (PARTITION BY player_id ORDER BY ts, id)
)
SELECT player_id, COUNT(*) flips, COUNT(DISTINCT ra) distinct_ips,
       MIN(ts) first_ts, MAX(ts) last_ts
FROM c
WHERE pra IS NOT NULL AND ra <> pra
  AND TIMESTAMPDIFF(SECOND, pts, ts) <= 60
  AND NOT (ra IN (SELECT ra FROM venue) AND pra IN (SELECT ra FROM venue))
GROUP BY player_id HAVING flips >= 3
ORDER BY flips DESC;
```

- For each hit give team, server, time window, transitions and reversals, the IP pair, and the short session lengths.
- Examine borderline accounts and say why they were or were not called (a one-way move or long stays between switches looks like one person changing network).
- Do not call it two people. The data cannot separate two people, one person with two devices, and one device flapping between two connections. Under a rule that only allows venue IPs it does not need to.

4. **Impact of a contention account.** Share of its findings and claims (points and counts) made while connected through the non-venue IP, and the account's share of its team's claim points. Claims are web submissions, so "connected through that IP at that moment" is all that can be said.
5. **Server mapping.** Match each session's `vpn_local_address` to the address ranges in the `openvpn` table to find its server, and check every table row. Report how teams map to servers, whether any player used more than one server, and whether any consecutive session pair crossed servers. Contention can only happen inside one server process, so say which server each contention account sits on and who else uses it.
6. **Other checks.** Non-venue IPs shared by accounts of different teams. Overlapping sessions per account longer than 120 s. Findings outside the account's own session windows. Treasure claims outside the claimant's session, and whether a teammate was online. IP coincidences between web sign-in addresses (`player_last` or similar) and VPN remote addresses of other players.

### 6.3 Flag and score sharing

1. Cross-team flag sharing through failed claims (6.1, item 2).
2. Scan the whole dump (every table, not only `abuser`) for every per-player flag value, to catch leaks in bios, team descriptions, notifications and so on. Search for the event's flag prefix plus the hash length found in section 4, rule 10. Do not stop at the first match.
3. Lockstep solving between teams: for the closest team pairs, shared treasures claimed within 5 minutes of each other, whether one team always trails the same team, and fast machine clears against the machine median.
4. Same item within 2 s across different teams (findings and treasures) and the largest repeat count for one player pair.
5. Score concentration: teams where almost all claim points sit in one account, and which team members never connected. Cross-check with 6.2, item 4.
6. Ledger integrity: points mismatches between `player_treasure`, `player_finding`, `treasure`, `finding`, stream rows without a claim, headshots without every flag.

### 6.4 Possible LLM or automation use

First check which text sources the dump has: writeups (`writeup`), support tickets (`inquiry`, `report`), speed challenge submissions (`speed_solution`) and logs (`debuglogs`). If they have rows, read them for machine-generated style and report what you find. If they are empty, say so and use only the indirect signals below.

1. **Well-formed flags that belong to nobody.** Failed claims shaped like a real flag (the event's flag prefix plus the right hash length) that match no player's flag (all treasures against all players). List player, team, time, string. Note if teammates submitted the same string or one player resubmitted it, which points to retrying and sharing and not to independent invention.
2. **Other failed claims:** classify as copy damage (for example a missing first character, trailing punctuation, interleaved flags, pasted command output), which points to a person copying and pasting, not a model.
3. **Speed.** Use a real 10-second window and never a self-join on the timestamp:

```sql
SELECT player_id, FROM_UNIXTIME(u) start_utc, c
FROM (
  SELECT player_id, UNIX_TIMESTAMP(ts) u,
         COUNT(*) OVER (PARTITION BY player_id ORDER BY UNIX_TIMESTAMP(ts)
                        RANGE BETWEEN CURRENT ROW AND 9 FOLLOWING) c
  FROM player_finding WHERE player_id NOT IN (<staff ids>)
) z ORDER BY c DESC LIMIT 10;
```

Run the same for `player_treasure`. A self-join over rows that share a second counts them twice, which inflates the counts a lot. Compare the result with the findings per target and the team instance ceiling (section 4, rule 6). 4. Fastest solves per difficulty from `headshot.timer` (first finding or claim to last, `[read in source]` in the trigger), claim bursts, and runs of activity with no breaks.

### 6.5 Remaining checks

1. Brute force: highest failed-claim count per player and the spacing between attempts.
2. Bandwidth: top senders and any scanner or DoS profile.
3. Instance spawning: when it started and any outliers (check `target_instance_audit` range).
4. Team changes during the event (`team_audit`), duplicate identities (same name across accounts, check employer or affiliation), accounts missing `player_metadata`.
5. Eligibility and identity observations, kept in a separate section from play behavior. First read `academic_grouping` in `sysconfig`. If the key is missing, treat grouping as off, skip the category checks and say so. `[assumed]` from the `!== false` check in the platform code, confirm it. If it is set, categories are the `academic` values on players and teams. Do not assume what each number means: take the names from `academic_Nlong`, `academic_Nshort` and `event_name_N` in `sysconfig` (N is the category number). Then check: player versus team category mismatches (the platform only lets a player join a team of the same category, `[read in source]` in the team `actionJoin`), team category against listed affiliations, and one organization on several teams.

## 7. Report format

One markdown file, no em dashes, claim tags on every load-bearing line:

1. Title (event name read from `sysconfig`), source dump name, scope, the tag legend, and "all times are UTC unless stated".
2. Summary table: finding, confidence, weakest link. One row per finding, including a row for "checked clean".
3. Data and method: tables, accounts excluded, event window, venue IPs and whether they were given or inferred, flag formula, timezone note.
4. One section per finding, with per-account evidence tables.
5. Reading the existing `abuser` data.
6. Checked, nothing found (numbered).
7. Eligibility and identity observations (only when academic grouping is on).
8. Reproduction: load steps and the detector queries.
9. Limits: what the dump cannot show (for example no per-flag log for successful claims, registration time is not discovery time, no device or ASN data, missing history before the event, instance audit range).
