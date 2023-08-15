# Generator commands
The controller provides a set of commands that assist in various generations needed for the frontend.

## sitemap
Generate sitemap.xml for the platform based on the existing player, target,
faq, instructions and other settings.

Usage: `./frontend/yii generator/sitemap [profiles] [baseurl]`

* `profiles` is optional parameter, if present it activates profile links inclusion into the sitemap
* `baseurl` takes the base url for all the links that will be produced defaults to `https://echoctf.red/`

## avatar
(Re-)Generate robohash avatars for all players

Usage: `./frontend/yii generator/avatar`

## auth-keys
Populate players with empty `auth_key` field

Usage: `./frontend/yii generator/auth-keys`

## all-badges($owner=0)
Generate profile badge images for all active players.

Usage: `./frontend/yii generator/all-badges [owner]`
* `owner` optional user id to change the ownership of the generated images

## badges($owner=0,$interval=86400,$limit=200)
Update the badges for players that had activity during the past 24 hours

Usage: `./frontend/yii generator/badges [owner] [interval] [limit]`

* `owner` optional local user to change the ownership of the generated image files (default uid 0)
* `interval` optional time in seconds to check that the badges are older than (default older than 86400 seconds)
* `limit` limit the operation to this number of players (default 200)

## urls($domain)
Generate all available registered URL routes on the system for easier testing of the activated endpoints.

Usage: `./frontend/yii generator/urls domain`

* `domain` A domain to use by default to prefix the generated urls

## routes($outfile="routes.php")
Generate a local file with the URL routes for when the memcache becomes unavailable.

Usage: `./frontend/yii generator/routes outfile`

* `outfile` A file to store the routes into, defaults to `config/routes.php`


## disabled-routes($outfile="disabled-routes.php")
Generate a local file with the disabled URL routes for when the memcache becomes unavailable.

Usage: `./frontend/yii generator/disabled-routes outfile`

* `outfile` A file to store the routes into, defaults to `config/disabled-routes.php`

## player-disabled-routes($outfile="disabled-routes.php")
Generate a local file with the player specific disabled URL routes for when the memcache becomes unavailable.

Usage: `./frontend/yii generator/player-disabled-routes outfile`

* `outfile` A file to store the routes into, defaults to `config/player-disabled-routes.php`
