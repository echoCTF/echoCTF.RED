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

## badges($owner=0)
Update the badges for players that had activity during the past 26 hours

Usage: `./frontend/yii generator/badges [owner]`

* `owner` optional user id to change the ownership of the generated images

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
