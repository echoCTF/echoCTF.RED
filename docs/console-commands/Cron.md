# Cron commands

- [Cron commands](#cron-commands)
  - [Index (default operation)](#index-default-operation)
  - [PowerOperations](#poweroperations)
  - [Instances](#instances)
  - [InstancePf](#instancepf)
  - [Healthcheck](#healthcheck)
  - [SpinQueue](#spinqueue)
  - [Powerups](#powerups)
  - [Powerdowns](#powerdowns)
  - [Ondemand](#ondemand)
  - [Offlines](#offlines)
  - [Update target related PF settings](#update-target-related-pf-settings)
  - [Generate and load Instance tables PF rules](#generate-and-load-instance-tables-pf-rules)

## Index (default operation)

Performs all of the following operations
* `actionSpinQueue`
* `actionOndemand`
* `actionPf`

## PowerOperations
Performs the following operations
* `actionPowerups`
* `actionPowerdowns`
* `actionOfflines`

## Instances
Processes target instances with pending actions. This includes instances that are scheduled to be started (spawn), restarted or destroyed:

Providing any argument causes the action to process only PF related operations for the instance.

Parameters:
- `pfonly` process only PF related operations (default: `0`)
- `expired_ago` seconds since last update after which an instance is considered expired (default: `2400`)
- `atOnce` process at most this many instances per run, `0` for no limit (default: `0`)
- `server_ids` optional comma separated list of server ids to limit the run to, omit for all servers

Usage: `./yii cron/instances [pfonly] [expired_ago] [atOnce] [server_ids]`

Example, process only instances on servers 3, 7 and 12: `./yii cron/instances 0 2400 0 3,7,12`

Without `server_ids` the lock file is `/tmp/cron-instances.lock`. Each server list gets its own lock file (eg. `/tmp/cron-instances-3_7_12.lock`) so several lists can run in parallel. Note that PF table syncing at the end of each run still covers all instances.

## InstancePf
Process instance specific packet filter rules. This allows syncing of packet filter rules in setups with multiple VPN servers.

Provide a value for actions on instances that got modified during the last `N` seconds

Usage: `./yii cron/instance-pf [seconds]`

Only instances of players connected to this server's OpenVPN networks (as registered with `vpn/load`), or of their team mates when the instance is team visible, are processed. If no networks are registered for this server all players are processed.

## Healthcheck
Checks the healthstatus of running containers and (optionaly) restart them if found unhealthy.

Usage:
```sh
./yii cron/healthcheck
# or to request restarting of unhealthy containers
./yii cron/healthcheck 1
```

## SpinQueue
Process the target spin queue and restart listed targets.

Usage: `./yii cron/spin-queue`


## Powerups
Check for targets that have been scheduled to power up and process them


Usage: `./yii cron/powerups`


## Powerdowns
Check for targets that have scheduled to power down


Usage: `./yii cron/powerdowns`


## Ondemand
Process ondemand targets that are scheduled for start/destroy

Usage: `./yii cron/ondemand`


## Offlines
Check for targets that have scheduled to go offline

Usage: `./yii cron/offlines`


## Update target related PF settings
Update PF `/etc/targets.conf`, `/etc/match-findings-pf.conf` among other things. This action syncs the firewall ruleset with the decisions of the database (eg private instances, private networks, target access etc)

Usage: `./yii target/pf [load]`

For private networks with running targets, it also creates the pass rules between the network and its players.

The network name is used as a PF table name. If it has characters other than letters, numbers and `_`, or if `<name>_clients` is longer than 31 characters, the network is skipped and you will see:

```
PF: skipping private network [name], not a valid pf table name
```

Names created by the frontend (`player_product_<id>`) are always fine.

## Generate and load Instance tables PF rules
Generate and (optionaly) load pf tables related to instances. This operation is run on the VPN to keep the PF tables in sync.

Usage: `./yii cron/instance-pf-tables [load]`

Only acts on players connected to this server's OpenVPN networks (as registered with `vpn/load`). If no networks are registered for this server all players are processed.
