# Target commands
Manipulate and manage targets.

* Optional command line arguments are enclosed in `[]`
* Required command line arguments are enclosed in `<>`

## Check heath of target container
Check health status of running target containers and optionally restart
unhealthy ones.

Usage: `./yii target/healthcheck [spin]`


Check hosts for `unhealthy` status and RESTART them.

Usage: `./yii target/healthcheck 1`

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


## Restart targets
Restart the first target returned that is up and running for more than 24
hours. Good to clean zombie processes and other potential junk that may be
running on a target from attack attempts.

Usage: `./yii target/restart`

## Destroy Instances
Destroy private instances of a target.

usage `./yii target/destroy-instances [target_id] [dopf]`

If `target_id` is provided then the specific target instances will be destroyed.
If `dopf` is provided then the operations will also include PF cleanups

## Destroy Ondemand
Destroy ondemand powered up targets.

usage `./yii target/destroy-ondemand [target_id] [dopf]`

If `target_id` is provided then the specific target will be destroyed.
If `dopf` is provided then the operations will also include PF cleanups.