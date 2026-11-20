Manage the ban list of player emails kept off the platform.

Emails are matched with SQL LIKE, so a single entry can ban a whole domain.

The displayed fields include:
* `ID`: The record id
* `Old ID`: The old player id for this ban
* `Username`: The username of the banned player
* `Email`: The banned email address or wildcard pattern
* `Registered At`: When the player account was registered

The page provides the following actions:
* `Create Banned Player`: Add a new entry to the ban list
* `Optimize Banned Players`: Remove entries whose email matches a pattern from another entry
