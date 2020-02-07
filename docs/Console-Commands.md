# Console Commands

The user interfaces allow certain operations to be performed from the console in order to allow easy integration into shell scripts.

## moderators UI (mUI)
### Etc command
Manipulate `/etc/` related files.
* `php yii etc/pftables` Generate targets and participant related pf table files and also populate the tables
* `php yii etc/npppd-users` (not implemented) Sync players with npppd-users
* `php yii etc/bridge-rules` Generate bridge rules for when MAC authentication is enabled
* `php yii etc/openvpn` (obsolete) create participant OpenVPN configuration files and certificates

### Player command
Perform player related operations (frontend users).\
* `php yii player` list players
* `php yii player/mail` Generate participant emails for account activation
* `php yii player/register` Register a player from the command line
```
php yii player/register $username $email $fullname $password=false $player_type="offense" $active=false $academic=false $team_name=false $team_logo=false $team_id=false $baseIP="10.10.0.0" $skip=0
```
 - `username`: The username for the new player eg. `SuperDooper`
 - `email`: The email of the player eg. `sdooper@example.com`
 - `fullname`: Full name for the player eg. `"Super Dooper"`
 - `password`: Password for the user. If `0` is used then the system will generate a random password
 - `player_type`: The player type `offense` or `defense`
 - `active`: Register the user as active, `0=inactive, 1=active`
 - `academic`: Academic user flag `0=non academic, 1=academic`
 - `team_name`: Team name, to be created. The user will be owner of the team
 - `team_logo`: Team logo to use
 - `team_id`: Team id if the user is to be assigned to an existing team
 - `baseIP`: The base IP to generate ranges for users. (Can be ignored)
 - `skip`:  How many blocks to skip from generation. For our setups the first range (eg `10.10.0.1`) is assigned to the `tun(4)` interface used by openvpn so we use skip 1.


### SSL Command
Ssl related operations. Generate and manipulate a very simple PKI for OpenVPN authentication.
* `php yii ssl` Usage
* `php yii ssl/create-cert` Create and Sign certificate for Servers (openvpn, web servers etc)
* `php yii ssl/create-ca` Create a self sign certificate for a local CA
* `php yii ssl/player-certs` Generate certificates for the player and sign their keys with our CA

### Sysconfig command
Manipulate `sysconfig` key/val pairs.\
* `php yii sysconfig/profile` load a sysconfig profile with a predefined key/val pairs
* `php yii sysconfig/set` Set a key to the given value

### User command
Manipulate backend users.\
* `php yii user` List current users
* `php yii user/find` Find user
* `php yii user/create` Create a user
* `php yii user/delete` Delete a user
* `php yii user/deleted` Set deleted status for user
* `php yii user/disable` Set user to disabled
* `php yii user/enable` Enable user
* `php yii user/password` set user password


### Target command
Manipulate and manage targets.\
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
