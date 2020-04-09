# User commands
Manipulate users that can access the backend interface.

* Optional command line arguments are enclosed in `[]`
* Required command line arguments are enclosed in `<>`


## List users
List users based on filter (default `all`)

Usage: `./backend/yii user/index [filter]`

Accepted filter values include one of `all`, `enabled`, `disabled`, `pending`.


## Find users
Find and list a given user based on name or email

Usage: `./backend/yii user/find <pattern>`


## Create users
Create a new backend user

Usage: `./backend/yii user/create <name> <email> [password]`


## Delete users
Delete a given user record, completely

Usage: `./backend/yii user/delete <email>`


## Set user deleted flag
Set deleted status for user, the record stays in the database

Usage: `./backend/yii user/deleted <email>`


## Set user disabled flag
Set user to disabled

Usage: `./backend/yii user/disable <email>`


## Set user enabled flag
Enable user

Usage: `./backend/yii user/enable <email>`


## Set user password
Set new password for user. If no `password` is supplied, then the command will
generate a random one.

Usage: `./backend/yii user/password <email> [password]`
