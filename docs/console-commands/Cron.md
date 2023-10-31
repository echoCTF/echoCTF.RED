# Cron commands


## Index
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

Usage: `./yii cron/instances [pfonly]`

## InstancePf
Process instance specific packet filter rules. This allows syncing of packet filter rules in setups with multiple VPN servers.

Provide a value for actions on instances that got modified during the last `N` seconds

Usage: `./yii cron/instance-pf [seconds]`

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
