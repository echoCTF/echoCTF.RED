Manage the queue of targets waiting to be reset.

These records are created by the system when players request a target reset. Processing an entry moves it to the spin history.

The displayed fields include:
* `Target ID`: The id of the target
* `Target`: The id and name of the target
* `Player`: The username of the player that requested the reset
* `Created At`: When the reset was requested

The page provides the following actions:
* `Create Spin Queue`: Queue a target reset manually
* `Truncate`: Delete all entries from the queue
