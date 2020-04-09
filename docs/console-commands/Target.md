# Target commands
Manipulate and manage targets.

* Optional command line arguments are enclosed in `[]`
* Required command line arguments are enclosed in `<>`

## Cron command
Check for changes on the targets during the last INTERVAL units. This is what powers up/down targets based on the `scheduled_at`

Usage: `./yii target/cron [interval] [unit]`


## Destroy target containers
Destroy a running target container, this command only affects the container and not the database record

Usage: `./yii target/destroy <target_id>`


## Spin targets
Create and start or if already started destroy and start targets.

Usage: `./yii target/spin [target_id]`

If a numeric `target_id` is provided then the operations will be performed only the specified target.

## Pull target images
Pull images for the targets on their corresponding docker servers.

Usage: `./yii target/pull [target_id]`

If the target image already exists on the docker server then it will check with the registry that the image was originally downloaded and if hash is changed it will re-pull the image.

## Process Spin Queue
Process target spin queue as requested by players,

Usage: `./yii target/spin-queue`

## Check heath of target container
Check health status of running target containers and optionally restart
unhealthy ones.

Usage: `./yii target/healthcheck [spin]`


Check hosts for `unhealthy` status and RESTART them.

Usage: `./yii target/healthcheck 1`


## Update target related PF settings
Update PF `/etc/targets.conf` and `/etc/match-findings-pf.conf`

Usage: `./yii target/pf [load]`


## Restart targets
Restart the first target returned that is up and running for more than 24
hours. Good to clean zombie processes and other potential junk that may be
running on a target from attack attempts.

Usage: `./yii target/restart`
