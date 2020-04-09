# Player commands (`backend/yii player/*`)
Perform player related operations (frontend users).


## List players (`backend/yii player/index`)

* `./backend/yii player` list players


## Mail players (`backend/yii player/mail`)

* `./backend/yii player/mail` Generate participant emails for account activation


## Register player (`backend/yii player/register`)

* `./backend/yii player/register` Register a player from the command line

Arguments:

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


 ```sh
 ./backend/yii player/register $username $email $fullname $password=false $player_type="offense" $active=false $academic=false $team_name=false $team_logo=false $team_id=false $baseIP="10.10.0.0" $skip=0
 ```
