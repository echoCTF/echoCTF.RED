# Sysconfig Keys
System configuration table (Sysconfig) keys and checks
## Values
* `footer_logos`: HTML code to display logos for the event on the footer of the page. (`themes/bootstrap/views/layout/main.php`)
* `offense_scenario` / `defense_scenario`: Offense and  Defense scenario texts
* `event_name`: The event name for this CTF (`themes/bootstrap/views/site/index.php`)
* `offense_vether_network`: OpenBSD Virtual Ethernet adapter network address offense (`protected/components/WebUser.php`)
* `offense_vether_netmask`: OpenBSD Virtual Ethernet adapter network mask offense (`protected/components/WebUser.php`)
* `defense_vether_network`: OpenBSD Virtual Ethernet adapter network address for defense (`protected/components/WebUser.php`)
* `defense_vether_netmask`: OpenBSD Virtual Ethernet adapter network mask for defense (`protected/components/WebUser.php`)
* `challenge_home`: Base path where the challenge files will be stored. (`protected/controllers/ChallengeController.php`)
* `award_points`: How the points will be awarded to users (full, divider, single)
* `blue_home`: The directory the defense UI files reside
* `CTF_end_ts`: A timestamp which denotes the end of the CTF competition (NOT USED YET)
* `CTF_start_ts`: A timestamp which denotes the start of the CTF competition (NOT USED YET)
* `defense_bridge_if`: The bridge interface for the defense network
(`mui/htdocs/protected/commands/EtcCommand.php`)
* `defense_bridge_if_rules`: (NOT USED??)
* `defense_domain`: The domain name for the defense players UI
(`mui/htdocs/protected/modules/settings/views/default/gwdhcp.php`)
* `defense_eth_if`: The ethernet interface connected to the defense network
(`mui/htdocs/protected/commands/EtcCommand.php`)
* `defense_pfauth_anchor`: (NOT USED YET)
* `defense_pfauth_table`: (NOT USED YET)
* `defense_registered_tag`: A tag for registered defense players, used for packet filtering
* `defense_vether_if`: 
* `moderators_domain`: The domain name for the moderators UI access (NOT USED YET)
* `moderators_gw`: Gateway for the moderators network
* `moderators_if`: The ethernet interface for moderators network
* `moderators_netmask`: The netmask of the moderators network
* `moderators_network`: The moderators network address
* `moderators_rdomain`: The moderators routing domain
* `mods_home`: The directory the moderators UI files reside
* `offense_bridge_if`: The bridge interface for the offense network
(`mui/htdocs/protected/commands/EtcCommand.php`)
* `offense_bridge_if_rules`: (NOT USED YET)
* `offense_domain`: The domain name for the offense players UI
* `offense_eth_if`: The ethernet interface connected to the offense network
(`mui/htdocs/protected/commands/EtcCommand.php`)
* `offense_pfauth_anchor`: (NOT USED YET)
* `offense_pfauth_table`: (NOT USED YET)
* `offense_registered_tag`: A tag for registered offense players, used for packet filtering
* `offense_vether_if`:
* `red_home`: The directory the offense UI files reside (NOT USED YET?)

## Boolean Keys
### event_active
Sets the event status
* `true` everything works
* `false`
  - pui disable flags menu altogether
  -￼ pui disable report creation
  - pui disable user registration
  -￼ pui disable challenge view, download and answer
  - mui commands/EtcController.php flushes targets, offense_activated & defense_activated tables


### disable_registration
When enabled it disables the online registration by users. This is for competitions with pre-registered participation.

* `true`
  - site/register disabled (protected/controllers/SiteController.php)
* `false`
  - site/login.php shows register button (themes/bootstrap/views/site/login.php)

### strict_activation
Enables strict activation checks. When enabled, account activation can only take place from the IP address allocated to it (eg user->ip == Request->srcip)

Checks
* `true`
  - site/activate forces check of user_ip == user_id (protected/controllers/SiteController.php)
* `false`

### require_activation
When enabled players need to activate their accounts before logging in.

* `true`
  - `register()` method sets user->active=false (protected/controllers/SiteController.php)
* `false`
  - `register()` method sets user->active=true (protected/models/RegisterForm.php)
  - `site/activate` disabled (protected/controllers/SiteController.php)


### player_profile
Enables player personal profiles.

Files
1. protected/controllers/TeamController.php
1. themes/bootstrap/views/layouts/main.php

Checks
* `true`
  - layouts/main.php shows team/player link
* `false`
  - team/player disabled (`TeamController.php`)

### trust_user_ip
When enabled it trusts the user ip and uses it as the ID of the player. This is useful for pre-registrations with static IP addresses (eg OpenVPN, npppd etc).

* `true`
  - `register()` method sets user->id into user->ip (protected/models/RegisterForm.php)

### join_team_with_token
Enables the support to join team by token.

* `true`
  - `register()` method auto joins member to the given token (protected/models/RegisterForm.php)
  - `site/register.php` shows token field (themes/bootstrap/views/site/register.php)

### teams
Enable team support for the setup. When disabled every player is his/her own team.

Files
1. protected/controllers/TeamController.php
1. protected/controllers/SiteController.php
1. protected/models/ClaimTreasureForm.php
1. themes/bootstrap/views/layouts/main.php
1. protected/models/RegisterForm.php
1. themes/bootstrap/views/instructions/index.php

Checks
* `true`
  - site/register redirects user to team/index
  - ClaimTreasureForm.php checks that team based unique claim is allowed
  - layout/main.php shows team management link


* `false`
  - team/join disabled (`TeamController.php`)
  - team/create disabled (`TeamController.php`)
  - team/index disabled (`TeamController.php`)
  - team/cancel disabled (`TeamController.php`)
  - RegisterForm.php/register() method create a team with player data

### team_manage_members
* `true` allow team management operations join,cancel,create
* `false` disable team management operations join,cancel,create

### mac_auth
Enables MAC address based identification of the user generated traffic.

* `true`
  - login() method checks and adds PlayerMac (protected/models/LoginForm.php)
* `false`
  - `login()` method checks and adds PlayerIp (protected/models/LoginForm.php)
