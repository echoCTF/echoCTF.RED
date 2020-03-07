# echoCTF Installation
The following steps outline the installation instructions for the applications hosted on this repository.

Keep in mind that you are advised to run `frontend` and `backend` from seperate nginx instances, each with its own uid and chroot location. Similarly the same logic must be followed when/if you're using `php-fpm`.

Before we start make sure you have MariaDB, NGiNX/Apache + PHP, php-memcached, composer and MEMCACHED running, this guide will not deal with these.


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

* Copy the sample files and update the database name, database server, memcached and other relevant details.
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
* Install the needed migrations
```
./echoCTF.RED/backend/yii migrate --interactive=0
```

* The migrations for the live platform at https://echoCTF.RED are stored here. You don't need to run this as it will most likely fail.
```
./echoCTF.RED/backend/yii migrate-red --interactive=0
```

* Ensure your web server configuration for the frontend points to `echoCTF.RED/frontend/web`
* Ensure your web server configuration for the backend points to `echoCTF.RED/backend/web`

If you're using apache you are going to need something like the following for clean URLs to work.
```
RewriteEngine on
# If a directory or a file exists, use it directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
# Otherwise forward it to index.php
RewriteRule . index.php
```
