# Player Status Transition Stages

## 1. The five status values

| Value | Constant            | Meaning                                                                                                      |
| ----- | ------------------- | ------------------------------------------------------------------------------------------------------------ |
| 0     | `STATUS_DELETED`    | Soft-delete flag: `active=0`, account unusable; the row may still exist                                      |
| 1     | `STATUS_SUSPENDED`  | Suspended: can authenticate, but every request is redirected to `/site/suspended`, which logs the player out |
| 8     | `STATUS_UNVERIFIED` | Email (re-)verification pending - set only when a player changes their email address                         |
| 9     | `STATUS_INACTIVE`   | Fresh registration, email not yet verified                                                                   |
| 10    | `STATUS_ACTIVE`     | Fully playable account                                                                                       |

A second flag `player.active` (tinyint) is toggled alongside status; login (`Player::findByUsername`) requires `active=1 AND status IN (10, 1)`.

## 2. Transitions

| #   | From → To                                                      | Trigger                                                                                                                                                                                                                                | Code                                                                                                                                    |
| --- | -------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | _(new)_ → **INACTIVE (9)**                                     | Signup with sysconfig `require_activation=true`: `active=0`, verification token generated (`player_token`, type `email_verification`), `emailVerify` (or `pendingApproval`) mail sent                                                  | `frontend/models/forms/SignupForm.php` `signup()`; default-status rule in `frontend/models/PlayerAR.php`                                |
| 2   | _(new)_ → **ACTIVE (10)**                                      | Signup with `require_activation=false`: `active=1`, no token, no mail                                                                                                                                                                  | same as #1                                                                                                                              |
| 3   | **INACTIVE (9) / UNVERIFIED (8)** → **ACTIVE (10)**            | Player clicks `site/verify-email?token=...`: sets `status=10, active=1`; if the old status was 9 it fires `NEW_PLAYER` (avatar, badge, signin IP)                                                                                      | `frontend/models/forms/VerifyEmailForm.php` `verifyEmail()`, invoked from `frontend/controllers/SiteController.php` `actionVerifyEmail` |
| 4   | **ACTIVE (10)** → **UNVERIFIED (8)**                           | Player changes their email in profile settings: new token + `emailChangeVerify` mail                                                                                                                                                   | `frontend/models/forms/SettingsForm.php` `save()`                                                                                       |
| 5   | **INACTIVE (9) / UNVERIFIED (8)** → _(unchanged)_              | "Resend verification email" (rate-limited per IP/email); template chosen by current status (`emailVerify` vs `emailChangeVerify`)                                                                                                      | `frontend/models/forms/ResendVerificationEmailForm.php`                                                                                 |
| 6   | **INACTIVE (9) / ACTIVE (10) / DELETED (0)** → **DELETED (0)** | Admin "Set Deleted": `status=0, active=0` (soft delete)                                                                                                                                                                                | `backend/modules/frontend/controllers/PlayerController.php` `actionSetDeleted`                                                          |
| 7   | _any_ → _row removed_                                          | Admin Delete / Delete filtered: real `ActiveRecord::delete()` - hard delete of the `player` row                                                                                                                                        | `PlayerController.php` `actionDelete`, `actionDeleteFiltered`                                                                           |
| 8   | _any_ → _archived + removed_                                   | Admin Ban / Ban filtered (`Player::ban()`): copies the row into the `banned_player` table with `banned_at`, then hard deletes it                                                                                                       | `backend/modules/frontend/models/Player.php` `ban()`                                                                                    |
| 9   | **INACTIVE (9)** → **ACTIVE (10)**                             | Admin "Activate" button: `status=10, active=1` (plus `approval=3` when approval mode is on)                                                                                                                                            | `PlayerController.php` `actionActivate`                                                                                                 |
| 10  | _any_ → **SUSPENDED (1)**                                      | **No code path in the repository sets this value.** It can only be written directly to the DB (e.g. hosted-platform moderation). The code only reacts to it: `BaseController` redirects to `site/suspended`, which logs the player out | `frontend/components/BaseController.php`, `SiteController.php` `actionSuspended`                                                        |
| 11  | _any_ → _archived + removed (automated)_                       | CLI `player/check-stopforumspam` bans players whose email matches StopForumSpam above the configured confidence → `ban()`                                                                                                              | `backend/commands/PlayerController.php` `actionCheckStopforumspam`                                                                      |

## 3. Automated transitions - MySQL event scheduler

Account cleanup is **not** done by the PHP cron (`backend/commands/CronController.php` never touches `player.status`). It is done by **MySQL events** defined in `schemas/echoCTF-events.sql`, which call stored procedures in `schemas/echoCTF-routines.sql`. The scheduler is toggled with `SET GLOBAL EVENT_SCHEDULER=ON/OFF` via `backend/commands/MaintenanceController.php` (`maintenance/start` / `maintenance/stop`).

**Event `player_maintenance` - runs EVERY 1 DAY → procedure `player_maintenance()`.** Each rule fires only if its sysconfig key is set (the value is an SQL interval, e.g. `10 DAY`):

| sysconfig key                                                          | SQL executed                                                                     | Transition                                           |
| ---------------------------------------------------------------------- | -------------------------------------------------------------------------------- | ---------------------------------------------------- |
| `player_delete_inactive_after`                                         | `DELETE FROM player WHERE status=9 AND ts < NOW() - INTERVAL {X}`                | **INACTIVE (9)** never verified → row removed        |
| `player_delete_rejected_after` (only if `player_require_approval > 0`) | `DELETE FROM player WHERE status=9 AND approval=4 AND ts < NOW() - INTERVAL {X}` | rejected registration (already mailed) → row removed |
| `player_changed_to_deleted_after`                                      | `UPDATE player SET status=0 WHERE status=8 AND ts < NOW() - INTERVAL {X}`        | **UNVERIFIED (8)** → **DELETED (0)** (soft)          |
| `player_delete_deleted_after`                                          | `DELETE FROM player WHERE status=0 AND ts < NOW() - INTERVAL {X}`                | **DELETED (0)** → row purged from the table          |

**Event `ev_player_token_expiration` - runs EVERY 10 SECONDS → procedure `expire_player_tokens()`:** deletes expired rows from `player_token` (verification, password-reset and API tokens). Once a verification token has expired, the account can no longer self-verify and is left for `player_maintenance` to handle.

Timed lifecycle of a non-active account: **9** (inactive signup, never verified) → row deleted after `player_delete_inactive_after`; **8** (email changed, never re-verified) → **0** after `player_changed_to_deleted_after`; the resulting **0** → row purged after `player_delete_deleted_after`.

`STATUS_SUSPENDED (1)` is outside the model's allowed validation range `[10, 8, 9, 0]` (`PlayerAR::rules()`), so it cannot be set through the admin edit form either - only by direct DB update, and no automated job ever sets it.

## 4. Approval sub-flag (only when sysconfig `player_require_approval=true`)

Runs _inside_ INACTIVE (9) until activation; stored in `player.approval`:

| Value | Meaning                       | Set by                                                     |
| ----- | ----------------------------- | ---------------------------------------------------------- |
| 0     | default after signup          | signup                                                     |
| 1     | admin approved                | `PlayerController::actionApproveFiltered`                  |
| 2     | approval/activation mail sent | `MailAction` (1→2)                                         |
| 3     | rejected / processed          | `actionReject` (also `actionActivate`, `actionSetDeleted`) |
| 4     | rejection mail sent           | `MailAction` (3→4)                                         |

A rejected account (status still 9, approval=4) is then hard-deleted by `player_maintenance` after `player_delete_rejected_after`.
