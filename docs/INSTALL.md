# OpenBSD General installation instructions of applications
The following steps outline the installation instructions for the applications hosted on this repository.

Keep in mind that you are advised to run `frontend` and `backend` from separate nginx instances, each with its own uid and chroot location.

**Note:** Although the interfaces are able to run on any system, the VPN server is assumed to run on OpenBSD.

Before we start make sure you have MariaDB, NGiNX or Apache, PHP, php-memcached, composer and memcahed running, this guide will not deal with these.

Also make sure you have installed the following UDFs [https://github.com/echoCTF/memcached_functions_mysql](https://github.com/echoCTF/memcached_functions_mysql) & [https://github.com/echoCTF/MySQL-global-user-variables-UDF](https://github.com/echoCTF/MySQL-global-user-variables-UDF)

Clone the repository
```sh
cd /var/www
git clone --depth 1 https://github.com/echoCTF/echoCTF.RED.git
cd echoCTF.RED
```

Create a database and import schema
```sh
mysql -e "CREATE DATABASE echoCTF CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql echoCTF<./schemas/echoCTF.sql
mysql echoCTF<./schemas/echoCTF-routines.sql
mysql echoCTF<./schemas/echoCTF-triggers.sql
mysql echoCTF<./schemas/echoCTF-events.sql
```

Copy the sample files and __edit__ the database name, database server, memcached and other relevant details.
```sh
cp backend/config/cache-local.php backend/config/cache.php
cp backend/config/validationKey-local.php backend/config/validationKey.php
cp backend/config/db-sample.php backend/config/db.php
cp frontend/config/memcached-local.php frontend/config/memcached.php
cp frontend/config/validationKey-local.php frontend/config/validationKey.php
cp frontend/config/db-local.php frontend/config/db.php
```

Install required composer files
```sh
composer install --no-dev --prefer-dist --no-progress --no-suggest -d backend/
composer install --no-dev --prefer-dist --no-progress --no-suggest -d frontend/
```

* Install the needed migrations
```sh
./backend/yii migrate --interactive=0
./backend/yii init_data --interactive=0
./backend/yii migrate-sales --interactive=0
./backend/yii template/emails --interactive=0
```

* The migrations for the live platform at [https://echoCTF.RED](https://echoCTF.RED) are stored here. You don't need to run this as it will most likely fail, however you can use it to store your own modifications so that you can always apply them on future updates.
```sh
./echoCTF.RED/backend/yii migrate-red --interactive=0
```

* Create assets folders and make sure they are writable by your webserver
```sh
mkdir -p backend/web/assets frontend/web/assets
chown www-data backend/web/assets frontend/web/assets
```

* Ensure runtime folder on both backend and fronend are also writable
```sh
chown www-data backend/runtime frontend/runtime
```

* Ensure your web server configuration for the frontend points to `/var/www/echoCTF.RED/frontend/web`
* Ensure your web server configuration for the backend points to `/var/www/echoCTF.RED/backend/web`

For Apache it could be the following:
```apache
    <VirtualHost *:80>
        ServerName frontend.test
        DocumentRoot "/var/www/echoCTF.RED/frontend/web/"

        <Directory "/var/www/echoCTF.RED/frontend/web/">
            # use mod_rewrite for pretty URL support
            RewriteEngine on
            # If a directory or a file exists, use the request directly
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            # Otherwise forward the request to index.php
            RewriteRule . index.php

            # use index.php as index file
            DirectoryIndex index.php

            # ...other settings...
            # Apache 2.4
            Require all granted

            ## Apache 2.2
            # Order allow,deny
            # Allow from all
        </Directory>
    </VirtualHost>

    <VirtualHost *:80>
        ServerName backend.test
        DocumentRoot "/var/www/echoCTF.RED/backend/web/"

        <Directory "/var/www/echoCTF.RED/backend/web/">
            # use mod_rewrite for pretty URL support
            RewriteEngine on
            # If a directory or a file exists, use the request directly
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            # Otherwise forward the request to index.php
            RewriteRule . index.php

            # use index.php as index file
            DirectoryIndex index.php

            # ...other settings...
            # Apache 2.4
            Require all granted

            ## Apache 2.2
            # Order allow,deny
            # Allow from all
        </Directory>
    </VirtualHost>
```

For nginx:
```nginx
    server {
        charset utf-8;
        client_max_body_size 128M;

        listen 80; ## listen for ipv4
        #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

        server_name frontend.test;
        root        /var/www/echoCTF.RED/frontend/web/;
        index       index.php;

#        access_log  /path/to/yii-application/log/frontend-access.log;
#        error_log   /path/to/yii-application/log/frontend-error.log;

        location / {
            # Redirect everything that isn't a real file to index.php
            try_files $uri $uri/ /index.php$is_args$args;
        }

        # uncomment to avoid processing of calls to non-existing static files by Yii
        #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
        #    try_files $uri =404;
        #}
        #error_page 404 /404.html;

        # deny accessing php files for the /assets directory
        location ~ ^/assets/.*\.php$ {
            deny all;
        }

        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_pass 127.0.0.1:9000;
            #fastcgi_pass unix:/var/run/php5-fpm.sock;
            try_files $uri =404;
        }

        location ~* /\. {
            deny all;
        }
    }

    server {
        charset utf-8;
        client_max_body_size 128M;

        listen 80; ## listen for ipv4
        #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

        server_name backend.test;
        root        /var/www/echoCTF.RED/backend/web/;
        index       index.php;

#        access_log  /path/to/yii-application/log/backend-access.log;
#        error_log   /path/to/yii-application/log/backend-error.log;

        location / {
            # Redirect everything that isn't a real file to index.php
            try_files $uri $uri/ /index.php$is_args$args;
        }

        # uncomment to avoid processing of calls to non-existing static files by Yii
        #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
        #    try_files $uri =404;
        #}
        #error_page 404 /404.html;

        # deny accessing php files for the /assets directory
        location ~ ^/assets/.*\.php$ {
            deny all;
        }

        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_pass 127.0.0.1:9000;
            #fastcgi_pass unix:/var/run/php5-fpm.sock;
            try_files $uri =404;
        }

        location ~* /\. {
            deny all;
        }
    }
```

Refer to the official Yii2 [guide](https://www.yiiframework.com/doc/guide/2.0/en/start-installation#configuring-web-servers) for more details on setting up your preferred webserver.
