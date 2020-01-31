# echoCTF Installation
The following steps outline the installation instructions for the applications hosted on this repository.

Keep in mind that you are advised to run `frontend` and `backend` from seperate nginx instances, each with its own uid and chroot location. Similarly the same logic must be followed when/if you're using `php-fpm`.

Before we start make sure you have MariaDB, NGiNX/Apache + PHP and composer up and running, this guide will not deal with these.

* cd /var/www
* git clone --depth 1 https://github.com/echoCTF/echoCTF.RED.git
* Create a database and import schema
```sh
mysqladmin create echoCTF
mysql echoCTF<./echoCTF.RED/schemas/echoCTF.sql
mysql echoCTF<./echoCTF.RED/schemas/echoCTF-routines.sql
mysql echoCTF<./echoCTF.RED/schemas/echoCTF-triggers.sql
mysql echoCTF<./echoCTF.RED/schemas/echoCTF-events.sql
```
* Copy and edit the destination files accordingly
```sh
cp echoCTF.RED/backend/config/cache-local.php echoCTF.RED/backend/config/cache.php
cp echoCTF.RED/backend/config/validationKey-local.php echoCTF.RED/backend/config/validationKey.php
cp echoCTF.RED/backend/config/db-sample.php echoCTF.RED/backend/config/db.php

cp echoCTF.RED/frontend/config/memcached-local.php echoCTF.RED/frontend/config/memcached.php
cp echoCTF.RED/frontend/config/validationKey-local.php echoCTF.RED/frontend/config/validationKey.php
cp echoCTF.RED/frontend/config/db-local.php echoCTF.RED/frontend/config/db.php
```
* Install required composer files
```sh
cd echoCTF.RED/backend
composer install
cd -
cd echoCTF.RED/frontend
composer install
```
* Install the needed migrations (the last command is mostly used by us to perform migrations for the live infrastructure)
```
./echoCTF.RED/backend/yii migration --interactive=0
./echoCTF.RED/backend/yii migration-red --interactive=0
```

* Ensure your nginx configuration for the frontend points to `echoCTF.RED/frontend/web`
* Ensure your nginx configuration for the backend points to `echoCTF.RED/backend/web`
