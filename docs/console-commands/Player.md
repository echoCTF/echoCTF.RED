# Player commands
Perform player related operations (frontend users).

* Optional command line arguments are enclosed in `[]`
* Required command line arguments are enclosed in `<>`


## List players

Usage: `./backend/yii player/index [filter]`

Accepted filter values include one of `all`, `active`, `inactive`


## Mail players
Generate and mail participant account activation URLs

Usage: `./backend/yii player/mail [baseURL] [active] [email]`

- `baseURL`: Base URL to use constructing the account activation url for the player (default: "https://echoctf.red/index.php?r=site/activate&key=")
- `active`:
## Register player

Usage: `./backend/yii player/register <username> <email> <fullname> [password] [player_type] [active] [academic] [team_name]` Register a player from the command line

- `username`: Unique username for the new player
- `email`: Unique email of the player
- `fullname`: Full name for the player
- `password`: Password for the user. Special value _`0`_ is used then the system will generate a random password
- `player_type`: The player type `offense` or `defense` (default: "offense")
- `active`: The registered users active status, `0=inactive, 1=active` (default: false)
- `academic`: Academic user flag `0=non academic, 1=academic` (default: false)
- `team_name`: Team name, to be created or join for the player (default: false)


## Change player password

Usage: `./backend/yii player/password <email or id> <password>`
