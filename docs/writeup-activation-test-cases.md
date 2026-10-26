# Writeup Activation Test Cases

## How to use this

* Each section starts with a **Start** block. Run all of it before the first step of that section. It runs the reset SQL and sets every key the tests depend on, so you never need to remember what an earlier section left behind.
* Then do the steps **in order, without resetting in between**. Each step starts from where the previous one ended, and every command a step needs is written in that step.
* "Click a writeup and confirm" means clicking a writeup on target 13's page and pressing OK in the dialog.
* `./backend/yii sysconfig/set <key> 0` turns a key off.

Run `./backend/yii migrate` once before starting.

## Test data

**Target `13`**
* `writeup_allowed=1`, with at least one **approved** writeup by another player
* 4 flags worth **1500**, **900**, **1300** and **1000**, plus 1 service finding worth **100** (4,800 points total)

**Player `13969`**, a brand new, non-admin player with no headshots.

Don't claim all 4 flags plus the finding in any test, because that's a headshot on target 13 and it changes the results.

## SQL

```sql
-- set the score (use the value the step gives)
UPDATE player_score SET points=10000 WHERE player_id=13969;

-- check the activation and its stream row
SELECT * FROM player_target_help WHERE player_id=13969 AND target_id=13;
SELECT id, points, ts FROM stream WHERE player_id=13969 AND model='player_target_help' AND model_id=13;

-- check points awarded per claim on target 13
SELECT pt.treasure_id, pt.points FROM player_treasure pt JOIN treasure t ON t.id=pt.treasure_id WHERE pt.player_id=13969 AND t.target_id=13;
SELECT pf.finding_id, pf.points FROM player_finding pf JOIN finding f ON f.id=pf.finding_id WHERE pf.player_id=13969 AND f.target_id=13;
```

**Reset SQL** (clears the player's progress on target 13; set the score again afterwards):

```sql
DELETE FROM stream WHERE player_id=13969 AND (
  (model='treasure' AND model_id IN (SELECT id FROM treasure WHERE target_id=13)) OR
  (model='finding' AND model_id IN (SELECT id FROM finding WHERE target_id=13)) OR
  (model='player_target_help' AND model_id=13));
DELETE FROM player_treasure WHERE player_id=13969 AND treasure_id IN (SELECT id FROM treasure WHERE target_id=13);
DELETE FROM player_finding WHERE player_id=13969 AND finding_id IN (SELECT id FROM finding WHERE target_id=13);
SET @TRIGGER_CHECKS=FALSE;
DELETE FROM headshot WHERE player_id=13969 AND target_id=13;
SET @TRIGGER_CHECKS=TRUE;
DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;
```

Run the reset SQL in one go, in the same MySQL session. The headshot line runs with triggers off on purpose: the headshot delete trigger removes the headshot stream entries of **every** player on target 13, not just this one.

## 1. Legacy behavior

**Start:** run the reset SQL once, then:

```
./backend/yii sysconfig/set plus_writeups 1
./backend/yii sysconfig/set writeup_stuck_hours 0
./backend/yii sysconfig/set writeup_cost_percent 0
./backend/yii sysconfig/set stream_player_target_help 0
./backend/yii sysconfig/set writeups_use_require_xp 0
```
```sql
UPDATE target SET required_xp=0 WHERE id=13;
UPDATE player_score SET points=10000 WHERE player_id=13969;
```

**1.1 Free activation with halving**
* Click a writeup. The dialog says remaining flags are reduced by 50%. Confirm.
* Expected: succeeds. PTH `cost=0`. No stream row. Score stays **10,000**.

**1.2 Halving applies**
* Claim the **900** flag.
* Expected: awarded **450**.

**1.3 Writeups are readable**
* Click a writeup.
* Expected: it opens and you can read it.

## 2. Existing checks

Some steps need a page that was loaded **before** a change, because after the change the page no longer shows the activate links. Those steps use two browser tabs, called **A** and **B**.

**Start:** run the reset SQL once, then:

```
./backend/yii sysconfig/set plus_writeups 1
./backend/yii sysconfig/set writeup_stuck_hours 0
./backend/yii sysconfig/set writeup_cost_percent 0
./backend/yii sysconfig/set stream_player_target_help 0
./backend/yii sysconfig/set writeups_use_require_xp 0
```
```sql
UPDATE target SET required_xp=0 WHERE id=13;
UPDATE player_score SET points=10000 WHERE player_id=13969;
```

**2.1 Activation works**
* Open target 13 in tab **A** and in tab **B**.
* In tab A, click a writeup and confirm.
* Expected: succeeds.

**2.2 Already activated**
* In tab **B** (not reloaded), click a writeup and confirm.
* Expected: "You have already enabled writeups for this target."

**2.3 Activation limit reached**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* Give the player an activation on another target, which uses up their one slot:
  ```sql
  INSERT INTO player_target_help (player_id,target_id,cost,created_at) SELECT 13969, MIN(id), 0, NOW() FROM target WHERE id<>13;
  ```
* Reload tab A, click a writeup and confirm.
* Expected: "You have activated too many writeups, headshot some of the targets first."
* Clean up: `DELETE FROM player_target_help WHERE player_id=13969 AND target_id<>13;`

**2.4 Players with a headshot don't need to activate**
* Reload tab A.
* Give the player a headshot on target 13 (triggers off, so no points, spins or stream entries):
  ```sql
  SET @TRIGGER_CHECKS=FALSE;
  INSERT INTO headshot (player_id,target_id,created_at,timer) VALUES (13969,13,NOW(),0);
  SET @TRIGGER_CHECKS=TRUE;
  ```
* In tab A (not reloaded), click a writeup and confirm.
* Expected: "You already have access to the writeups of this target."
* Reload tab A and click a writeup. Expected: it opens without activating.
* Clean up:
  ```sql
  SET @TRIGGER_CHECKS=FALSE;
  DELETE FROM headshot WHERE player_id=13969 AND target_id=13;
  SET @TRIGGER_CHECKS=TRUE;
  ```

**2.5 No approved writeups**
* Reload tab A.
* Note which writeups are approved, so you can restore exactly those:
  ```sql
  SELECT id FROM writeup WHERE target_id=13 AND approved=1 AND status='OK';
  ```
* Unapprove them: `UPDATE writeup SET approved=0 WHERE target_id=13;`
* In tab A (not reloaded), click a writeup and confirm.
* Expected: "There are no writeups for this target."
* Clean up, using the ids you noted: `UPDATE writeup SET approved=1 WHERE id IN (<ids>);`

**2.6 No access to the target's network**
* Check that target 13 isn't already in a network: `SELECT * FROM network_target WHERE target_id=13;` If it returns a row, skip this step.
* Reload tab A.
* Put target 13 in a new private network:
  ```sql
  INSERT INTO network (name,public,active) VALUES ('writeup-test',0,1);
  INSERT INTO network_target (network_id,target_id,created_at,updated_at) SELECT id,13,NOW(),NOW() FROM network WHERE name='writeup-test';
  ```
* In tab A (not reloaded), click a writeup and confirm.
* Expected: "Failed to activate writeups for this target. You don't have access to this network."
* Clean up:
  ```sql
  DELETE FROM network_target WHERE target_id=13 AND network_id=(SELECT id FROM network WHERE name='writeup-test');
  DELETE FROM network WHERE name='writeup-test';
  ```

**2.7 No activation slots at all**
* `./backend/yii sysconfig/set plus_writeups 0`
* Reload tab A, click a writeup and confirm.
* Expected: "You have activated too many writeups, headshot some of the targets first."

## 3. Stuck check

**Start:** run the reset SQL once, then:

```
./backend/yii sysconfig/set plus_writeups 1
./backend/yii sysconfig/set writeup_stuck_hours 2
./backend/yii sysconfig/set writeup_cost_percent 0
./backend/yii sysconfig/set stream_player_target_help 0
./backend/yii sysconfig/set writeups_use_require_xp 0
```
```sql
UPDATE target SET required_xp=0 WHERE id=13;
UPDATE player_score SET points=10000 WHERE player_id=13969;
```

**3.1 Nothing claimed yet**
* Click a writeup and confirm.
* Expected: "You need to claim at least one flag or finding on this target before activating its writeups."

**3.2 Claimed too recently**
* Claim the finding: `INSERT INTO player_finding (player_id,finding_id) VALUES (13969,24);`
* Click a writeup and confirm.
* Expected: "You can activate writeups for this target 2 hours after your last flag or finding on it."

**3.3 Stuck long enough**
* Make the finding 3 hours old: `UPDATE player_finding SET ts=NOW()-INTERVAL 3 HOUR WHERE player_id=13969 AND finding_id=24;`
* Click a writeup and confirm.
* Expected: succeeds. PTH `cost=0`.

**3.4 The latest claim counts**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* Claim the **1000** flag.
* Click a writeup and confirm.
* Expected: "You can activate writeups for this target 2 hours after...", even though the finding is 3 hours old.

## 4. Paid activation

Do the steps **in order, without resetting in between**. Each step starts from where the previous one ended. Run the commands in the step first, then do what it says on target 13's page.

**Start:** run the reset SQL once, then:

```
./backend/yii sysconfig/set plus_writeups 1
./backend/yii sysconfig/set writeup_stuck_hours 0
./backend/yii sysconfig/set writeup_cost_percent 50
./backend/yii sysconfig/set stream_player_target_help 1
./backend/yii sysconfig/set writeups_use_require_xp 0
```
```sql
UPDATE target SET required_xp=0 WHERE id=13;
-- claim the finding, then set the score (in this order, the finding adds 100 points)
INSERT INTO player_finding (player_id,finding_id) VALUES (13969,24);
UPDATE player_score SET points=7050 WHERE player_id=13969;
```

Now the player has only the finding, **4,700** points are unclaimed, and the score is **7,050**.

**4.1 Dialog shows the cost**
* Click a writeup.
* Expected: the dialog says it costs **2,350 points (50%...)** and remaining flags keep full points. Click **Cancel**.

**4.2 Not enough points**
* `UPDATE player_score SET points=7049 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: "...costs 2,350 points. You need at least 7,050 points." No activation row, no stream row.

**4.3 Paid activation**
* `UPDATE player_score SET points=7050 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: "...for 2,350 points". PTH row has `cost=2350`. Stream row with `points=-2350`, shown as "...for -2350 points". Score = **4,700**.

**4.4 Full points after paying**
* Claim the **1500** flag.
* Expected: awarded the full **1,500**. Score = **6,200**.

**4.5 Cost follows what's unclaimed**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* `UPDATE player_score SET points=4800 WHERE player_id=13969;`
* Now 3,200 points are unclaimed (finding and 1500 flag claimed). Click a writeup and confirm.
* Expected: cost **1,600**. PTH `cost=1600`. Score = **3,200**.

**4.6 Different percent**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* `./backend/yii sysconfig/set writeup_cost_percent 25`
* `UPDATE player_score SET points=2400 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: the dialog and the charge both show **800**. Score = **1,600**.

**4.7 Stream logging off means legacy halving**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* `./backend/yii sysconfig/set writeup_cost_percent 50`
* `./backend/yii sysconfig/set stream_player_target_help 0`
* `UPDATE player_score SET points=10000 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: the dialog mentions the 50% reduction. PTH `cost=0`. No new stream row. Score stays **10,000**.

**4.8 Halving applies**
* Claim the **900** flag.
* Expected: awarded **450**.

**4.9 Free activation, logging on**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* `./backend/yii sysconfig/set writeup_cost_percent 0`
* `./backend/yii sysconfig/set stream_player_target_help 1`
* `UPDATE player_score SET points=10000 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: PTH `cost=0`. A new stream row with `points=0`. Score stays **10,000**.

**4.10 Free activation, logging off**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* `./backend/yii sysconfig/set stream_player_target_help 0`
* Click a writeup and confirm.
* Expected: PTH `cost=0`. No new stream row. Score stays **10,000**.

**4.11 Team score (optional, only if teams are enabled)**
* Run the reset SQL, then the whole **Start** block again.
* Put player 13969 in a team and approve them.
* Click a writeup and confirm.
* Expected: `team_stream` has a row with `points=-2350` and the team score drops by 2,350.

## 5. Required XP

Do the steps **in order, without resetting in between**. Nothing needs to be claimed in this section.

**Start:** run the reset SQL once, then:

```
./backend/yii sysconfig/set plus_writeups 1
./backend/yii sysconfig/set writeup_stuck_hours 0
./backend/yii sysconfig/set writeup_cost_percent 0
./backend/yii sysconfig/set stream_player_target_help 0
./backend/yii sysconfig/set writeups_use_require_xp 1
```
```sql
UPDATE target SET required_xp=10000 WHERE id=13;
```

**5.1 Below the required XP**
* `UPDATE player_score SET points=9999 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: "You need at least 10,000 points...". No activation row.

**5.2 At the required XP**
* `UPDATE player_score SET points=10000 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: succeeds. PTH `cost=0`.

**5.3 Setting off, XP ignored**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* `./backend/yii sysconfig/set writeups_use_require_xp 0`
* `UPDATE player_score SET points=9999 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: succeeds even though the score is below 10,000.

**5.4 Setting on, target has no required XP**
* `DELETE FROM player_target_help WHERE player_id=13969 AND target_id=13;`
* `./backend/yii sysconfig/set writeups_use_require_xp 1`
* `UPDATE target SET required_xp=0 WHERE id=13;`
* `UPDATE player_score SET points=0 WHERE player_id=13969;`
* Click a writeup and confirm.
* Expected: succeeds. No XP check.

## 6. Admins

Log in as the **admin** player (id `1`) instead of 13969. Do the steps **in order**.

**Start:** turn every check on, to show admins skip them, and clear the admin's progress on target 13:

```
./backend/yii sysconfig/set plus_writeups 0
./backend/yii sysconfig/set writeup_stuck_hours 2
./backend/yii sysconfig/set writeup_cost_percent 50
./backend/yii sysconfig/set stream_player_target_help 1
./backend/yii sysconfig/set writeups_use_require_xp 1
```
```sql
UPDATE target SET required_xp=10000 WHERE id=13;
DELETE FROM stream WHERE player_id=1 AND (
  (model='treasure' AND model_id IN (SELECT id FROM treasure WHERE target_id=13)) OR
  (model='finding' AND model_id IN (SELECT id FROM finding WHERE target_id=13)) OR
  (model='player_target_help' AND model_id=13));
DELETE FROM player_treasure WHERE player_id=1 AND treasure_id IN (SELECT id FROM treasure WHERE target_id=13);
DELETE FROM player_finding WHERE player_id=1 AND finding_id IN (SELECT id FROM finding WHERE target_id=13);
SET @TRIGGER_CHECKS=FALSE;
DELETE FROM headshot WHERE player_id=1 AND target_id=13;
SET @TRIGGER_CHECKS=TRUE;
DELETE FROM player_target_help WHERE player_id=1 AND target_id=13;
UPDATE player_score SET points=0 WHERE player_id=1;
```

**6.1 Admin skips the checks**
* Click a writeup and confirm.
* Expected: succeeds, even with nothing claimed, score 0 and no activation slots. PTH `cost=0`. Stream row with `points=0`. Score stays **0**.

**6.2 Admin gets the legacy halving**
* Claim the **900** flag as the admin.
* Expected: awarded **450**.

## 7. Legacy activation rows

Activations made before the migration have `cost=0`. This section checks them, and then simulates one on target 13 to confirm it keeps the halving even with paid mode on. Do the steps **in order**.

**Start:** run the reset SQL once, then:

```
./backend/yii sysconfig/set plus_writeups 1
./backend/yii sysconfig/set writeup_stuck_hours 0
./backend/yii sysconfig/set writeup_cost_percent 50
./backend/yii sysconfig/set stream_player_target_help 1
./backend/yii sysconfig/set writeups_use_require_xp 0
```
```sql
UPDATE target SET required_xp=0 WHERE id=13;
```

**7.1 Old rows got cost=0**
* Run:
  ```sql
  SELECT cost, count(*) FROM player_target_help
  WHERE created_at < FROM_UNIXTIME((SELECT apply_time FROM migration WHERE version='m261026_150000_add_cost_column_to_player_target_help_table'))
  GROUP BY cost;
  ```
* Expected: one line, `cost=0`. No rows at all is also fine (there were no activations before the migration).

**7.2 A legacy row keeps the halving**
* Create a legacy-style activation for the player:
  ```sql
  INSERT INTO player_target_help (player_id,target_id,cost,created_at) VALUES (13969,13,0,NOW());
  ```
* Claim the **900** flag.
* Expected: awarded **450**, even though `writeup_cost_percent` is 50.

## 8. Double click (optional)

Checks that two activations sent at the same moment only charge once.

**Start:** run the reset SQL once, then:

```
./backend/yii sysconfig/set plus_writeups 1
./backend/yii sysconfig/set writeup_stuck_hours 0
./backend/yii sysconfig/set writeup_cost_percent 50
./backend/yii sysconfig/set stream_player_target_help 1
./backend/yii sysconfig/set writeups_use_require_xp 0
```
```sql
UPDATE target SET required_xp=0 WHERE id=13;
INSERT INTO player_finding (player_id,finding_id) VALUES (13969,24);
UPDATE player_score SET points=7050 WHERE player_id=13969;
```

**8.1 Two activations at once**
* Open target 13 in two browser tabs.
* In each tab, click a writeup so the confirm dialog is showing.
* Click **OK** in both tabs as quickly as you can.
* Expected: one tab succeeds and the other shows an error. Exactly one PTH row with `cost=2350`, exactly one stream row with `points=-2350`, and score = **4,700**.
