# Private Network PF rules: testing guide

`cron/pf` creates the pass rules between each private network and its players. This guide checks that players can reach their private network targets, and that bad network names are skipped.

Follow the steps from top to bottom. Each step says where to run it and has everything it needs.

## Before you start: fill in your values

Do a **find and replace across this whole file** for each placeholder.

| Placeholder       | Replace with                                   |
| ----------------- | ---------------------------------------------- |
| `<PLAYER_ID>`     | id of the player that owns the private network |
| `<PLAYER_VPN_IP>` | VPN IP of that player, once connected          |
| `<NETWORK>`       | name of the private network                    |
| `<PN_TARGET_IP>`  | IP of a running target in that network         |

You need:

- A shell as `root` on the VPN server, in the application folder (for `./backend/yii`)
- A `mysql` shell on the `echoCTF` database
- The player connected to the VPN, with a terminal on their machine

Find the network, its running targets and the player VPN IP:

```sql
SELECT pn.name, pnt.target_id, INET_NTOA(pnt.ip) AS ip FROM private_network_target pnt JOIN private_network pn ON pn.id=pnt.private_network_id WHERE pn.player_id=<PLAYER_ID> AND pnt.state<2 AND pnt.server_id IS NOT NULL AND pnt.ip>0;
SELECT INET_NTOA(vpn_local_address) AS vpn_ip FROM player_last WHERE id=<PLAYER_ID>;
```

If the first query returns no rows, start a target in the private network first and run `./backend/yii private-network/target-ops` on the VPN server.

---

## Step 1: The rules are created

**Where:** VPN server

```sh
./backend/yii cron/pf 1
grep '<NETWORK>' /etc/targets_networks.conf
pfctl -a networks -sr | grep '<NETWORK>'
```

**Expected:** the same rules in both outputs:

- `pass quick inet from <<NETWORK>_clients> to <<NETWORK>> ...`
- `pass quick from <<NETWORK>> to <<NETWORK>_clients> ...`

Each network has only one pair of rules, even with more than one target.

---

## Step 2: The tables are filled

**Where:** VPN server

```sh
pfctl -t <NETWORK> -T show
pfctl -t <NETWORK>_clients -T show
```

**Expected:**

- `<NETWORK>` has `<PN_TARGET_IP>`
- `<NETWORK>_clients` has `<PLAYER_VPN_IP>`

If the player is missing from `<NETWORK>_clients`, reconnect the player to the VPN.

---

## Step 3: The player reaches the target

**Where:** player machine

```sh
ping -c 3 <PN_TARGET_IP>
```

**Expected:** 3 replies. Also try a port that the target has open, for example:

```sh
nc -zv <PN_TARGET_IP> <port>
```

---

## Step 4: A bad network name is skipped

**Where:** mysql, then VPN server

1. Give the network a name that is not a valid PF table name:

   ```sql
   UPDATE private_network SET name='bad name' WHERE name='<NETWORK>';
   ```

2. On the VPN server:

   ```sh
   ./backend/yii cron/pf 1
   pfctl -a networks -sr | wc -l
   ```

**Expected:**

- `PF: skipping private network [bad name], not a valid pf table name`
- The other rules still load. The rule count is above `0` if you have other networks or instances running.

3. Put the name back and reload:

   ```sql
   UPDATE private_network SET name='<NETWORK>' WHERE name='bad name';
   ```

   ```sh
   ./backend/yii cron/pf 1
   grep '<NETWORK>' /etc/targets_networks.conf
   ```

**Expected:** the rules from step 1 are back.

---

## Step 5: Stopped targets lose their rules

**Where:** mysql, then VPN server

Skip this step if the network has other running targets, the rules stay as long as one target runs.

1. Stop the target from the backend, or:

   ```sql
   UPDATE private_network_target SET state=2 WHERE ip=INET_ATON('<PN_TARGET_IP>');
   ```

2. On the VPN server:

   ```sh
   ./backend/yii private-network/target-ops
   ./backend/yii cron/pf 1
   grep '<NETWORK>' /etc/targets_networks.conf
   ```

**Expected:** no rules for `<NETWORK>`.

Start the target again from the frontend when you are done.
