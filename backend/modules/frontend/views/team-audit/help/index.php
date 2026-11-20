Audit log of team changes, filled by the database when teams are created, updated or their membership changes.

The displayed fields include:
* `ID`: The record id
* `Team ID`: The team the entry belongs to
* `Team Name`: The name of the team
* `Player Username`: The player that produced the entry
* `Action`: The action performed, like join, approve or withdraw
* `Message`: Details about the action
* `Ts`: When the entry was recorded

The page provides the following actions:
* `Create Team Audit`: Add a new audit entry
* `Truncate`: Delete all the audit entries
