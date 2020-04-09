# Target commands
Manipulate and manage targets.

* `./yii target/cron $INTERVAL=5 $unit=MINUTE` Check for changes on the targets during the last INTERVAL units. This is what powers up/down targets based on the `scheduled_at`
* `./yii target/destroy target_id` Destroy a target container
* `./yii target/spin` Spin all targets
* `./yii target/pull` pull images
* `./yii target/spin-queue` Process the SpinQueue that holds requests from users
* `./yii target/docker-health-status` Check the status of containers running on the docker servers defined
* `./yii target/healthcheck` Check hosts for `unhealthy` status but do nothing.
* `./yii target/healthcheck 1` Check hosts for `unhealthy` status and RESTART them.
* `./yii target/pf` Update PF related targets.conf and rules needed for the findings (match-findings-pf.conf)
* `./yii target/pf 1` Update PF related targets.conf and rules and LOAD them
* `./yii target/restart` Restart a single target that is up for more than 24 hours
