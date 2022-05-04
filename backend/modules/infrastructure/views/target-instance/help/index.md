**Manage private target instances**

Players with proper permissions are allowed to spawn private instances of
existing targets. These pages allow you to manage these private instances.

The fields used are as following:
* **Player ID**: The player that owns this instance / the player that created
  the record. These instances are only accessible by the player who spawned them
* **Target ID**: The target that this instance will spawn
* **Server ID**: The server that this instance has been spawned on. When the
  record is initially created, the Server ID is `NULL`. It gets populated by
  the system that spawns the targets.
* **IP**: The IP address that this instance has been assigned. When the record
  is initially created, the IP is `NULL`. It gets populated after the instance
  has been spawned and IP has been assigned to the instance.
* **Reboot**: Reboot flag for the instance. The reboot flag takes the following
  values:
  * **`0`**: Do nothing (default)
  * **`1`**: Reboot system, the target gets restarted
  * **`2`**: Destroy system, the target gets destroyed and the record is deleted
* **Created/Updated at**: Record creation and last modification dates

**NOTE:** When a player requests a new target instance, a record is
created here, with `server_id`,`ip` set to `NULL`. Once the backend system
processes the request these values get populated with the instance details.
