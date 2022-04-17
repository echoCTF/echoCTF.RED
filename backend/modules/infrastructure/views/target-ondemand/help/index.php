Target ondemand holds the targets that players power up manually in order to access them.

These targets are not running all the time and as such help in conserving server resources.

The fields include:
* **Target**: The target that we will activate ondemand powerup
* **Player**: The player that the powerup was initiated (we use the admin user by default eg ID:1)
* **State**: The state, the system assumes, the target is currently at (eg powered up, powered down)
* **Heartbeat**: When was the last recorded activity on the target. (After an hour of inactivity the system will shutdown automatically)
* **Created at / Updated at**: Record creation and last update date and time

