# Instances Only: step by step testing guide

Follow this guide **from top to bottom**. Every step has everything it needs right there: the settings to apply, the exact SQL to run, the command, and what you should see. You never need to look anywhere else in the document.

## Before anything else: fill in your IDs

Every query uses five placeholders. Before you start, do a **find and replace across this whole file** for each one, so you can copy and paste every query as it is.

| Placeholder | Replace with |
|---|---|
| `<PLAYER_ID>` | id of the normal player (no subscription, not an admin) |
| `<VIP_ID>` | id of the VIP player |
| `<T_ALWAYS_ID>` | id of the always-on target |
| `<T_OD_ID>` | id of the first on-demand target (instances allowed) |
| `<T_OD_NOINST_ID>` | id of the second on-demand target (instances **not** allowed) |

Use this query to look up the player IDs:

```sql
SELECT id, username FROM player WHERE username IN ('<player username>','<vip username>');
```

And this one to look up the target IDs:

```sql
SELECT id, name, status, active FROM target WHERE name IN ('<always-on target name>','<on-demand target 1 name>','<on-demand target 2 name>');
```

Pick three targets that have `status=online` and `active=1` and are **not** part of a private network.

All `./backend/yii ...` commands are run from the application folder. Nothing runs in the background in this environment, so every background job is run by hand when a step says so.

You need two browsers (or one normal and one private window) and VPN configuration files for both players.

---

## Stage 0: Prepare the data

### Step 0.1: Check that an instance server exists

```sql
SELECT id, name, INET_NTOA(ip) AS ip FROM server;
```

**Expected:** at least one row. If there are none, stop: private instances can't work without a server.

### Step 0.2: Make VIP a VIP

```sql
INSERT INTO sysconfig (id,val) VALUES ('admin_player:<VIP_ID>','1') ON DUPLICATE KEY UPDATE val='1';
```

Then make sure PLAYER is **not** VIP:

```sql
DELETE FROM sysconfig WHERE id='admin_player:<PLAYER_ID>';
SELECT player_id, active, ending FROM player_subscription WHERE player_id=<PLAYER_ID>;
```

**Expected:** the SELECT returns no rows, or no row with `active=1`. If PLAYER has an active subscription, pick a different player. If MySQL says the `player_subscription` table doesn't exist, subscriptions aren't installed, so PLAYER has none and you can carry on.

### Step 0.3: Set the target flags

```sql
UPDATE target SET instance_allowed=1, player_spin=1 WHERE id IN (<T_ALWAYS_ID>,<T_OD_ID>);
UPDATE target SET instance_allowed=0, player_spin=1 WHERE id=<T_OD_NOINST_ID>;
```

### Step 0.4: Make the two on-demand targets on-demand, and the always-on target not

```sql
DELETE FROM target_ondemand WHERE target_id=<T_ALWAYS_ID>;
INSERT INTO target_ondemand (target_id, player_id, state, heartbeat, created_at, updated_at)
VALUES (<T_OD_ID>, NULL, -1, NULL, NOW(), NOW()), (<T_OD_NOINST_ID>, NULL, -1, NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE state=-1, heartbeat=NULL, player_id=NULL;
```

### Step 0.5: Check the targets

```sql
SELECT t.id, t.name, t.status, t.active, t.instance_allowed, t.player_spin, INET_NTOA(t.ip) AS ip, o.state AS ondemand_state
FROM target t LEFT JOIN target_ondemand o ON o.target_id=t.id
WHERE t.id IN (<T_ALWAYS_ID>,<T_OD_ID>,<T_OD_NOINST_ID>);
```

**Expected:**

| target | status | active | instance_allowed | player_spin | ondemand_state |
|---|---|---|---|---|---|
| T_ALWAYS | online | 1 | 1 | 1 | NULL |
| T_OD | online | 1 | 1 | 1 | -1 |
| T_OD_NOINST | online | 1 | 0 | 1 | -1 |

Write down the `ip` of T_ALWAYS and T_OD. The steps below call them **T_ALWAYS IP** and **T_OD IP**.

---

## Stage 1: Starting settings

### Step 1.1: Apply the starting settings

```sql
DELETE FROM sysconfig WHERE id IN ('instances_only','disable_ondemand_operations','all_players_vip');
INSERT INTO sysconfig (id,val) VALUES ('spins_per_day','3') ON DUPLICATE KEY UPDATE val='3';
UPDATE player_spin SET counter=0, perday=3 WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
DELETE FROM target_instance WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
DELETE FROM spin_queue;
```

### Step 1.2: Check the starting state

```sql
SELECT id, val FROM sysconfig WHERE id IN ('instances_only','disable_ondemand_operations','all_players_vip','spins_per_day');
SELECT player_id, counter, perday FROM player_spin WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
SELECT COUNT(*) AS instances FROM target_instance WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
SELECT COUNT(*) AS queued FROM spin_queue;
SELECT target_id, state FROM target_ondemand WHERE target_id IN (<T_OD_ID>,<T_OD_NOINST_ID>);
```

**Expected:**
* the settings query returns only `spins_per_day = 3`
* both players have `counter=0` and `perday=3`
* `instances = 0` and `queued = 0`
* both on-demand targets have `state=-1`

> **Note:** spin counters are reset once a day. If you continue on another day, run step 1.1 again.

---

## Stage 2: Baseline with the mode OFF

This stage shows that normal behaviour is unchanged before the mode is switched on.

### Step 2.1: Make sure the mode is off

```sql
DELETE FROM sysconfig WHERE id IN ('instances_only','disable_ondemand_operations','all_players_vip');
UPDATE player_spin SET counter=0, perday=3 WHERE player_id=<PLAYER_ID>;
```

### Step 2.2: Log in as PLAYER and connect to the VPN

1. In **browser 1**, log in to the frontend as PLAYER.
2. Connect PLAYER to the VPN.
3. Wait about 10 seconds, then check that the connection is registered:

   ```sql
   SELECT player_id, INET_NTOA(vpn_local_address) AS vpn_ip FROM player_last WHERE id=<PLAYER_ID>;
   ```

   **Expected:** `vpn_ip` is filled in. Powering up and rebooting don't work without it.

### Step 2.3: Check T_OD on the Targets page

Open `/targets` and find T_OD.

**Expected:** the IP column shows `0.0.0.0`. Hovering over it shows *"System currently powered down. Go to the target page to power it up."*

### Step 2.4: Check the T_OD page

Open `/target/<T_OD_ID>`.

**Expected:** the page says *"This target is currently powered off."* The card shows `0.0.0.0`.

### Step 2.5: Check the T_OD actions menu

Click the **cogs icon** on the T_OD card.

**Expected:** **Power up this target** and **Spawn a private instance** are both listed.

### Step 2.6: Check that PLAYER can't spawn instances

Click **Spawn a private instance**.

**Expected:** a dialog says you don't have an active subscription. Close it.

### Step 2.7: Check the T_ALWAYS actions menu

Open `/target/<T_ALWAYS_ID>` and click the cogs icon.

**Expected:** **Spawn a private instance** (with the subscription dialog) and **Reboot target** are listed.

### Step 2.8: Power up T_OD

Go back to `/target/<T_OD_ID>`, open the cogs menu, click **Power up this target** and confirm.

**Expected:** the flash says *"Target [T_OD] queued to power-up..."*

```sql
SELECT target_id, player_id FROM spin_queue;
```

**Expected:** one row, for `<T_OD_ID>` and `<PLAYER_ID>`.

### Step 2.9: Process the power up

```sh
./backend/yii cron/spin-queue
```

**Expected:** the output contains `Processing queue [T_OD] ... by [PLAYER] ... OK`.

```sql
SELECT target_id, state, heartbeat FROM target_ondemand WHERE target_id=<T_OD_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
SELECT COUNT(*) AS queued FROM spin_queue;
```

**Expected:**
* T_OD has `state=1` and a heartbeat time
* `counter=1`
* `queued=0`

### Step 2.10: Check that T_OD's IP is now visible

Reload `/target/<T_OD_ID>`.

**Expected:** the card shows the **T_OD IP**, and the page shows *"The target will shutdown in"* with a countdown.

Then press **F12**, open the **Console** tab and run:

```js
fetch('/target/<T_OD_ID>/ip',{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(console.log)
```

**Expected:** `{ip: "<T_OD IP>"}`

Leave T_OD powered on. Stage 3 needs it that way.

---

## Stage 3: Turn the mode ON

### Step 3.1: Turn instances only on

```sql
INSERT INTO sysconfig (id,val) VALUES ('instances_only','1') ON DUPLICATE KEY UPDATE val='1';
DELETE FROM sysconfig WHERE id IN ('disable_ondemand_operations','all_players_vip');
UPDATE player_spin SET counter=0, perday=3 WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
```

Check it:

```sql
SELECT id, val FROM sysconfig WHERE id IN ('instances_only','disable_ondemand_operations','all_players_vip');
SELECT target_id, state FROM target_ondemand WHERE target_id=<T_OD_ID>;
```

**Expected:** only `instances_only = 1` is returned, and T_OD is still at `state=1` (powered on).

### Step 3.2: Let cron shut down running on-demand targets

```sh
./backend/yii cron/ondemand
```

**Expected:** the output contains `Destroying ondemand target <T_OD fqdn>`.

```sql
SELECT target_id, state, heartbeat FROM target_ondemand WHERE target_id IN (<T_OD_ID>,<T_OD_NOINST_ID>);
```

**Expected:** both targets have `state=-1` and `heartbeat=NULL`.

### Step 3.3: Run it again

```sh
./backend/yii cron/ondemand
```

**Expected:** no output. Nothing else gets destroyed.

---

## Stage 4: What players see with the mode ON

### Step 4.1: Settings for this stage

```sql
INSERT INTO sysconfig (id,val) VALUES ('instances_only','1') ON DUPLICATE KEY UPDATE val='1';
DELETE FROM sysconfig WHERE id IN ('disable_ondemand_operations','all_players_vip');
DELETE FROM target_instance WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
UPDATE player_spin SET counter=0, perday=3 WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
```

PLAYER stays logged in to browser 1 and connected to the VPN.

### Step 4.2: Check the Targets page

As PLAYER, reload `/targets`.

**Expected:**
* T_OD shows `0.0.0.0`. Hovering over it shows *"Spawn a private instance of this target to get an IP."*
* T_ALWAYS shows the **T_ALWAYS IP**

### Step 4.3: Check the T_OD page

Open `/target/<T_OD_ID>`.

**Expected:**
* **no** *"This target is currently powered off"* message and no countdown
* the card shows `0.0.0.0`, with the tooltip *"Spawn a private instance of this target to get an IP."*

### Step 4.4: Check the T_OD actions menu

Click the cogs icon.

**Expected:**
* **Spawn a private instance** is listed (and **Spawn a team instance**, if team instances are enabled)
* **Power up this target** is **not** listed

### Step 4.5: Check the spawn dialog

Click **Spawn a private instance**, read the dialog, then press **Cancel**.

**Expected:** *"You are about to spawn a private instance of this target..."*, with **no** mention of a subscription.

### Step 4.6: Check the T_ALWAYS actions menu

Open `/target/<T_ALWAYS_ID>` and click the cogs icon.

**Expected:**
* **Spawn a private instance** is listed, with no subscription dialog
* **Reboot target** is **not** listed

### Step 4.7: Check T_OD_NOINST

Open `/target/<T_OD_NOINST_ID>` and click the cogs icon, if there is one.

**Expected:** the card shows `0.0.0.0`, and there are no actions (or no cogs icon at all).

### Step 4.8: Check the IP endpoint

In the browser console (F12 → Console), run:

```js
['<T_OD_ID>','<T_OD_NOINST_ID>','<T_ALWAYS_ID>'].forEach(id=>fetch('/target/'+id+'/ip',{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(j=>console.log(id,j)))
```

**Expected:**
* `<T_OD_ID>` returns `{ip: "0.0.0.0"}`
* `<T_OD_NOINST_ID>` returns `{ip: "0.0.0.0"}`
* `<T_ALWAYS_ID>` returns `{ip: "<T_ALWAYS IP>"}`

### Step 4.9: Repeat as VIP

In **browser 2**, log in as VIP, connect VIP to the VPN, and repeat steps 4.2 to 4.7.

**Expected:** VIP sees exactly the same as PLAYER.

Log VIP out afterwards.

### Step 4.10: Check that nothing changed

```sql
SELECT COUNT(*) AS instances FROM target_instance WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
SELECT COUNT(*) AS queued FROM spin_queue;
SELECT player_id, counter FROM player_spin WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
```

**Expected:** `instances=0`, `queued=0`, and both counters are `0`.

---

## Stage 5: Instance lifecycle and spin limits (as PLAYER)

### Step 5.1: Settings for this stage

```sql
INSERT INTO sysconfig (id,val) VALUES ('instances_only','1') ON DUPLICATE KEY UPDATE val='1';
DELETE FROM sysconfig WHERE id IN ('disable_ondemand_operations','all_players_vip');
DELETE FROM target_instance WHERE player_id=<PLAYER_ID>;
UPDATE player_spin SET counter=0, perday=3 WHERE player_id=<PLAYER_ID>;
```

PLAYER stays logged in to browser 1 and connected to the VPN.

### Step 5.2: Spawn an instance of T_OD

Open `/target/<T_OD_ID>`, click the cogs icon, then **Spawn a private instance**, then **OK**.

**Expected:** the flash says *"Spawning new instance for [T_OD]. You will receive a notification when the instance is up."*

```sql
SELECT target_id, INET_NTOA(ip) AS ip, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:**
* one row: `target_id=<T_OD_ID>`, `ip=NULL`, `reboot=0`
* `counter=1`

### Step 5.3: Start the instance

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:**
* `ip` is now filled in (the **instance IP**) and `reboot=0`
* `counter=1`

Reload `/target/<T_OD_ID>`.

**Expected:** the card shows the instance IP in **red**.

In the browser console, run:

```js
fetch('/target/<T_OD_ID>/ip',{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(console.log)
```

**Expected:** `{ip: "<instance IP>", instance: true}`

From PLAYER's VPN machine, `ping <instance IP>` should get replies.

### Step 5.4: Reboot the instance

Click the cogs icon.

**Expected:** **Reboot your instance** and **Shut your instance** are listed.

Click **Reboot your instance**, then **OK**.

**Expected:** the flash says *"Target instance [T_OD] scheduled for restart..."*

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `reboot=1` and `counter=2`.

### Step 5.5: Process the reboot

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** `reboot=0`, and `ip` is still filled in.

### Step 5.6: Shut the instance

Reload `/target/<T_OD_ID>`, click the cogs icon, then **Shut your instance**, then **OK**.

**Expected:** the flash says *"Scheduling shutdown for instance [T_OD]..."*

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `reboot=2` and `counter=2` (shutting down doesn't use a spin).

### Step 5.7: Process the shutdown

```sh
./backend/yii cron/instances
```

```sql
SELECT COUNT(*) AS instances FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** `instances=0`. After a reload, the T_OD card is back to `0.0.0.0`.

### Step 5.8: Spawn an instance of T_ALWAYS

Open `/target/<T_ALWAYS_ID>`, click the cogs icon, then **Spawn a private instance**, then **OK**.

**Expected:** the flash says *"Spawning new instance for [T_ALWAYS]..."*

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter, perday FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** one row with `target_id=<T_ALWAYS_ID>`, and `counter=3`, `perday=3`. PLAYER has now used all spins for the day.

### Step 5.9: Start the T_ALWAYS instance

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** `ip` is filled in and `reboot=0`.

### Step 5.10: Try to spawn with no spins left

Open `/target/<T_OD_ID>`, click the cogs icon, then **Spawn a private instance**, then **OK**.

**Expected:** a red error flash says *"You've used all your spins; your counter is maxed out!"*

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** still only the T_ALWAYS row with `reboot=0`, and `counter=3`.

### Step 5.11: Try to reboot with no spins left

Open `/target/<T_ALWAYS_ID>`, click the cogs icon, then **Reboot your instance**, then **OK**.

**Expected:** the same *"counter is maxed out"* error.

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `reboot=0` and `counter=3`.

### Step 5.12: Shut down with no spins left

Click the cogs icon, then **Shut your instance**, then **OK**.

**Expected:** the shutdown is accepted (*"Scheduling shutdown for instance [T_ALWAYS]..."*).

```sql
SELECT target_id, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `reboot=2` and `counter=3`.

### Step 5.13: Process the shutdown

```sh
./backend/yii cron/instances
```

```sql
SELECT COUNT(*) AS instances FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** `instances=0`.

---

## Stage 6: The spin endpoint is blocked

### Step 6.1: Settings for this stage

```sql
INSERT INTO sysconfig (id,val) VALUES ('instances_only','1') ON DUPLICATE KEY UPDATE val='1';
DELETE FROM sysconfig WHERE id IN ('disable_ondemand_operations','all_players_vip');
UPDATE player_spin SET counter=0, perday=3 WHERE player_id=<PLAYER_ID>;
DELETE FROM spin_queue;
```

PLAYER stays logged in to browser 1 and connected to the VPN.

### Step 6.2: Try to spin T_OD from the browser

Open `/target/<T_OD_ID>`. In the browser console (F12 → Console), run this. It sends the same request the old **Power up** menu item sent:

```js
(function(){var f=document.createElement('form');f.method='post';f.action='/target/<T_OD_ID>/spin';var i=document.createElement('input');i.type='hidden';i.name=yii.getCsrfParam();i.value=yii.getCsrfToken();f.appendChild(i);document.body.appendChild(f);f.submit();})()
```

**Expected:** the page reloads with a red flash: *"Not allowed to spin target. Target cannot be spun."*

```sql
SELECT COUNT(*) AS queued FROM spin_queue;
SELECT counter FROM player_spin WHERE player_id=<PLAYER_ID>;
```

**Expected:** `queued=0` and `counter=0`.

### Step 6.3: Try to spin T_ALWAYS from the browser

Open `/target/<T_ALWAYS_ID>`. In the console, run:

```js
(function(){var f=document.createElement('form');f.method='post';f.action='/target/<T_ALWAYS_ID>/spin';var i=document.createElement('input');i.type='hidden';i.name=yii.getCsrfParam();i.value=yii.getCsrfToken();f.appendChild(i);document.body.appendChild(f);f.submit();})()
```

**Expected:** the same red flash, *"Not allowed to spin target. Target cannot be spun."*

```sql
SELECT COUNT(*) AS queued FROM spin_queue;
```

**Expected:** `queued=0`.

### Step 6.4: A stale power up request for T_OD gets dropped

Simulate a power up request that was queued just before the mode was turned on:

```sql
INSERT INTO spin_queue (target_id, player_id, created_at) VALUES (<T_OD_ID>, <PLAYER_ID>, NOW());
```

```sh
./backend/yii cron/spin-queue
```

**Expected:** the output contains `Dropping queue [T_OD] by [PLAYER], on-demand disabled (instances_only)`.

```sql
SELECT COUNT(*) AS queued FROM spin_queue;
SELECT target_id, state FROM target_ondemand WHERE target_id=<T_OD_ID>;
```

**Expected:** `queued=0`, and T_OD is still at `state=-1`. It was **not** powered up.

### Step 6.5: A queued reboot of T_ALWAYS is still processed

```sql
INSERT INTO spin_queue (target_id, player_id, created_at) VALUES (<T_ALWAYS_ID>, <PLAYER_ID>, NOW());
```

```sh
./backend/yii cron/spin-queue
```

**Expected:** the output contains `Processing queue [T_ALWAYS] ... OK`.

```sql
SELECT COUNT(*) AS queued FROM spin_queue;
```

**Expected:** `queued=0`.

---

## Stage 7: Turn the mode OFF while an instance is running

### Step 7.1: Settings for this stage

```sql
INSERT INTO sysconfig (id,val) VALUES ('instances_only','1') ON DUPLICATE KEY UPDATE val='1';
DELETE FROM sysconfig WHERE id IN ('disable_ondemand_operations','all_players_vip');
DELETE FROM target_instance WHERE player_id=<PLAYER_ID>;
UPDATE player_spin SET counter=0, perday=3 WHERE player_id=<PLAYER_ID>;
```

PLAYER stays logged in to browser 1 and connected to the VPN.

### Step 7.2: Spawn and start an instance of T_ALWAYS

Open `/target/<T_ALWAYS_ID>`, click the cogs icon, then **Spawn a private instance**, then **OK**.

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip, reboot FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** one row for `<T_ALWAYS_ID>`, with `ip` filled in and `reboot=0`.

### Step 7.3: Turn the mode off

```sql
DELETE FROM sysconfig WHERE id='instances_only';
SELECT id, val FROM sysconfig WHERE id IN ('instances_only','disable_ondemand_operations','all_players_vip');
```

**Expected:** the SELECT returns no rows.

### Step 7.4: Check the T_ALWAYS page

Reload `/target/<T_ALWAYS_ID>` and click the cogs icon.

**Expected:**
* the card still shows the instance IP in red
* **Spawn a private instance** shows the **subscription** dialog
* **Shut your instance** is **not** listed

### Step 7.5: Check the T_OD page

Open `/target/<T_OD_ID>` and click the cogs icon.

**Expected:**
* *"This target is currently powered off."* is shown again
* **Power up this target** is listed again

On `/targets`, hovering over T_OD's `0.0.0.0` shows *"System currently powered down. Go to the target page to power it up."*

### Step 7.6: Check the IP endpoint

In the browser console, run:

```js
fetch('/target/<T_OD_ID>/ip',{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(console.log)
```

**Expected:** `{ip: "0.0.0.0"}`, because the target is powered off.

### Step 7.7: Destroy the leftover instance

```sql
UPDATE target_instance SET reboot=2 WHERE player_id=<PLAYER_ID>;
```

```sh
./backend/yii cron/instances
```

```sql
SELECT COUNT(*) AS instances FROM target_instance WHERE player_id=<PLAYER_ID>;
```

**Expected:** `instances=0`.

Don't delete the row directly: that would leave the instance container running.

---

## Stage 8: Regression check for `disable_ondemand_operations` on its own

This stage confirms the older setting still works as before and does **not** open instances to everyone.

### Step 8.1: Settings for this stage

```sql
DELETE FROM sysconfig WHERE id IN ('instances_only','all_players_vip');
INSERT INTO sysconfig (id,val) VALUES ('disable_ondemand_operations','1') ON DUPLICATE KEY UPDATE val='1';
DELETE FROM target_instance WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
UPDATE player_spin SET counter=0, perday=3 WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
SELECT id, val FROM sysconfig WHERE id IN ('instances_only','disable_ondemand_operations','all_players_vip');
```

**Expected:** only `disable_ondemand_operations = 1` is returned.

### Step 8.2: Check the T_OD page as PLAYER

In browser 1, open `/target/<T_OD_ID>` and click the cogs icon.

**Expected:**
* no *"currently powered off"* message
* **Power up this target** is **not** listed

### Step 8.3: Check the T_ALWAYS page as PLAYER

Open `/target/<T_ALWAYS_ID>` and click the cogs icon.

**Expected:**
* **Spawn a private instance** shows the **subscription** dialog
* **Reboot target** is **not** listed

### Step 8.4: Spawn an instance as VIP

In browser 2, log in as VIP and open `/target/<T_OD_ID>`. Click the cogs icon, then **Spawn a private instance**, then **OK**.

**Expected:** the flash says *"Spawning new instance for [T_OD]..."*

```sql
SELECT player_id, target_id, reboot FROM target_instance WHERE player_id=<VIP_ID>;
SELECT counter FROM player_spin WHERE player_id=<VIP_ID>;
```

**Expected:** one row for `<T_OD_ID>`, and `counter=1`.

### Step 8.5: Start VIP's instance

```sh
./backend/yii cron/instances
```

```sql
SELECT target_id, INET_NTOA(ip) AS ip FROM target_instance WHERE player_id=<VIP_ID>;
```

**Expected:** `ip` is filled in. Reload the page as VIP: the instance IP is shown in red.

### Step 8.6: Shut VIP's instance

As VIP, click the cogs icon, then **Shut your instance**, then **OK**.

```sh
./backend/yii cron/instances
```

```sql
SELECT COUNT(*) AS instances FROM target_instance WHERE player_id=<VIP_ID>;
```

**Expected:** `instances=0`.

---

## Stage 9: Clean up

### Step 9.1: Restore the settings and remove the test data

```sql
DELETE FROM sysconfig WHERE id IN ('instances_only','disable_ondemand_operations','admin_player:<VIP_ID>');
UPDATE player_spin SET counter=0 WHERE player_id IN (<PLAYER_ID>,<VIP_ID>);
DELETE FROM spin_queue;
```

### Step 9.2: Restore your own values

Set `spins_per_day`, the target flags and the on-demand entries back to what your environment normally uses. For example:

```sql
INSERT INTO sysconfig (id,val) VALUES ('spins_per_day','<your value>') ON DUPLICATE KEY UPDATE val='<your value>';
```

---

## Sign-off

| Stage | Steps | Pass / Fail | Notes |
|---|---|---|---|
| 0. Prepare the data | 0.1 to 0.5 | | |
| 1. Starting settings | 1.1 to 1.2 | | |
| 2. Baseline, mode OFF | 2.1 to 2.10 | | |
| 3. Turn mode ON | 3.1 to 3.3 | | |
| 4. Player view, mode ON | 4.1 to 4.10 | | |
| 5. Instance lifecycle and spin limits | 5.1 to 5.13 | | |
| 6. Spin endpoint blocked | 6.1 to 6.5 | | |
| 7. Turn mode OFF with a running instance | 7.1 to 7.7 | | |
| 8. `disable_ondemand_operations` regression | 8.1 to 8.6 | | |
| 9. Clean up | 9.1 to 9.2 | | |

Tested by: ____________________  Date: ____________  Build / commit: ____________
