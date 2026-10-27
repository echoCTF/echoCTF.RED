# Target Instances: step by step testing guide

Follow this guide **from top to bottom**. Each stage starts with a **Setup** step that puts the system in the state it needs. So you can also start any stage on its own, without going back.

## Before anything else: fill in your IDs

Do a **find and replace across this whole file** for each placeholder, so you can copy and paste everything as it is.

| Placeholder | Replace with |
|---|---|
| `<PLAYER_ID>` | id of the test player (not an admin) |
| `<T1_ID>` | id of the first target |
| `<T2_ID>` | id of the second target |
| `<T3_ID>` | id of the third target |
| `<T4_ID>` | id of the fourth target |

Look up the player:

```sql
SELECT id, username FROM player WHERE username='<player username>';
```

Look up the targets:

```sql
SELECT id, name, status, active FROM target WHERE name IN ('<target 1>','<target 2>','<target 3>','<target 4>');
```

Pick four targets that are `status=online`, `active=1`, and that the player can reach (not in a network they don't have access to).

All `./backend/yii ...` commands run from the application folder. Nothing runs in the background, so you run `cron/instances` by hand when a step says so.

You need one browser logged in to the frontend as the test player, and one logged in to the backend as an admin.

---

## Stage 0: Prepare the data

### Step 0.1: Run the migrations

```sh
./backend/yii migrate
```

### Step 0.2: Check that an instance server exists

```sql
SELECT id, name FROM server;
```

**Expected:** at least one row. If there are none, stop here. Instances can't start without a server.

### Step 0.3: Allow instances on the four targets

```sql
UPDATE target SET instance_allowed=1 WHERE id IN (<T1_ID>,<T2_ID>,<T3_ID>,<T4_ID>);
SELECT id, name, status, active, instance_allowed FROM target WHERE id IN (<T1_ID>,<T2_ID>,<T3_ID>,<T4_ID>);
```

**Expected:** all four rows show `online`, `1`, `1`.

### Step 0.4: Check that the player has no subscription

```sql
SELECT player_id, subscription_id, price_id, active FROM player_subscription WHERE player_id=<PLAYER_ID>;
```

**Expected:** no rows. If the player has a subscription, pick another player.

### Step 0.5: Create a test product that gives 3 extra instances

The product is inactive, so it doesn't show up on the subscriptions page.

```sql
INSERT INTO product (id, active, name, metadata, created_at, updated_at)
VALUES ('prod_multi_test', 0, 'Multi instance test', '{"max_target_instances":"3"}', NOW(), NOW())
ON DUPLICATE KEY UPDATE metadata=VALUES(metadata);

INSERT INTO price (id, active, product_id, unit_amount, metadata)
VALUES ('price_multi_test', 0, 'prod_multi_test', 0, '{}')
ON DUPLICATE KEY UPDATE product_id=VALUES(product_id);
```

---

## Stage 1: Starting settings

### Step 1.1: Apply the starting settings

```sql
INSERT INTO sysconfig (id,val) VALUES ('all_players_vip','1') ON DUPLICATE KEY UPDATE val='1';
DELETE FROM sysconfig WHERE id='max_target_instances';
UPDATE player_spin SET counter=0, perday=50, max_instances=1 WHERE player_id=<PLAYER_ID>;
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

### Step 1.2: Check the starting state

```sql
SELECT id, val FROM sysconfig WHERE id IN ('all_players_vip','max_target_instances');
SELECT counter, perday, max_instances FROM player_spin WHERE player_id=<PLAYER_ID>;
SELECT COUNT(*) AS instances FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:**
* `all_players_vip=1`, no `max_target_instances` row
* `counter=0`, `perday=50`, `max_instances=1`
* `instances=0`

---

## Stage 2: Limit of 1

### Step 2.1: Setup

```sql
UPDATE player_spin SET max_instances=1 WHERE player_id=<PLAYER_ID>;
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

### Step 2.2: Spawn T1

Open `/target/<T1_ID>`, click the **cogs icon**, then **Spawn a private instance**, then **OK**.

**Expected:** the flash says *"Spawning new instance for [T1]..."*

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** one row for `<T1_ID>` with an `ip`.

### Step 2.3: Try to spawn T2

Open `/target/<T2_ID>`, click the cogs icon, then **Spawn a private instance**.

**Expected:** the dialog says you already have one instance running for T1. Click **OK**.

**Expected:** the flash says *"Scheduling shutdown for old [T1] instance..."*

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** only `<T1_ID>`, with `reboot=2`. There is no T2 row.

### Step 2.4: Spawn T2 once T1 is gone

```sh
./backend/yii cron/instances
```

Spawn T2 again from `/target/<T2_ID>`.

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** only one row, for `<T2_ID>`, with an `ip`.

---

## Stage 3: Limit of 2 for this player

### Step 3.1: Setup

```sql
UPDATE player_spin SET counter=0, max_instances=1 WHERE player_id=<PLAYER_ID>;
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

### Step 3.2: Raise the player limit from the backend

On the backend, go to `/frontend/player-spin/index`, find the player and click the update icon. Set **Max instances** to `2` and save.

```sql
SELECT max_instances FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `2`.

Open the player profile on the backend.

**Expected:** the counters table has a **Max instances** row showing `2`.

### Step 3.3: Spawn T1 and T2

Spawn T1 from `/target/<T1_ID>`, then T2 from `/target/<T2_ID>`.

**Expected:** both flashes say *"Spawning new instance..."*. No shutdown message for T1.

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip, reboot FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

**Expected:** two rows, T1 and T2, both with an `ip` and `reboot=0`.

### Step 3.4: Check the dashboard and the targets page

Open `/dashboard`.

**Expected:** two instance cards, one for T1 and one for T2, each with its own IP.

Open `/targets`.

**Expected:** T1 and T2 show their instance IP in red. T3 shows its normal IP.

### Step 3.5: Check the IP endpoint

This endpoint only answers AJAX calls, so you can't just open it in a tab. Stay on any frontend page, open the browser console (F12) and paste this:

```js
[<T1_ID>, <T2_ID>, <T3_ID>].forEach(id => fetch('/target/' + id + '/ip', {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(r => r.json()).then(d => console.log(id, d.ip, d.instance)));
```

**Expected:** T1 and T2 print their own instance IP and `true`. T3 prints its normal IP and `undefined`.

### Step 3.6: Check that T3 can't be spawned

Open `/target/<T3_ID>` and click the cogs icon.

**Expected:** you see **Instance limit reached** instead of **Spawn a private instance**. Clicking it shows a dialog that names T1 and T2. Nothing gets spawned.

### Step 3.7: Reboot only T1

Open `/target/<T1_ID>`, click the cogs icon, then **Reboot your instance**, then **OK**.

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

**Expected:** T1 has `reboot=1`, T2 still has `reboot=0`.

```sh
./backend/yii cron/instances
```

**Expected:** both are back to `reboot=0`, both still have an `ip`.

### Step 3.8: Shut T2. It still counts until it's gone

Open `/target/<T2_ID>`, click the cogs icon, then **Shut your instance**, then **OK**.

**Expected:** the flash says *"Scheduling shutdown for instance [T2]..."*

Before running the cron, open `/target/<T3_ID>` and click the cogs icon.

**Expected:** still **Instance limit reached**.

Now let the cron remove T2:

```sh
./backend/yii cron/instances
```

Reload `/target/<T3_ID>` and click the cogs icon.

**Expected:** **Spawn a private instance** is back. Click it, then **OK**. The flash says *"Spawning new instance for [T3]..."*

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

**Expected:** T1 and T3, both with an `ip`.

---

## Stage 4: A subscription adds 3 instances

### Step 4.1: Setup

This leaves the player with a limit of 2, no subscription, and T1 and T3 running.

```sql
DELETE FROM player_subscription WHERE player_id=<PLAYER_ID> AND price_id='price_multi_test';
UPDATE product SET metadata='{"max_target_instances":"3"}' WHERE id='prod_multi_test';
UPDATE player_spin SET counter=0, max_instances=2 WHERE player_id=<PLAYER_ID>;
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

```sql
INSERT INTO target_instance (player_id, target_id, server_id, reboot, ip, created_at) VALUES
(<PLAYER_ID>, <T1_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 2 MINUTE),
(<PLAYER_ID>, <T3_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 1 MINUTE);
```

```sh
./backend/yii cron/instances
```

### Step 4.2: Check that the player is full

Open `/target/<T2_ID>` and click the cogs icon.

**Expected:** **Instance limit reached**.

### Step 4.3: Give the player the subscription from the backend

On the backend, go to `/sales/player-subscription/create` and fill in:

* **Player**: the test player
* **Subscription ID**: `sub_vip`
* **Price ID**: `price_multi_test`
* **Active**: `1`
* **Starting**: today, eg `2026-10-27 00:00:00`
* **Ending**: tomorrow, eg `2026-10-28 00:00:00`

Save.

```sql
SELECT max_instances FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `5` (2 + 3).

### Step 4.4: Saving again doesn't add again

Open the subscription on the backend, click **Update**, change nothing and save.

```sql
SELECT max_instances FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** still `5`.

### Step 4.5: Spawn T2

Reload `/target/<T2_ID>` and click the cogs icon.

**Expected:** **Spawn a private instance** is back. Click it, then **OK**.

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

**Expected:** three rows, T1, T3 and T2, all with an `ip`.

---

## Stage 5: The subscription expires

### Step 5.1: Setup

This leaves the player on a subscription that ended a minute ago, a limit of 5 (2 + 3), and T1, T2, T3 running (in that order).

```sql
UPDATE product SET metadata='{"max_target_instances":"3"}' WHERE id='prod_multi_test';
DELETE FROM player_subscription WHERE player_id=<PLAYER_ID> AND price_id='price_multi_test';
INSERT INTO player_subscription (player_id, subscription_id, price_id, active, `starting`, ending, created_at, updated_at)
VALUES (<PLAYER_ID>, 'sub_vip', 'price_multi_test', 1, NOW() - INTERVAL 1 DAY, NOW() - INTERVAL 1 MINUTE, NOW(), NOW());
UPDATE player_spin SET max_instances=5 WHERE player_id=<PLAYER_ID>;
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

```sql
INSERT INTO target_instance (player_id, target_id, server_id, reboot, ip, created_at) VALUES
(<PLAYER_ID>, <T1_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 3 MINUTE),
(<PLAYER_ID>, <T2_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 2 MINUTE),
(<PLAYER_ID>, <T3_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 1 MINUTE);
```

```sh
./backend/yii cron/instances
```

### Step 5.2: Expire the subscription

```sh
./backend/yii sales/cron/expire-subscriptions 1 0
```

**Expected:** the output has an *"Expiring: <username>..."* line.

```sql
SELECT COUNT(*) AS subs FROM player_subscription WHERE player_id=<PLAYER_ID>;
SELECT max_instances FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `subs=0` and `max_instances=2` (5 - 3).

### Step 5.3: Run the instances cron

```sh
./backend/yii cron/instances
```

**Expected:** the output has a line like *"Scheduling destroy T3 for <username>, over the allowed instances limit"*, followed by T3 being destroyed.

```sql
SELECT target_id FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

**Expected:** T1 and T2. The newest one (T3) is gone.

### Step 5.4: Check the dashboard

Open `/dashboard`.

**Expected:** two instance cards, T1 and T2.

---

## Stage 6: Switching a subscription off, then deleting it

### Step 6.1: Setup

This leaves the player with an active subscription and a limit of 5 (2 + 3).

```sql
UPDATE product SET metadata='{"max_target_instances":"3"}' WHERE id='prod_multi_test';
DELETE FROM player_subscription WHERE player_id=<PLAYER_ID> AND price_id='price_multi_test';
INSERT INTO player_subscription (player_id, subscription_id, price_id, active, `starting`, ending, created_at, updated_at)
VALUES (<PLAYER_ID>, 'sub_vip', 'price_multi_test', 1, NOW(), NOW() + INTERVAL 1 DAY, NOW(), NOW());
UPDATE player_spin SET max_instances=5 WHERE player_id=<PLAYER_ID>;
```

### Step 6.2: Switch it off from the backend

On the backend, open `/sales/player-subscription/update?id=<PLAYER_ID>`. Set **Active** to `0` and save.

```sql
SELECT max_instances FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `2` (5 - 3).

### Step 6.3: Deleting it doesn't take them back again

On the backend, go to `/sales/player-subscription/index` and delete the test subscription.

```sql
SELECT max_instances FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** still `2`.

---

## Stage 7: Lowering a player limit

### Step 7.1: Setup

This leaves the player with no subscription, a limit of 3, and T1, T2, T3 running (in that order).

```sql
DELETE FROM player_subscription WHERE player_id=<PLAYER_ID> AND price_id='price_multi_test';
UPDATE player_spin SET max_instances=3 WHERE player_id=<PLAYER_ID>;
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

```sql
INSERT INTO target_instance (player_id, target_id, server_id, reboot, ip, created_at) VALUES
(<PLAYER_ID>, <T1_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 3 MINUTE),
(<PLAYER_ID>, <T2_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 2 MINUTE),
(<PLAYER_ID>, <T3_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 1 MINUTE);
```

```sh
./backend/yii cron/instances
```

### Step 7.2: Lower the limit to 1

On the backend, go to `/frontend/player-spin/index`, edit the player and set **Max instances** to `1`. Save.

```sh
./backend/yii cron/instances
```

**Expected:** two *"Scheduling destroy..."* lines, one for T2 and one for T3.

```sql
SELECT target_id FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** only T1.

### Step 7.3: Check that single instance mode is back

Open `/target/<T2_ID>` and click the cogs icon.

**Expected:** **Spawn a private instance** is there, and the dialog says you already have one instance running for T1. Click **Cancel**.

---

## Stage 8: New players get the default

### Step 8.1: Setup

```sql
INSERT INTO sysconfig (id,val) VALUES ('max_target_instances','3') ON DUPLICATE KEY UPDATE val='3';
DELETE FROM player WHERE username='multiinstancetest';
```

### Step 8.2: Create a player from the backend

On the backend, go to `/frontend/player/create` and fill in:

* **Username**: `multiinstancetest`
* **Email**: `multiinstancetest@example.com`
* **Type**: Offense
* **New password**: any password
* **Status**: Active

Save.

```sql
SELECT ps.max_instances FROM player_spin ps JOIN player p ON p.id=ps.player_id WHERE p.username='multiinstancetest';
```

**Expected:** `3`.

### Step 8.3: Changing the default doesn't touch existing players

```sql
UPDATE sysconfig SET val='1' WHERE id='max_target_instances';
SELECT ps.max_instances FROM player_spin ps JOIN player p ON p.id=ps.player_id WHERE p.username='multiinstancetest';
```

**Expected:** the new player still has `3`.

---

## Stage 9: Backend instance pages

### Step 9.1: Setup

This leaves the player with a limit of 2 and T1 and T2 running.

```sql
UPDATE player_spin SET max_instances=2 WHERE player_id=<PLAYER_ID>;
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

```sql
INSERT INTO target_instance (player_id, target_id, server_id, reboot, ip, created_at) VALUES
(<PLAYER_ID>, <T1_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 2 MINUTE),
(<PLAYER_ID>, <T2_ID>, (SELECT id FROM server ORDER BY id LIMIT 1), 0, NULL, NOW() - INTERVAL 1 MINUTE);
```

```sh
./backend/yii cron/instances
```

### Step 9.2: Check the instances list

Go to **Infrastructure => Target Instances**.

**Expected:** two rows for the player, T1 and T2. Hover over the view icon of each row. The link has both `player_id` and `target_id`.

### Step 9.3: Open T2

Click the view icon on the T2 row.

**Expected:** the page shows T2, not T1. The **Update**, **Logs** and **Exec** buttons also open T2.

### Step 9.4: Restart T2 from the list

Note the current times:

```sql
SELECT target_id, updated_at FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

Back on the list, click the restart icon on the T2 row and confirm.

```sql
SELECT target_id, reboot, updated_at FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

**Expected:** only T2 has a new `updated_at`. Both still have `reboot=0`, the restart happens right away.

### Step 9.5: Check the instances tab of a target

Open `/infrastructure/target/full-view?id=<T1_ID>` on the backend and click the server icon tab (_Target Instances_).

**Expected:** the player's T1 instance is listed. Click its restart icon and confirm.

```sql
SELECT target_id, reboot, updated_at FROM target_instance WHERE player_id=<PLAYER_ID> ORDER BY created_at;
```

**Expected:** now T1 has a new `updated_at` too. Both still have `reboot=0`.

### Step 9.6: Destroy T2 from the list

On **Infrastructure => Target Instances**, click the destroy icon on the T2 row and confirm.

```sql
SELECT target_id FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** only T1.

---

## Stage 10: Clean up

### Step 10.1: Remove the instances and the test data

```sql
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

```sql
DELETE FROM player_subscription WHERE player_id=<PLAYER_ID> AND price_id='price_multi_test';
DELETE FROM price WHERE id='price_multi_test';
DELETE FROM product WHERE id='prod_multi_test';
DELETE FROM player WHERE username='multiinstancetest';
UPDATE player_spin SET counter=0 WHERE player_id=<PLAYER_ID>;
```

### Step 10.2: Restore your own values

Set `max_target_instances`, `all_players_vip`, the player `perday` and `max_instances`, and the target `instance_allowed` flags back to what your environment normally uses. For example:

```sql
DELETE FROM sysconfig WHERE id IN ('max_target_instances','all_players_vip');
UPDATE player_spin SET max_instances=1 WHERE player_id=<PLAYER_ID>;
```

---
