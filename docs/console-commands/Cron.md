# Cron commands


## Index
Performs all of the following operations
* `actionPowerups`
* `actionPowerdowns`
* `actionOfflines`
* `actionPf`
* `target/healthcheck 1`

## Powerups
Check for targets that have scheduled to power up


Usage: `./yii cron/powerups`


## Powerdowns
Check for targets that have scheduled to power down


Usage: `./yii cron/powerdowns`

## Offlines
Check for targets that have scheduled to go offline


Usage: `./yii cron/offlines`

## Update target related PF settings
Update PF `/etc/targets.conf` and `/etc/match-findings-pf.conf`

Usage: `./yii target/pf [load]`
