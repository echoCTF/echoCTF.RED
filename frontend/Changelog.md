# Changelog

## v0.9 New Frontend
* [x] New layout integrated
* [x] Change the icons for new hints and new notifications
* [x] Add meta tags on pages that are going to be public
* [x] Replace activity stream icons with better ones from material or font-awesome
* [x] Add profile on sidebar
* [x] Fix player spins logic from pui
* [x] Add `accessRules` on all `Controllers` including their `verbs`
* [x] Propagate `$model->updateAttributes()` to all single attribute operations
* [x] Propagate `$model->updateCounters(['appears' => -11]);` for increment/decrement of counters on models (eg claim)
* [x] typcasting on models
* [x] Fix order criteria across all models and ensure indexes
* [x] Add twitter links on profile page
* [x] Add twitter links on target view
* [x] Convert login form to standard one (such as `signup.php`)
* [x] Fix CSS error messages
* [x] Ensure Player with 0 progress has target header
* [x] Add Player Hints on target

## 0.10
* [ ] Create target vs profile_id view
* [ ] Create avatar upload and/or external avatars inclusion
* [ ] Add Tutorial module with structure similar to challenges
