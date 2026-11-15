Changes to player details, written by the database when a player is updated, no matter where the change came from (player settings, backend or console).

Tracked fields: `username`, `email`, `fullname`, `academic`, `status` and `type`.

* Filter on **Change** to find every player that ever used a given value, for example an old username.
* Entries are kept when the player is deleted.
* Entries cannot be created or edited, only deleted by admins.
