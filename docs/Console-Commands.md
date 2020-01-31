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
Perform player related operations
* `php yii player` list players
* `php yii player/mail` Generate participant emails for account activation
* `php yii player/register` Register a player from the command line
```
php yii player/register $username $email $fullname $password=false $player_type="offense" $active=false $academic=false $team_name=false $team_logo=false $team_id=false $baseIP="10.10.0.0" $skip=0
```
### Ssl Command
Ssl related operations. Generate and manipulate a very simple PKI for OpenVPN authentication.
* `php yii ssl` Usage
* `php yii ssl/create-cert` Create and Sign certificate for Servers (openvpn, web servers etc)
* `php yii ssl/create-ca` Create a self sign certificate for a local CA
* `php yii ssl/player-certs` Generate certificates for the player and sign their keys with our CA

### Sysconfig command
Manipulate `sysconfig` key/val pairs.
* `php yii sysconfig/profile` load a sysconfig profile with a predefined key/val pairs
* `php yii sysconfig/set` Set a key to the given value

### User command
Manipulate backend (mui) users.
* `php yii user` List current users
* `php yii user/find` Find user
* `php yii user/create` Create a user
* `php yii user/delete` Delete a user
* `php yii user/deleted` Set deleted status for user
* `php yii user/disable` Set user to disabled
* `php yii user/enable` Enable user
* `php yii user/password` set user password


