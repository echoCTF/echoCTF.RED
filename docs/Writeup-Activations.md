# Writeup Activations

Players who fully solve a target (a **headshot**) can submit a writeup for it. Players who haven't solved it can **activate** the target's writeups to read them, at a price. This page explains the rules, the sysconfig keys that control them, and what happens in the database.

## Quick reference

| Key | Type | Default | Effect |
|---|---|---|---|
| `writeup_stuck_hours` | integer | unset | Hours since the player's last own flag or finding on the target before activation is allowed. Unset or `0` disables the check. |
| `writeup_cost_percent` | integer, 0 to 100 | unset | Percent of the player's unclaimed points on the target charged on activation. Unset or `0` keeps the legacy 50% halving instead. |
| `writeups_use_require_xp` | flag | off | When on, the player's score must be at least the target's `required_xp` to activate. |
| `plus_writeups` | integer | unset | Activations allowed on top of the player's headshot count. |
| `stream_player_target_help` | flag | off | Log activations (free and paid) on the activity stream. |

You can set all of them from the backend under **Settings → Configure → Targets and Challenges properties**.

With `writeup_stuck_hours` and `writeup_cost_percent` both unset, the platform behaves exactly as it did before these were introduced.

## Writing writeups

* The target must have `writeup_allowed` set.
* The player must have a headshot on the target.
* One writeup per player per target.
* New writeups start as `PENDING` and must be approved by staff. Editing an approved writeup sends it back to `PENDING`.

## Reading writeups

A player can read a target's approved writeups if they either:

* have a headshot on the target (always free, no activation needed), or
* have activated writeups for the target (a row in `player_target_help`).

## Activating writeups

The checks run in this order. The first one that fails is shown to the player as an error.

1. **Not already activated** for this target.
2. **No headshot** on the target. Finishers already have access, so they can't use up an activation slot.
3. **The target has at least one approved writeup.**
4. **Admins stop here.** Everything below is skipped for admins, and their activation is always free (`cost=0`), which means it uses the legacy halving.
5. **Network access.** If the target belongs to a network, the network must be active, and the player must have access to it unless it's public.
6. **Activation cap.** Active activations must be fewer than `headshots + plus_writeups`.
7. **Required XP** (only with `writeups_use_require_xp` on). If the target has a `required_xp` above 0, the player's score must be at least that.
8. **Stuck check** (only with `writeup_stuck_hours` set). The player must have at least one of their own treasures or findings on the target, and the most recent one must be older than `writeup_stuck_hours`.
9. **Affordability** (only with `writeup_cost_percent` set). The player's score must be at least **3× the cost**. This guarantees at least 2× the cost remains after paying.

### How the cost is calculated

```
unclaimed = sum of points of the target's treasures and findings the player hasn't claimed
cost      = ceil(unclaimed × writeup_cost_percent / 100)
```

The cost is rounded up, so a paid activation never ends up with `cost=0`.

**Example** with `writeup_cost_percent=50`: a target has 1,000 unclaimed points for the player. The cost is 500, the player needs a score of at least 1,500, and ends up with at least 1,000.

The confirmation dialog in the frontend shows the player the exact cost before they confirm.

## Paid vs legacy activations

Every activation is stored in `player_target_help` with a `cost` column, and that value decides how the rest of the target is scored.

| | Legacy (`cost = 0`) | Paid (`cost > 0`) |
|---|---|---|
| When | `writeup_cost_percent` unset, admin activations, and all activations made before the migration | `writeup_cost_percent` set |
| Up-front charge | none | `cost` points |
| Later flags and findings | worth **50%** | worth **full points** |
| Stream entry | only if `stream_player_target_help` is on, 0 points | only if `stream_player_target_help` is on, `-cost` points |

Changing `writeup_cost_percent` only affects new activations. Existing rows keep their `cost`, so players who activated under the old rules keep the halving.

## Under the hood

* **Migrations.** `m261026_150000_add_cost_column_to_player_target_help_table` adds the `cost` column (default `0`). `m261026_150100_update_writeup_activation_triggers_for_cost` updates the triggers below. Apply them with `./backend/yii migrate`.
* **Halving.** The `tbi_player_treasure` and `tbi_player_finding` triggers halve points only when a `player_target_help` row with `cost=0` exists for that player and target.
* **Charging.** The `tai_player_target_help` trigger handles the charge, and how depends on `stream_player_target_help`:
  * **On:** it inserts a `stream` row with `points = -cost`. The existing `tai_stream` trigger deducts it from `player_score` and copies it to `team_stream`, so team scores follow too.
  * **Off:** no stream row. It deducts `cost` directly from `player_score` and, if the player is in an approved team, from `team_score`.
* **Migrations (stream toggle).** `m261026_160000_update_trigger_after_insert_on_player_target_help_stream_toggle` makes `stream_player_target_help` apply to paid activations too.
* **Concurrency.** The frontend locks the player's `player_score` row (`SELECT … FOR UPDATE`) and recalculates the cost inside the transaction. Two simultaneous activations can't both pass the affordability check.
* **Code.** The rules live in `frontend/modules/target/models/WriteupActivation.php`. `WriteupController::actionEnable()` and the writeups list (`_target_writeups.php`) both use it.

## Things to know

* **No refunds.** Deleting a paid `player_target_help` row from the backend doesn't give the points back. If you need to refund, remove the matching `player_target_help` stream entry (when stream logging was on), or add a positive one.
* **Charges made with stream logging off aren't in the stream.** Anything that rebuilds scores from `stream` or `team_stream` (for example `repopulate_team_stream`) won't include them, so those players and teams get the points back after a rebuild. Keep `stream_player_target_help` on if you rebuild scores.
* **Deleting an activation re-enables full points** for any flags claimed afterwards, since the halving trigger looks the row up at claim time. Flags already claimed keep the points they were awarded.
* **`required_xp` is compared against the raw score** in `player_score.points`, which is also what player experience levels are based on.
* **Time zones.** The stuck check is compared in SQL against `NOW()`, so it follows the database time zone (`time_zone` sysconfig key), not PHP's.
