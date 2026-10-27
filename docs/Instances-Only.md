# Instances only

echoCTF.RED can operate in an instances only mode, where on-demand targets are disabled and every player (not only VIP) can spawn private instances of the targets. The `spins_per_day` limits still apply to instance spawns and reboots.

The mode is enabled from the backend **Settings => Configure** by ticking **Instances only** under _Targets and Challenges properties_.

When enabled the following apply:

- On-demand targets cannot be powered up and their IP is hidden (`0.0.0.0`). Players can only reach them through a private instance
- Always-on targets keep their shared IP and can also be spawned as private instances
- The **Power up** and **Reboot target** actions are hidden and the spin endpoints refuse the requests
- All players can spawn, reboot and shut private instances without a subscription
- Spawns and reboots increase the player spin counter, shutting down an instance does not
- `cron/spin-queue` drops any queued spins for on-demand targets
- `cron/ondemand` shuts down any on-demand target that is powered up

**NOTE:** On-demand targets need `instance_allowed` enabled, otherwise they become unreachable. At least one server must exist under **Infrastructure => Servers**.

The option implies `disable_ondemand_operations`. Unlike `all_players_vip`, it only opens up private instances and does not grant any other VIP perks.

When disabled, on-demand targets come back powered off and non-VIP players lose the instance actions. Their already running instances expire normally or can be destroyed from **Infrastructure => Target Instances**.
