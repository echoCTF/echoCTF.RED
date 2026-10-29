# Player Target Traffic: testing guide

Follow the steps from top to bottom. Each step says where to run it and has everything it needs.

## Before you start: fill in your values

Do a **find and replace across this whole file** for each placeholder.

| Placeholder | Replace with |
|---|---|
| `<PLAYER_ID>` | id of the test player |
| `<PLAYER>` | username of the test player |
| `<PLAYER_VPN_IP>` | VPN IP of the test player, once connected |
| `<TARGET_ID>` | id of an online target that is **not** in a network |
| `<TARGET>` | name of that target |
| `<TARGET_IP>` | IP of that target |

You need:

* A shell as `root` on the VPN server, in the application folder (for `./backend/yii`)
* A `mysql` shell on the `echoCTF` database
* The test player connected to the VPN, with a terminal on their machine
* One browser logged in to the backend as an admin

Find the player VPN IP and the target IP:

```sql
SELECT INET_NTOA(vpn_local_address) AS vpn_ip FROM player_last WHERE id=<PLAYER_ID>;
SELECT id, name, INET_NTOA(ip) AS ip FROM target WHERE id=<TARGET_ID>;
```

---

## Step 1: Bring up pflow1

**Where:** VPN server

```sh
cat > /etc/hostname.pflow1 <<'EOF'
flowsrc 127.0.0.1 flowdst 127.0.0.1:9995
pflowproto 5
up
EOF
sh /etc/netstart pflow1
ifconfig pflow1
ifconfig pflow0
```

**Expected:**
* `pflow1` shows `pflow: sender: 127.0.0.1 receiver: 127.0.0.1:9995 version: 5`
* If you had a `pflow0` before, it still shows its old receiver

---

## Step 2: Load the PF rules

**Where:** mysql, then VPN server

Add `pflow` to `pf_state_limits` (does nothing if it is already there):

```sql
UPDATE sysconfig SET val=CONCAT('(pflow, ', SUBSTRING(TRIM(val), 2)) WHERE id='pf_state_limits' AND val NOT LIKE '%pflow%';
SELECT val FROM sysconfig WHERE id='pf_state_limits';
```

On the VPN server, make sure `/etc/service.pf.conf` is the new `vpn.service.conf`, then:

```sh
pfctl -nf /etc/pf.conf && pfctl -f /etc/pf.conf
./backend/yii cron/pf 1
pfctl -sr | grep pflow
pfctl -a networks -sr | grep pflow
```

**Expected:**
* `pfctl -nf` prints no errors
* The `<targets>` rule and `pass from <targets> to <offense_activated>` (in the `offense` anchor) have `pflow`
* If you have networks or instances running, their rules in the `networks` anchor have `pflow`

---

## Step 3: Start the collector by hand

**Where:** VPN server, in a second terminal. Leave it running for the next steps.

The supervisord program uses the same port, so stop it first:

```sh
supervisorctl stop backend-traffic
./backend/yii traffic/collect 127.0.0.1:9995 5 1
```

**Expected:**
* `counting flows on interface indexes: ...` with the indexes of the `offense` group. Compare with `ifconfig offense | grep index`
* `traffic/collect listening on udp://127.0.0.1:9995, flushing every 5s`
* a counters line every 5 seconds

---

## Step 4: Player to target, exact numbers

This checks that each connection is counted once. `ping -s 100` sends packets of exactly 128 bytes (100 data + 8 ICMP + 20 IP).

**Where:** mysql, player machine, VPN server

1. Note the current values (mysql):
   ```sql
   SELECT packets_sent, packets_received, bytes_sent, bytes_received FROM player_target_traffic WHERE player_id=<PLAYER_ID> AND target_id=<TARGET_ID>;
   ```
   No row means all values are `0`.

2. On the player machine:
   ```sh
   ping -c 10 -s 100 <TARGET_IP>
   ```
   All 10 must get a reply.

3. On the VPN server, within 20 seconds of the ping, look at the states:
   ```sh
   pfctl -ss -v | grep -A1 <PLAYER_VPN_IP>
   ```
   You see one state for each interface the ping went through. **Only one of them must have `, pflow`** at the end of its second line.

   End the states so they get exported:
   ```sh
   pfctl -k <PLAYER_VPN_IP> -k <TARGET_IP>
   ```

4. Wait 10 seconds, then run the query from 1 again.

**Expected:** each value grew by exactly:

| packets_sent | packets_received | bytes_sent | bytes_received |
|---|---|---|---|
| +10 | +10 | +1280 | +1280 |

If it grew by `+20` / `+2560`, the connection was counted twice. The `pfctl -ss` output from 3 shows that both states had `pflow`.

---

## Step 5: Target to player

This checks the rules for targets connecting back to players.

**Where:** docker server of the target, mysql, VPN server

1. Note the current values (mysql):
   ```sql
   SELECT packets_sent, packets_received, bytes_sent, bytes_received FROM player_target_traffic WHERE player_id=<PLAYER_ID> AND target_id=<TARGET_ID>;
   ```

2. From inside the target (needs `ping` in the container):
   ```sh
   docker exec -it <TARGET> ping -c 5 -s 100 <PLAYER_VPN_IP>
   ```

3. On the VPN server:
   ```sh
   pfctl -k <TARGET_IP> -k <PLAYER_VPN_IP>
   ```

4. Wait 10 seconds, then run the query from 1 again.

**Expected:** `packets_received` +5 and `bytes_received` +640 (the target sent these), `packets_sent` +5 and `bytes_sent` +640 (the player replies). If the player firewall blocks ping, only the `received` values grow.

---

## Step 6: Target instance

**Where:** frontend, mysql, player machine, VPN server

1. As the player, spawn a private instance of any target from the frontend. Wait until it is running.

2. Find it (mysql):
   ```sql
   SELECT target_id, INET_NTOA(ip) AS ip FROM target_instance WHERE player_id=<PLAYER_ID>;
   ```

3. On the VPN server, check that its rules have `pflow`:
   ```sh
   ./backend/yii cron/pf 1
   pfctl -a networks -sr | grep pflow
   ```

4. On the player machine, use the `ip` from 2:
   ```sh
   ping -c 10 -s 100 <instance ip>
   ```

5. On the VPN server:
   ```sh
   pfctl -k <PLAYER_VPN_IP>
   ```

6. Wait 10 seconds (mysql), using the `target_id` from 2:
   ```sql
   SELECT * FROM player_target_traffic WHERE player_id=<PLAYER_ID> AND target_id=<instance target_id>;
   ```

**Expected:** a row with `packets_sent` 10 more and `bytes_sent` 1280 more than before (or exactly `10` / `1280` if the row is new).

---

## Step 7: Player that already disconnected

**Where:** player machine, VPN server, mysql

1. Note the current values (mysql):
   ```sql
   SELECT packets_sent, bytes_sent FROM player_target_traffic WHERE player_id=<PLAYER_ID> AND target_id=<TARGET_ID>;
   ```

2. On the player machine, ping and then disconnect from the VPN right away:
   ```sh
   ping -c 5 -s 100 <TARGET_IP>
   ```

3. Check the player is offline (mysql), it must show `NULL`. Then end the states on the VPN server:
   ```sql
   SELECT vpn_local_address FROM player_last WHERE id=<PLAYER_ID>;
   ```
   ```sh
   pfctl -k <PLAYER_VPN_IP>
   ```

4. Wait 10 seconds, then run the query from 1 again.

**Expected:** `packets_sent` +5 and `bytes_sent` +640, still on `<PLAYER_ID>`. The player is found from `player_vpn_history`.

Connect the player to the VPN again before the next steps. If they get a different IP, update `<PLAYER_VPN_IP>`.

---

## Step 8: Backend pages

**Where:** backend, as admin

1. Open **Activity => Player Target Traffic**
   * **Expected:** rows for `<PLAYER>` from the steps above. Filtering by username and target works. Sizes show as KiB/MiB, the exact bytes show on hover.

2. Open the player profile full view: `/frontend/profile/view-full?id=<profile id of PLAYER>`
   * **Expected:** a **Target Traffic** tab (network icon) after Target Progress. It lists only `<PLAYER>`, biggest `bytes_sent` first.

3. Log in as a backend user that is **not** an admin and open the same profile
   * **Expected:** no **Target Traffic** tab

4. Console:
   ```sh
   ./backend/yii traffic/show <PLAYER>
   ./backend/yii traffic/show <PLAYER> <TARGET>
   ```
   * **Expected:** the same numbers as the backend page

---

## Step 9: Back to normal

**Where:** VPN server

Stop the collector from step 3 with `Ctrl+C`. It saves what it has and exits. Then start the supervisord one:

```sh
supervisorctl start backend-traffic
supervisorctl status backend-traffic
```

**Expected:** `RUNNING`

To clear the test data, use **Truncate** on **Activity => Player Target Traffic**.
